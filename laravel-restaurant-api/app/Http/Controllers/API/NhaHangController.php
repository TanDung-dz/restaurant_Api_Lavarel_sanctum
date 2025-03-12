<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class NhaHangController extends Controller
{
    /**
     * Lấy danh sách nhà hàng
     */
    public function index()
    {
        $nhahangs = NhaHang::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách nhà hàng',
            'data' => $nhahangs
        ]);
    }

    /**
     * Tạo nhà hàng mới
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'TenNhaHang' => 'required|string|max:255',
        'DiaChi' => 'required|string|max:255',
        'Sdt' => 'required|string|max:20',
        'Email' => 'required|email|max:255',
        'MieuTa' => 'nullable|string|max:1000',
        'OpenTime' => 'required',
        'CloseTime' => 'required',
        'DungTich' => 'required|integer',
        'Anh1' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        'Anh2' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        'Anh3' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
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
    for ($i = 1; $i <= 3; $i++) {
        $fieldName = 'Anh' . $i;
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $filename = 'nhahang_' . time() . '_' . $i . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/nhahang', $filename);
            $anhPaths[$fieldName] = 'storage/nhahang/' . $filename;
        }
    }

    // Tìm ID lớn nhất và tăng thêm 1
    $maxId = NhaHang::max('ID_NhaHang') ?? 0;
    $nextId = $maxId + 1;

    // Định dạng thời gian
    $openTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' ' . $request->OpenTime));
    $closeTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' ' . $request->CloseTime));

    $nhahang = NhaHang::create([
        'ID_NhaHang' => $nextId, // Thêm ID_NhaHang vào đây
        'TenNhaHang' => $request->TenNhaHang,
        'DiaChi' => $request->DiaChi,
        'Sdt' => $request->Sdt,
        'Email' => $request->Email,
        'MieuTa' => $request->MieuTa,
        'OpenTime' => $openTime,
        'CloseTime' => $closeTime,
        'DungTich' => $request->DungTich,
        'XepHangTrungBinh' => 0,
        'Anh1' => $anhPaths['Anh1'] ?? null,
        'Anh2' => $anhPaths['Anh2'] ?? null,
        'Anh3' => $anhPaths['Anh3'] ?? null,
        'NgayTao' => now(),
        'NgayCapNhap' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Tạo nhà hàng thành công',
        'data' => $nhahang
    ], 201);
}
    /**
     * Hiển thị thông tin chi tiết nhà hàng
     */
    public function show($id)
    {
        $nhahang = NhaHang::find($id);
        
        if (!$nhahang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà hàng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin nhà hàng',
            'data' => $nhahang
        ]);
    }

    /**
     * Cập nhật thông tin nhà hàng
     */
    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
        \Illuminate\Support\Facades\Log::info('Spoofed method: ' . $request->input('_method'));
    
        $nhahang = NhaHang::find($id);
    
        if (!$nhahang) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy nhà hàng'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'TenNhaHang' => 'sometimes|required|string|max:255',
            'DiaChi' => 'sometimes|required|string|max:255',
            'Sdt' => 'sometimes|required|string|max:20',
            'Email' => 'sometimes|required|email|max:255',
            'MieuTa' => 'sometimes|nullable|string|max:1000',
            'OpenTime' => 'sometimes|required|date_format:H:i',
            'CloseTime' => 'sometimes|required|date_format:H:i',
            'DungTich' => 'sometimes|required|integer',
            'Anh1' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh2' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'Anh3' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = 'Anh' . $i;
            if ($request->hasFile($fieldName)) {
                // Xóa ảnh cũ nếu có
                $oldPath = $nhahang->$fieldName;
                if ($oldPath) {
                    $oldPathWithoutStorage = str_replace('storage/', '', $oldPath);
                    if (Storage::exists('public/' . $oldPathWithoutStorage)) {
                        Storage::delete('public/' . $oldPathWithoutStorage);
                    }
                }

                // Upload ảnh mới
                $file = $request->file($fieldName);
                $filename = 'nhahang_' . time() . '_' . $i . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/nhahang', $filename);
                $anhPaths[$fieldName] = 'storage/nhahang/' . $filename;
            }
        }

        // Cập nhật dữ liệu
        $dataToUpdate = $request->except(['Anh1', 'Anh2', 'Anh3']);

        // Định dạng thời gian từ H:i thành datetime nếu có
        if (isset($dataToUpdate['OpenTime'])) {
            $now = now()->format('Y-m-d');
            $openTime = \DateTime::createFromFormat('Y-m-d H:i', $now . ' ' . $dataToUpdate['OpenTime']);
            $dataToUpdate['OpenTime'] = $openTime;
        }
        
        if (isset($dataToUpdate['CloseTime'])) {
            $now = now()->format('Y-m-d');
            $closeTime = \DateTime::createFromFormat('Y-m-d H:i', $now . ' ' . $dataToUpdate['CloseTime']);
            $dataToUpdate['CloseTime'] = $closeTime;
        }

        // Thêm đường dẫn ảnh mới nếu có
        foreach ($anhPaths as $field => $path) {
            $dataToUpdate[$field] = $path;
        }

        $dataToUpdate['NgayCapNhap'] = now();

        $nhahang->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nhà hàng thành công',
            'data' => $nhahang
        ]);
    }

    /**
     * Xóa nhà hàng
     */
    public function destroy($id)
    {
        $nhahang = NhaHang::find($id);
        
        if (!$nhahang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà hàng'
            ], 404);
        }

        // Xóa các ảnh liên quan
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = 'Anh' . $i;
            $path = $nhahang->$fieldName;
            if ($path) {
                $pathWithoutStorage = str_replace('storage/', '', $path);
                if (Storage::exists('public/' . $pathWithoutStorage)) {
                    Storage::delete('public/' . $pathWithoutStorage);
                }
            }
        }

        $nhahang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nhà hàng thành công'
        ]);
    }
}