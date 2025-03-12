<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDatMon;
use App\Models\MonAn;
use App\Models\ThongTinDatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChiTietDatMonController extends Controller
{
    /**
     * Lấy danh sách món ăn đã đặt
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_thongtin_datban' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $chiTietDatMons = ChiTietDatMon::with('monAn')
            ->where('ID_ThongTinDatBan', $request->id_thongtin_datban)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Danh sách món ăn đã đặt',
            'data' => $chiTietDatMons
        ]);
    }

    /**
     * Thêm món ăn vào đơn đặt bàn
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
            'ID_MonAn' => 'required|exists:MonAn,ID_MonAn',
            'SoLuong' => 'required|integer|min:1',
            'GhiChu' => 'nullable|string|max:255',
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
                'message' => 'Đơn đặt bàn đã bị hủy, không thể thêm món ăn'
            ], 400);
        }

        $monAn = MonAn::find($request->ID_MonAn);
        
        // Kiểm tra món ăn có hoạt động không
        if ($monAn->TrangThai == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Món ăn không còn phục vụ'
            ], 400);
        }

        // Tìm ID lớn nhất và tăng thêm 1
        $maxId = ChiTietDatMon::max('ID_ChiTietDatMon') ?? 0;
        $nextId = $maxId + 1;

        // Tính thành tiền
        $donGia = $monAn->Gia;
        $soLuong = $request->SoLuong;
        $thanhTien = $donGia * $soLuong;

        try {
            // Kiểm tra xem món ăn đã có trong đơn chưa
            $existingItem = ChiTietDatMon::where('ID_ThongTinDatBan', $request->ID_ThongTinDatBan)
                              ->where('ID_MonAn', $request->ID_MonAn)
                              ->first();

            if ($existingItem) {
                // Nếu đã có, cập nhật số lượng và thành tiền
                $newSoLuong = $existingItem->SoLuong + $soLuong;
                $newThanhTien = $donGia * $newSoLuong;
                
                $existingItem->update([
                    'SoLuong' => $newSoLuong,
                    'ThanhTien' => $newThanhTien,
                    'GhiChu' => $request->GhiChu ?? $existingItem->GhiChu
                ]);
                
                $chiTietDatMon = $existingItem;
                $message = 'Cập nhật số lượng món ăn thành công';
            } else {
                // Nếu chưa có, tạo mới
                $chiTietDatMon = ChiTietDatMon::create([
                    'ID_ChiTietDatMon' => $nextId,
                    'ID_MonAn' => $request->ID_MonAn,
                    'ID_ThongTinDatBan' => $request->ID_ThongTinDatBan,
                    'SoLuong' => $soLuong,
                    'GhiChu' => $request->GhiChu,
                    'DonGia' => $donGia,
                    'ThanhTien' => $thanhTien
                ]);
                
                $message = 'Thêm món ăn vào đơn đặt bàn thành công';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $chiTietDatMon->load('monAn')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi thêm món ăn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng món ăn
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
            'ID_MonAn' => 'required|exists:MonAn,ID_MonAn',
            'SoLuong' => 'required|integer|min:0',
            'GhiChu' => 'nullable|string|max:255',
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
                'message' => 'Đơn đặt bàn đã bị hủy, không thể cập nhật'
            ], 400);
        }

        $chiTietDatMon = ChiTietDatMon::where('ID_ThongTinDatBan', $request->ID_ThongTinDatBan)
                          ->where('ID_MonAn', $request->ID_MonAn)
                          ->first();
        
        if (!$chiTietDatMon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn trong đơn đặt bàn'
            ], 404);
        }

        $soLuong = $request->SoLuong;
        
        if ($soLuong == 0) {
            // Nếu số lượng = 0, xóa món ăn khỏi đơn
            $chiTietDatMon->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa món ăn khỏi đơn đặt bàn'
            ]);
        } else {
            // Cập nhật số lượng và thành tiền
            $thanhTien = $chiTietDatMon->DonGia * $soLuong;
            
            $chiTietDatMon->update([
                'SoLuong' => $soLuong,
                'ThanhTien' => $thanhTien,
                'GhiChu' => $request->GhiChu ?? $chiTietDatMon->GhiChu
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật món ăn thành công',
                'data' => $chiTietDatMon->load('monAn')
            ]);
        }
    }

    /**
     * Xóa món ăn khỏi đơn đặt bàn
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_ThongTinDatBan' => 'required|exists:ThongTinDatBan,ID_ThongTinDatBan',
            'ID_MonAn' => 'required|exists:MonAn,ID_MonAn',
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
                'message' => 'Đơn đặt bàn đã bị hủy, không thể xóa món ăn'
            ], 400);
        }

        $deleted = ChiTietDatMon::where('ID_ThongTinDatBan', $request->ID_ThongTinDatBan)
                    ->where('ID_MonAn', $request->ID_MonAn)
                    ->delete();
        
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn trong đơn đặt bàn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa món ăn khỏi đơn đặt bàn'
        ]);
    }

    /**
     * Tính tổng tiền của đơn đặt bàn
     */
    public function calculateTotal($idThongTinDatBan)
    {
        $total = ChiTietDatMon::where('ID_ThongTinDatBan', $idThongTinDatBan)
                  ->sum('ThanhTien');
        
        return $total;
    }
}