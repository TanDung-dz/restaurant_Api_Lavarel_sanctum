<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KhuVuc;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhuVucController extends Controller
{
    /**
     * Lấy danh sách khu vực của nhà hàng
     */
    public function index(Request $request)
    {
        $idNhaHang = $request->input('id_nhahang');
        
        if ($idNhaHang) {
            $khuVucs = KhuVuc::where('ID_NhaHang', $idNhaHang)->get();
        } else {
            $khuVucs = KhuVuc::all();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách khu vực',
            'data' => $khuVucs
        ]);
    }

    /**
     * Tạo khu vực mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_NhaHang' => 'required|exists:NhaHang,ID_NhaHang',
            'Ten' => 'required|string|max:255',
            'DiaChi' => 'required|string|max:255',
            'Tang' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tìm ID lớn nhất và tăng thêm 1
        $maxId = KhuVuc::max('ID_KhuVuc') ?? 0;
        $nextId = $maxId + 1;

        $khuVuc = KhuVuc::create([
            'ID_KhuVuc' => $nextId,
            'ID_NhaHang' => $request->ID_NhaHang,
            'Ten' => $request->Ten,
            'DiaChi' => $request->DiaChi,
            'Tang' => $request->Tang,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo khu vực thành công',
            'data' => $khuVuc
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết khu vực
     */
    public function show($id)
    {
        $khuVuc = KhuVuc::with('bans')->find($id);
        
        if (!$khuVuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin khu vực',
            'data' => $khuVuc
        ]);
    }

    /**
     * Cập nhật thông tin khu vực
     */
    public function update(Request $request, $id)
    {
        $khuVuc = KhuVuc::find($id);
        
        if (!$khuVuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ID_NhaHang' => 'sometimes|required|exists:NhaHang,ID_NhaHang',
            'Ten' => 'sometimes|required|string|max:255',
            'DiaChi' => 'sometimes|required|string|max:255',
            'Tang' => 'sometimes|required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $khuVuc->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật khu vực thành công',
            'data' => $khuVuc
        ]);
    }

    /**
     * Xóa khu vực
     */
    public function destroy($id)
    {
        $khuVuc = KhuVuc::find($id);
        
        if (!$khuVuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ], 404);
        }

        // Kiểm tra xem có bàn nào trong khu vực này không
        if ($khuVuc->bans->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa khu vực vì còn bàn thuộc khu vực này'
            ], 400);
        }

        $khuVuc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa khu vực thành công'
        ]);
    }
}