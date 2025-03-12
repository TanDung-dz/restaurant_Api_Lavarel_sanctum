<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDatMon;
use App\Models\ThanhToan;
use App\Models\ThongTinDatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ChiTietDatMonController;

class ThanhToanController extends Controller
{
    /**
     * Lấy thông tin thanh toán
     */
    public function index(Request $request)
    {
        $query = ThanhToan::with('thongTinDatBan');
        
        // Lọc theo id đặt bàn
        if ($request->has('id_thongtin_datban')) {
            $query->where('ID_ThongTinDatBan', $request->id_thongtin_datban);
        }
        
        // Lọc theo trạng thái thanh toán
        if ($request->has('trang_thai')) {
            $query->where('TrangThaiThanhToan', $request->trang_thai);
        }
        
        $thanhToans = $query->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách thanh toán',
            'data' => $thanhToans
        ]);
    }

    /**
     * Tạo thanh toán mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
            'PhuongThucThanhToan' => 'required|integer|in:1,2,3', // 1: Tiền mặt, 2: Thẻ, 3: Chuyển khoản
            'MaGiaoDich' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $thongTinDatBan = ThongTinDatBan::find($request->ID_ThongTinDatBan);
        
        // Kiểm tra trạng thái đặt bàn
        if ($thongTinDatBan->TrangThai != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn chưa được xác nhận hoặc đã bị hủy'
            ], 400);
        }

        // Kiểm tra đã có thanh toán nào chưa
        $existingPayment = ThanhToan::where('ID_ThongTinDatBan', $request->ID_ThongTinDatBan)
                            ->where('TrangThaiThanhToan', 1)
                            ->first();
        
        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn này đã được thanh toán'
            ], 400);
        }

        // Tính tổng tiền
        $chiTietDatMonController = new ChiTietDatMonController();
        $tongTien = $chiTietDatMonController->calculateTotal($request->ID_ThongTinDatBan);
        
        if ($tongTien <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn không có món ăn, không thể thanh toán'
            ], 400);
        }

        // Tìm ID lớn nhất và tăng thêm 1
        $maxId = ThanhToan::max('ID_ThanhToan') ?? 0;
        $nextId = $maxId + 1;

        $thanhToan = ThanhToan::create([
            'ID_ThanhToan' => $nextId,
            'ID_ThongTinDatBan' => $request->ID_ThongTinDatBan,
            'SoLuong' => $tongTien,
            'PhuongThucThanhToan' => $request->PhuongThucThanhToan,
            'TrangThaiThanhToan' => 1, // 1: Đã thanh toán
            'NgayThanhToan' => now(),
            'MaGiaoDich' => $request->MaGiaoDich,
            'NgayTao' => now(),
            'NgayCapNhap' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công',
            'data' => $thanhToan
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết thanh toán
     */
    public function show($id)
    {
        $thanhToan = ThanhToan::with('thongTinDatBan.chiTietDatMons.monAn')
            ->find($id);
        
        if (!$thanhToan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin thanh toán',
            'data' => $thanhToan
        ]);
    }

    /**
     * Cập nhật thông tin thanh toán
     */
    public function update(Request $request, $id)
    {
        $thanhToan = ThanhToan::find($id);
        
        if (!$thanhToan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'PhuongThucThanhToan' => 'sometimes|required|integer|in:1,2,3',
            'TrangThaiThanhToan' => 'sometimes|required|integer|in:0,1', // 0: Chưa thanh toán, 1: Đã thanh toán
            'MaGiaoDich' => 'sometimes|nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $dataToUpdate = $request->only(['PhuongThucThanhToan', 'TrangThaiThanhToan', 'MaGiaoDich']);
        $dataToUpdate['NgayCapNhap'] = now();
        
        // Nếu đổi trạng thái từ chưa thanh toán sang đã thanh toán
        if (!$thanhToan->TrangThaiThanhToan && $request->input('TrangThaiThanhToan', 0) == 1) {
            $dataToUpdate['NgayThanhToan'] = now();
        }

        $thanhToan->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thanh toán thành công',
            'data' => $thanhToan
        ]);
    }

    /**
     * Hủy thanh toán
     */
    public function destroy($id)
    {
        $thanhToan = ThanhToan::find($id);
        
        if (!$thanhToan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ], 404);
        }

        // Thay vì xóa, ta set TrangThaiThanhToan = 0 (chưa thanh toán)
        $thanhToan->update([
            'TrangThaiThanhToan' => 0,
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hủy thanh toán thành công'
        ]);
    }

    /**
 * Gửi xác nhận thanh toán và thông báo
 */
public function sendConfirmation($id)
{
    $thanhToan = ThanhToan::find($id);
    
    if (!$thanhToan) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy thông tin thanh toán'
        ], 404);
    }

    // Gửi thông báo trong hệ thống
    $thongBaoController = new ThongBaoController();
    $thongBao = $thongBaoController->sendPaymentConfirmation($id);

    // Gửi email xác nhận
    $emailService = new \App\Services\EmailService();
    $emailSent = $emailService->sendPaymentConfirmation($id);

    return response()->json([
        'success' => true,
        'message' => 'Gửi xác nhận thanh toán thành công',
        'notification_sent' => $thongBao ? true : false,
        'email_sent' => $emailSent
    ]);
}
}