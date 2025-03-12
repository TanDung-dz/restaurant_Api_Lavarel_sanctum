<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DanhGiaController extends Controller
{
    /**
     * Lấy danh sách đánh giá
     */
    public function index(Request $request)
    {
        $query = DanhGia::with(['user', 'nhaHang']);
        
        // Lọc theo nhà hàng
        if ($request->has('id_nhahang')) {
            $query->where('ID_NhaHang', $request->id_nhahang);
        }
        
        // Lọc theo người dùng
        if ($request->has('id_user')) {
            $query->where('ID_USER', $request->id_user);
        }
        
        // Lọc theo xếp hạng
        if ($request->has('xep_hang')) {
            $query->where('XepHang', $request->xep_hang);
        }
        
        $danhGias = $query->orderBy('ID_DanhGia', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách đánh giá',
            'data' => $danhGias
        ]);
    }

    /**
     * Tạo đánh giá mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_NhaHang' => 'required|exists:NhaHang,ID_NhaHang',
            'XepHang' => 'required|integer|min:1|max:5',
            'BinhLuan' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra người dùng đã đánh giá nhà hàng này chưa
        $existingRating = DanhGia::where('ID_USER', $request->user()->ID_USER)
                           ->where('ID_NhaHang', $request->ID_NhaHang)
                           ->first();
        
        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá nhà hàng này, vui lòng cập nhật đánh giá thay vì tạo mới'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            // Tìm ID lớn nhất và tăng thêm 1
            $maxId = DanhGia::max('ID_DanhGia') ?? 0;
            $nextId = $maxId + 1;
            
            // Tạo đánh giá mới
            $danhGia = DanhGia::create([
                'ID_DanhGia' => $nextId,
                'ID_USER' => $request->user()->ID_USER,
                'XepHang' => $request->XepHang,
                'BinhLuan' => $request->BinhLuan,
                'TraLoi' => null,
                'ID_NhaHang' => $request->ID_NhaHang,
                'NgayTao' => now(),
                'NgayCapNhat' => now(),
            ]);
            
            // Cập nhật xếp hạng trung bình của nhà hàng
            $this->updateAverageRating($request->ID_NhaHang);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá thành công',
                'data' => $danhGia->load(['user', 'nhaHang'])
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết đánh giá
     */
    public function show($id)
    {
        $danhGia = DanhGia::with(['user', 'nhaHang'])->find($id);
        
        if (!$danhGia) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đánh giá'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin đánh giá',
            'data' => $danhGia
        ]);
    }

    /**
     * Cập nhật đánh giá
     */
    public function update(Request $request, $id)
    {
        $danhGia = DanhGia::find($id);
        
        if (!$danhGia) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đánh giá'
            ], 404);
        }

        // Kiểm tra quyền: chỉ người tạo đánh giá mới được cập nhật đánh giá
        if ($request->user()->ID_USER != $danhGia->ID_USER) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật đánh giá này'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'XepHang' => 'sometimes|required|integer|min:1|max:5',
            'BinhLuan' => 'sometimes|required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Cập nhật đánh giá
            $danhGia->update([
                'XepHang' => $request->input('XepHang', $danhGia->XepHang),
                'BinhLuan' => $request->input('BinhLuan', $danhGia->BinhLuan),
                'NgayCapNhat' => now(),
            ]);
            
            // Cập nhật xếp hạng trung bình của nhà hàng
            $this->updateAverageRating($danhGia->ID_NhaHang);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đánh giá thành công',
                'data' => $danhGia->load(['user', 'nhaHang'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa đánh giá
     */
    public function destroy(Request $request, $id)
    {
        $danhGia = DanhGia::find($id);
        
        if (!$danhGia) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đánh giá'
            ], 404);
        }

        // Kiểm tra quyền: chỉ người tạo đánh giá hoặc admin mới được xóa
        if ($request->user()->ID_USER != $danhGia->ID_USER && $request->user()->Quyen != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa đánh giá này'
            ], 403);
        }

        try {
            DB::beginTransaction();
            
            $idNhaHang = $danhGia->ID_NhaHang;
            
            // Xóa đánh giá
            $danhGia->delete();
            
            // Cập nhật xếp hạng trung bình của nhà hàng
            $this->updateAverageRating($idNhaHang);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa đánh giá thành công'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm trả lời cho đánh giá
     */
    public function reply(Request $request, $id)
    {
        $danhGia = DanhGia::find($id);
        
        if (!$danhGia) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đánh giá'
            ], 404);
        }

        // Kiểm tra quyền: chỉ admin mới được trả lời đánh giá
        if ($request->user()->Quyen != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền trả lời đánh giá này'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'TraLoi' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $danhGia->update([
            'TraLoi' => $request->TraLoi,
            'NgayCapNhat' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trả lời đánh giá thành công',
            'data' => $danhGia->load(['user', 'nhaHang'])
        ]);
    }

    /**
     * Cập nhật xếp hạng trung bình của nhà hàng
     */
    private function updateAverageRating($idNhaHang)
    {
        $avgRating = DanhGia::where('ID_NhaHang', $idNhaHang)->avg('XepHang');
        $avgRating = round($avgRating);
        
        NhaHang::where('ID_NhaHang', $idNhaHang)->update([
            'XepHangTrungBinh' => $avgRating ?: 0,
            'NgayCapNhap' => now()
        ]);
    }
}