<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\BangChiTietDatBan;
use App\Models\ThongTinDatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BangChiTietDatBanController extends Controller
{
    /**
     * Gán bàn cho đặt bàn
     */
    public function assignTable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
            'ID_Ban' => 'required|exists:Ban,ID_Ban',
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
        if ($thongTinDatBan->TrangThai == 2) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn đã bị hủy, không thể gán bàn'
            ], 400);
        }

        // Kiểm tra bàn có sẵn không
        $banId = $request->ID_Ban;
        $thoiGianDatBan = $thongTinDatBan->ThoiGianDatBan;
        
        // Lấy thông tin bàn để kiểm tra dung tích
        $ban = Ban::find($banId);
        if ($ban->DungTich < $thongTinDatBan->SoLuongKhach) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn không đủ chỗ cho số lượng khách'
            ], 400);
        }
        
        // Kiểm tra bàn đã được đặt vào thời điểm đó chưa
        $isBooked = BangChiTietDatBan::whereHas('thongTinDatBan', function($query) use ($thoiGianDatBan, $thongTinDatBan) {
            $query->whereDate('ThoiGianDatBan', date('Y-m-d', strtotime($thoiGianDatBan)))
                  ->where('TrangThai', 1)
                  ->where('ID_ThongTinDatBan', '!=', $thongTinDatBan->ID_ThongTinDatBan);
        })->where('ID_Ban', $banId)->exists();
        
        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn đã được đặt vào thời điểm này'
            ], 400);
        }

        // Tìm ID lớn nhất và tăng thêm 1
        $maxId = BangChiTietDatBan::max('ID_ChiTietDatBan') ?? 0;
        $nextId = $maxId + 1;

        // Kiểm tra xem đã có bàn nào được gán chưa
        $existingTable = BangChiTietDatBan::where('ID_ThongTinDatBan', $request->ID_ThongTinDatBan)->first();

        if ($existingTable) {
            // Nếu đã có bàn, cập nhật bàn mới
            $existingTable->update([
                'ID_Ban' => $banId
            ]);
            
            $message = 'Cập nhật bàn thành công';
        } else {
            // Nếu chưa có bàn, tạo mới
            BangChiTietDatBan::create([
                'ID_ChiTietDatBan' => $nextId,
                'ID_Ban' => $banId,
                'ID_ThongTinDatBan' => $request->ID_ThongTinDatBan
            ]);
            
            $message = 'Gán bàn thành công';
        }

        // Cập nhật trạng thái đặt bàn thành đã xác nhận
        $thongTinDatBan->update([
            'TrangThai' => 1, // 1 = đã xác nhận
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $thongTinDatBan->load('chiTietDatBans.ban')
        ]);
    }

    /**
     * Hủy gán bàn
     */
    public function removeTable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
            'ID_ChiTietDatBan' => 'required|exists:BangChiTietDatBan,ID_ChiTietDatBan',
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
        if ($thongTinDatBan->TrangThai == 2) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn đã bị hủy, không thể thay đổi'
            ], 400);
        }

        // Xóa chi tiết đặt bàn
        BangChiTietDatBan::where('ID_ChiTietDatBan', $request->ID_ChiTietDatBan)
            ->where('ID_ThongTinDatBan', $request->ID_ThongTinDatBan)
            ->delete();

        // Cập nhật trạng thái đặt bàn thành chờ xác nhận
        $thongTinDatBan->update([
            'TrangThai' => 0, // 0 = chờ xác nhận
            'NgayCapNhap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hủy gán bàn thành công',
            'data' => $thongTinDatBan
        ]);
    }
}