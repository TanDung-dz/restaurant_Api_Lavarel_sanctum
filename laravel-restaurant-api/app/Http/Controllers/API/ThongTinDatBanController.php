<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\BangChiTietDatBan;
use App\Models\ThongTinDatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ThongTinDatBanController extends Controller
{
    /**
     * Lấy danh sách đặt bàn
     */
    public function index(Request $request)
    {
        $query = ThongTinDatBan::with(['user', 'chiTietDatBans.ban.khuVuc']);
        
        // Lọc theo user
        if ($request->has('id_user')) {
            $query->where('ID_USER', $request->id_user);
        }
        
        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }
        
        // Lọc theo ngày
        if ($request->has('ngay_dat')) {
            $query->whereDate('ThoiGianDatBan', $request->ngay_dat);
        }
        
        $datBans = $query->orderBy('ThoiGianDatBan', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách đặt bàn',
            'data' => $datBans
        ]);
    }

    /**
     * Tạo đặt bàn mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ThoiGianDatBan' => 'required|date_format:Y-m-d H:i:s|after:now',
            'SoLuongKhach' => 'required|integer|min:1',
            'YeuCau' => 'nullable|string|max:255',
            'ID_Ban' => 'required|exists:Ban,ID_Ban'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra bàn có sẵn không
        $banId = $request->ID_Ban;
        $thoiGianDatBan = $request->ThoiGianDatBan;
        
        // Lấy thông tin bàn để kiểm tra dung tích
        $ban = Ban::find($banId);
        if ($ban->DungTich < $request->SoLuongKhach) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn không đủ chỗ cho số lượng khách'
            ], 400);
        }
        
        // Kiểm tra bàn đã được đặt vào thời điểm đó chưa
        $isBooked = BangChiTietDatBan::whereHas('thongTinDatBan', function($query) use ($thoiGianDatBan) {
            $query->whereDate('ThoiGianDatBan', date('Y-m-d', strtotime($thoiGianDatBan)))
                  ->where('TrangThai', 1); // Chỉ kiểm tra các đơn đã xác nhận
        })->where('ID_Ban', $banId)->exists();
        
        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn đã được đặt vào thời điểm này'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            // Tìm ID lớn nhất và tăng thêm 1
            $maxId = ThongTinDatBan::max('ID_ThongTinDatBan') ?? 0;
            $nextId = $maxId + 1;
            
            // Tạo thông tin đặt bàn
            $thongTinDatBan = ThongTinDatBan::create([
                'ID_ThongTinDatBan' => $nextId,
                'ID_USER' => $request->user()->ID_USER,
                'ThoiGianDatBan' => $thoiGianDatBan,
                'SoLuongKhach' => $request->SoLuongKhach,
                'YeuCau' => $request->YeuCau,
                'TrangThai' => 0, // 0 = chờ xác nhận
                'NgayTao' => now(),
                'NgayCapNhap' => now(),
            ]);
            
            // Tìm ID lớn nhất và tăng thêm 1 cho chi tiết đặt bàn
            $maxChiTietId = BangChiTietDatBan::max('ID_ChiTietDatBan') ?? 0;
            $nextChiTietId = $maxChiTietId + 1;
            
            // Tạo chi tiết đặt bàn
            BangChiTietDatBan::create([
                'ID_ChiTietDatBan' => $nextChiTietId,
                'ID_Ban' => $banId,
                'ID_ThongTinDatBan' => $nextId
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Đặt bàn thành công, vui lòng chờ xác nhận',
                'data' => $thongTinDatBan->load('chiTietDatBans.ban')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đặt bàn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết đặt bàn
     */
    public function show($id)
    {
        $thongTinDatBan = ThongTinDatBan::with([
            'user', 
            'chiTietDatBans.ban.khuVuc',
            'chiTietDatMons.monAn',
            'thanhToan'
        ])->find($id);
        
        if (!$thongTinDatBan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đặt bàn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin đặt bàn',
            'data' => $thongTinDatBan
        ]);
    }

    /**
     * Cập nhật thông tin đặt bàn (thay đổi trạng thái, yêu cầu)
     */
    public function update(Request $request, $id)
    {
        $thongTinDatBan = ThongTinDatBan::find($id);
        
        if (!$thongTinDatBan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đặt bàn'
            ], 404);
        }

        // Kiểm tra quyền: Chỉ admin hoặc chủ đơn hàng mới có thể cập nhật
        if ($request->user()->ID_USER != $thongTinDatBan->ID_USER && $request->user()->Quyen != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật đơn đặt bàn này'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'TrangThai' => 'sometimes|required|integer|in:0,1,2',
            'YeuCau' => 'sometimes|nullable|string|max:255',
            'ThoiGianDatBan' => 'sometimes|required|date_format:Y-m-d H:i:s|after:now',
            'SoLuongKhach' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Nếu đã thanh toán thì không được sửa
        if ($thongTinDatBan->thanhToan && $thongTinDatBan->thanhToan->TrangThaiThanhToan == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật đơn đặt bàn đã thanh toán'
            ], 400);
        }

        $thongTinDatBan->update([
            'TrangThai' => $request->input('TrangThai', $thongTinDatBan->TrangThai),
            'YeuCau' => $request->input('YeuCau', $thongTinDatBan->YeuCau),
            'ThoiGianDatBan' => $request->input('ThoiGianDatBan', $thongTinDatBan->ThoiGianDatBan),
            'SoLuongKhach' => $request->input('SoLuongKhach', $thongTinDatBan->SoLuongKhach),
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin đặt bàn thành công',
            'data' => $thongTinDatBan->load('chiTietDatBans.ban')
        ]);
    }

    /**
     * Hủy đặt bàn (đổi trạng thái thành 2 = đã hủy)
     */
    public function destroy($id)
    {
        $thongTinDatBan = ThongTinDatBan::find($id);
        
        if (!$thongTinDatBan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đặt bàn'
            ], 404);
        }

        // Kiểm tra đã thanh toán chưa
        if ($thongTinDatBan->thanhToan && $thongTinDatBan->thanhToan->TrangThaiThanhToan == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn đặt bàn đã thanh toán'
            ], 400);
        }

        $thongTinDatBan->update([
            'TrangThai' => 2, // 2 = đã hủy
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hủy đặt bàn thành công'
        ]);
    }

    /**
 * Xác nhận đặt bàn
 */
public function confirmBooking(Request $request, $id)
{
    $thongTinDatBan = ThongTinDatBan::find($id);
    
    if (!$thongTinDatBan) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy thông tin đặt bàn'
        ], 404);
    }

    // Cập nhật trạng thái
    $thongTinDatBan->update([
        'TrangThai' => 1, // 1 = đã xác nhận
        'NgayCapNhap' => now()
    ]);

    // Gửi thông báo trong hệ thống
    $thongBaoController = new ThongBaoController();
    $thongBao = $thongBaoController->sendBookingConfirmation($id);

    // Gửi email xác nhận
    $emailService = new \App\Services\EmailService();
    $emailSent = $emailService->sendBookingConfirmation($id);

    return response()->json([
        'success' => true,
        'message' => 'Xác nhận đặt bàn thành công',
        'notification_sent' => $thongBao ? true : false,
        'email_sent' => $emailSent,
        'data' => $thongTinDatBan->load('chiTietDatBans.ban')
    ]);
}
}