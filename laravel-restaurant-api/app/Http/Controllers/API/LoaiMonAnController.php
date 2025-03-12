<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LoaiMonAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoaiMonAnController extends Controller
{
    /**
     * Lấy danh sách loại món ăn
     */
    public function index()
    {
        $loaiMonAns = LoaiMonAn::where('Hide', 0)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách loại món ăn',
            'data' => $loaiMonAns
        ]);
    }

    /**
     * Tạo loại món ăn mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenLoai' => 'required|string|max:255',
            'MoTa' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tìm MaLoai lớn nhất và tăng thêm 1
        $maxId = LoaiMonAn::max('MaLoai') ?? 0;
        $nextId = $maxId + 1;

        $loaiMonAn = LoaiMonAn::create([
            'MaLoai' => $nextId,
            'TenLoai' => $request->TenLoai,
            'MoTa' => $request->MoTa,
            'Hide' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo loại món ăn thành công',
            'data' => $loaiMonAn
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết loại món ăn
     */
    public function show($id)
    {
        $loaiMonAn = LoaiMonAn::find($id);
        
        if (!$loaiMonAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy loại món ăn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin loại món ăn',
            'data' => $loaiMonAn
        ]);
    }

    /**
     * Cập nhật thông tin loại món ăn
     */
    public function update(Request $request, $id)
    {
        $loaiMonAn = LoaiMonAn::find($id);
        
        if (!$loaiMonAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy loại món ăn'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'TenLoai' => 'sometimes|required|string|max:255',
            'MoTa' => 'sometimes|nullable|string|max:255',
            'Hide' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $loaiMonAn->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại món ăn thành công',
            'data' => $loaiMonAn
        ]);
    }

    /**
     * Xóa loại món ăn (Ẩn thay vì xóa)
     */
    public function destroy($id)
    {
        $loaiMonAn = LoaiMonAn::find($id);
        
        if (!$loaiMonAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy loại món ăn'
            ], 404);
        }

        // Kiểm tra xem có món ăn nào thuộc loại này không
        if ($loaiMonAn->monAns()->where('TrangThai', 1)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa loại món ăn vì còn món ăn thuộc loại này'
            ], 400);
        }

        // Thay vì xóa, ta set Hide = 1
        $loaiMonAn->update(['Hide' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Ẩn loại món ăn thành công'
        ]);
    }
}