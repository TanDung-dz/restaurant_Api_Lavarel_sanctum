<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDatMon;
use App\Models\ThanhToan;
use App\Models\ThongTinDatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ChiTietDatMonController;
use App\Services\MoMoPaymentService;

class ThanhToanController extends Controller
{
    protected $momoService;

    public function __construct(MoMoPaymentService $momoService)
    {
        $this->momoService = $momoService;
    }

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
            'PhuongThucThanhToan' => 'required|integer|in:1,2,3,4', // 1: Tiền mặt, 2: Thẻ, 3: Chuyển khoản, 4: MoMo
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

        // Trạng thái thanh toán mặc định (đã thanh toán cho phương thức 1, 2, 3)
        $trangThaiThanhToan = 1;
        
        // Nếu là thanh toán qua MoMo thì ban đầu đặt là chưa thanh toán
        if ($request->PhuongThucThanhToan == 4) {
            $trangThaiThanhToan = 0;
        }

        $thanhToan = ThanhToan::create([
            'ID_ThanhToan' => $nextId,
            'ID_ThongTinDatBan' => $request->ID_ThongTinDatBan,
            'SoLuong' => $tongTien,
            'PhuongThucThanhToan' => $request->PhuongThucThanhToan,
            'TrangThaiThanhToan' => $trangThaiThanhToan,
            'NgayThanhToan' => $trangThaiThanhToan == 1 ? now() : null,
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
            'PhuongThucThanhToan' => 'sometimes|required|integer|in:1,2,3,4',
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

    /**
     * Tạo thanh toán qua MoMo
     */
    /**
 * Tạo thanh toán qua MoMo
 */
public function createMoMoPayment(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('MoMo Payment Request', $request->all());

        
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
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
            'PhuongThucThanhToan' => 4, // 4: MoMo
            'TrangThaiThanhToan' => 0, // 0: Chưa thanh toán
            'NgayTao' => now(),
            'NgayCapNhap' => now(),
        ]);

                // Trước khi gọi momoService->createPayment
        \Illuminate\Support\Facades\Log::info('MoMo Payment Data', [
            'ID_ThanhToan' => $thanhToan->ID_ThanhToan,
            'ID_ThongTinDatBan' => $thanhToan->ID_ThongTinDatBan,
            'SoLuong' => $thanhToan->SoLuong
        ]);

        // Tạo thanh toán MoMo
        $result = $this->momoService->createPayment($thanhToan);

        // Log kết quả
        \Illuminate\Support\Facades\Log::info('MoMo Payment Result', $result);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo thanh toán MoMo thành công',
                'data' => [
                    'id_thanh_toan' => $thanhToan->ID_ThanhToan,
                    'payment_url' => $result['paymentUrl']
                ]
            ], 201);
        }

        // Nếu có lỗi, xóa thanh toán đã tạo
        $thanhToan->delete();

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Lỗi khi tạo thanh toán MoMo'
        ], 400);


        \Illuminate\Support\Facades\Log::info('MoMo Payment Result', $result);
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus(Request $request, $id)
    {
        $thanhToan = ThanhToan::with('thongTinDatBan')
            ->find($id);
        
        if (!$thanhToan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin trạng thái thanh toán',
            'data' => [
                'id_thanh_toan' => $thanhToan->ID_ThanhToan,
                'id_thongtin_datban' => $thanhToan->ID_ThongTinDatBan,
                'so_luong' => $thanhToan->SoLuong,
                'phuong_thuc_thanh_toan' => $thanhToan->PhuongThucThanhToan,
                'trang_thai_thanh_toan' => $thanhToan->TrangThaiThanhToan,
                'ngay_thanh_toan' => $thanhToan->NgayThanhToan,
                'ma_giao_dich' => $thanhToan->MaGiaoDich,
                'momo_result_code' => $thanhToan->MoMo_ResultCode,
                'momo_message' => $thanhToan->MoMo_Message
            ]
        ]);
    }
}