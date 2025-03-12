<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MonAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MonAnController extends Controller
{
    /**
     * Lấy danh sách món ăn
     */
    public function index(Request $request)
    {
        $query = MonAn::with(['loaiMonAn', 'nhaHang']);
        
        // Lọc theo nhà hàng
        if ($request->has('id_nhahang')) {
            $query->where('ID_NhaHang', $request->id_nhahang);
        }
        
        // Lọc theo loại món ăn
        if ($request->has('ma_loai')) {
            $query->where('MaLoai', $request->ma_loai);
        }
        
        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }
        
        $monAns = $query->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách món ăn',
            'data' => $monAns
        ]);
    }

    /**
     * Tạo món ăn mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_NhaHang' => 'required|exists:NhaHang,ID_NhaHang',
            'MaLoai' => 'required|exists:LoaiMonAn,MaLoai',
            'TenMonAn' => 'required|string|max:255',
            'MoTa' => 'nullable|string|max:255',
            'Gia' => 'required|numeric|min:0',
            'TrangThai' => 'required|integer|in:0,1',
            'Anh1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Xử lý upload hình ảnh
        $anhPaths = [];
        for ($i = 1; $i <= 5; $i++) {
            $fieldName = 'Anh' . $i;
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $filename = 'monan_' . time() . '_' . $i . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/monan', $filename);
                $anhPaths[$fieldName] = 'storage/monan/' . $filename;
            }
        }

        // Tìm ID lớn nhất và tăng thêm 1
        $maxId = MonAn::max('ID_MonAn') ?? 0;
        $nextId = $maxId + 1;

        $monAn = MonAn::create([
            'ID_MonAn' => $nextId,
            'ID_NhaHang' => $request->ID_NhaHang,
            'MaLoai' => $request->MaLoai,
            'TenMonAn' => $request->TenMonAn,
            'MoTa' => $request->MoTa,
            'Gia' => $request->Gia,
            'TrangThai' => $request->TrangThai,
            'Anh1' => $anhPaths['Anh1'] ?? null,
            'Anh2' => $anhPaths['Anh2'] ?? null,
            'Anh3' => $anhPaths['Anh3'] ?? null,
            'Anh4' => $anhPaths['Anh4'] ?? null,
            'Anh5' => $anhPaths['Anh5'] ?? null,
            'NgayTao' => now(),
            'NgayCapNhap' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo món ăn thành công',
            'data' => $monAn
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết món ăn
     */
    public function show($id)
    {
        $monAn = MonAn::with(['loaiMonAn', 'nhaHang'])->find($id);
        
        if (!$monAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin món ăn',
            'data' => $monAn
        ]);
    }

    /**
     * Cập nhật thông tin món ăn
     */
    public function update(Request $request, $id)
    {
        $monAn = MonAn::find($id);
        
        if (!$monAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ID_NhaHang' => 'sometimes|required|exists:NhaHang,ID_NhaHang',
            'MaLoai' => 'sometimes|required|exists:LoaiMonAn,MaLoai',
            'TenMonAn' => 'sometimes|required|string|max:255',
            'MoTa' => 'sometimes|nullable|string|max:255',
            'Gia' => 'sometimes|required|numeric|min:0',
            'TrangThai' => 'sometimes|required|integer|in:0,1',
            'Anh1' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh2' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh3' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh4' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh5' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Xử lý upload hình ảnh mới
        $anhPaths = [];
        for ($i = 1; $i <= 5; $i++) {
            $fieldName = 'Anh' . $i;
            if ($request->hasFile($fieldName)) {
                // Xóa ảnh cũ
                $oldPath = $monAn->$fieldName;
                if ($oldPath) {
                    $oldPathWithoutStorage = str_replace('storage/', '', $oldPath);
                    if (Storage::exists('public/' . $oldPathWithoutStorage)) {
                        Storage::delete('public/' . $oldPathWithoutStorage);
                    }
                }

                // Upload ảnh mới
                $file = $request->file($fieldName);
                $filename = 'monan_' . time() . '_' . $i . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/monan', $filename);
                $anhPaths[$fieldName] = 'storage/monan/' . $filename;
            }
        }

        // Cập nhật dữ liệu
        $dataToUpdate = $request->except(['Anh1', 'Anh2', 'Anh3', 'Anh4', 'Anh5']);
        
        // Thêm đường dẫn ảnh mới
        foreach ($anhPaths as $field => $path) {
            $dataToUpdate[$field] = $path;
        }

        $dataToUpdate['NgayCapNhap'] = now();

        $monAn->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật món ăn thành công',
            'data' => $monAn
        ]);
    }

    /**
     * Xóa món ăn (hoặc đổi trạng thái)
     */
    public function destroy($id)
    {
        $monAn = MonAn::find($id);
        
        if (!$monAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        }

        // Thay vì xóa, ta set TrangThai = 0 (không hoạt động)
        $monAn->update(['TrangThai' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đổi trạng thái món ăn thành không hoạt động'
        ]);
    }
}