<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\User;
use App\Models\ThongTinDatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 
class ThongBaoController extends Controller
{
    /**
     * Lấy danh sách thông báo của người dùng
     */
    public function index(Request $request)
    {
        $query = ThongBao::where('ID_USER', $request->user()->ID_USER)
                  ->where('Hide', 0);
        
        // Lọc theo đã đọc hay chưa
        if ($request->has('da_doc')) {
            $query->where('DaDoc', $request->da_doc);
        }
        
        // Lọc theo thể loại
        if ($request->has('the_loai')) {
            $query->where('TheLoai', $request->the_loai);
        }
        
        $thongBaos = $query->orderBy('NgayTao', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách thông báo',
            'data' => $thongBaos
        ]);
    }

    /**
     * Xem chi tiết thông báo
     */
    public function show(Request $request, $id)
    {
        $thongBao = ThongBao::find($id);
        
        if (!$thongBao) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        // Kiểm tra quyền: chỉ người nhận thông báo mới được xem
        if ($request->user()->ID_USER != $thongBao->ID_USER) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem thông báo này'
            ], 403);
        }

        // Cập nhật trạng thái đã đọc
        if (!$thongBao->DaDoc) {
            $thongBao->update([
                'DaDoc' => true,
                'NgayCapNhap' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin thông báo',
            'data' => $thongBao
        ]);
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(Request $request, $id)
    {
        $thongBao = ThongBao::find($id);
        
        if (!$thongBao) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        // Kiểm tra quyền: chỉ người nhận thông báo mới được đánh dấu
        if ($request->user()->ID_USER != $thongBao->ID_USER) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thao tác với thông báo này'
            ], 403);
        }

        $thongBao->update([
            'DaDoc' => true,
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu thông báo như đã đọc',
            'data' => $thongBao
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead(Request $request)
    {
        ThongBao::where('ID_USER', $request->user()->ID_USER)
            ->where('DaDoc', false)
            ->where('Hide', false)
            ->update([
                'DaDoc' => true,
                'NgayCapNhap' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo như đã đọc'
        ]);
    }

    /**
     * Xóa thông báo (ẩn)
     */
    public function destroy(Request $request, $id)
    {
        $thongBao = ThongBao::find($id);
        
        if (!$thongBao) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        // Kiểm tra quyền: chỉ người nhận thông báo mới được xóa
        if ($request->user()->ID_USER != $thongBao->ID_USER) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa thông báo này'
            ], 403);
        }

        $thongBao->update([
            'Hide' => true,
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo'
        ]);
    }

    /**
     * Gửi thông báo đến người dùng
     */
    public function sendNotification($userId, $title, $description, $content, $type, $condition = null)
    {
        try {
            // Tìm ID lớn nhất và tăng thêm 1
            $maxId = ThongBao::max('ID_ThongBao') ?? 0;
            $nextId = $maxId + 1;
            
            $thongBao = ThongBao::create([
                'ID_ThongBao' => $nextId,
                'ID_USER' => $userId,
                'Ten' => $title,
                'MoTa' => $description,
                'NoiDung' => $content,
                'TheLoai' => $type,
                'DieuKienKichHoat' => $condition,
                'DaDoc' => false,
                'Hide' => false,
                'NgayTao' => now(),
                'NgayCapNhap' => now(),
            ]);
            
            return $thongBao;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gửi thông báo xác nhận đặt bàn
     */
    public function sendBookingConfirmation($bookingId)
    {
        $datBan = ThongTinDatBan::with(['user', 'chiTietDatBans.ban.khuVuc.nhaHang'])
                  ->find($bookingId);
        
        if (!$datBan) {
            return false;
        }
        
        $user = $datBan->user;
        $nhaHang = $datBan->chiTietDatBans[0]->ban->khuVuc->nhaHang ?? null;
        
        if (!$user || !$nhaHang) {
            return false;
        }
        
        $title = 'Xác nhận đặt bàn';
        $description = 'Đơn đặt bàn của bạn đã được xác nhận';
        $content = "Kính gửi {$user->HoVaTen},\n\nĐơn đặt bàn của bạn tại {$nhaHang->TenNhaHang} vào lúc " . 
                  $datBan->ThoiGianDatBan->format('H:i d/m/Y') . " đã được xác nhận.\n" .
                  "Số lượng khách: {$datBan->SoLuongKhach}\n" .
                  "Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!";
        
        return $this->sendNotification($user->ID_USER, $title, $description, $content, 'booking_confirmation');
    }

    /**
     * Gửi thông báo nhắc lịch đặt bàn
     */
    public function sendBookingReminder($bookingId)
    {
        $datBan = ThongTinDatBan::with(['user', 'chiTietDatBans.ban.khuVuc.nhaHang'])
                  ->find($bookingId);
        
        if (!$datBan) {
            return false;
        }
        
        $user = $datBan->user;
        $nhaHang = $datBan->chiTietDatBans[0]->ban->khuVuc->nhaHang ?? null;
        
        if (!$user || !$nhaHang) {
            return false;
        }
        
        $title = 'Nhắc lịch đặt bàn';
        $description = 'Nhắc nhở lịch đặt bàn sắp tới của bạn';
        $content = "Kính gửi {$user->HoVaTen},\n\nChỉ còn 1 ngày nữa là đến lịch đặt bàn của bạn tại {$nhaHang->TenNhaHang} vào lúc " . 
                  $datBan->ThoiGianDatBan->format('H:i d/m/Y') . ".\n" .
                  "Số lượng khách: {$datBan->SoLuongKhach}\n" .
                  "Chúng tôi rất mong được phục vụ bạn!";
        
        return $this->sendNotification($user->ID_USER, $title, $description, $content, 'booking_reminder');
    }

    public function createNotification(Request $request)
{
    $validator = Validator::make($request->all(), [
        'ID_USER' => 'required|exists:User,ID_USER',
        'Ten' => 'required|string|max:255',
        'MoTa' => 'required|string|max:255',
        'NoiDung' => 'required|string|max:1000',
        'TheLoai' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }

    // Tìm ID lớn nhất và tăng thêm 1
    $maxId = ThongBao::max('ID_ThongBao') ?? 0;
    $nextId = $maxId + 1;
    
    $thongBao = ThongBao::create([
        'ID_ThongBao' => $nextId,
        'ID_USER' => $request->ID_USER,
        'Ten' => $request->Ten,
        'MoTa' => $request->MoTa,
        'NoiDung' => $request->NoiDung,
        'TheLoai' => $request->TheLoai,
        'DieuKienKichHoat' => $request->DieuKienKichHoat,
        'DaDoc' => false,
        'Hide' => false,
        'NgayTao' => now(),
        'NgayCapNhap' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Tạo thông báo thành công',
        'data' => $thongBao
    ], 201);
}

    /**
     * Gửi thông báo xác nhận thanh toán
     */
    public function sendPaymentConfirmation($paymentId)
    {
        // Logic gửi thông báo xác nhận thanh toán
        // Tương tự các phương thức trên
    }

    /**
     * Gửi thông báo phản hồi đánh giá
     */
    public function sendReviewResponse($reviewId)
    {
        // Logic gửi thông báo phản hồi đánh giá
        // Tương tự các phương thức trên
    }
}