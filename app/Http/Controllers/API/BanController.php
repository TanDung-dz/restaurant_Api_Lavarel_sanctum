<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\KhuVuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BanController extends Controller
{
    /**
     * Lấy danh sách bàn
     */
    public function index(Request $request)
    {
        $idKhuVuc = $request->input('id_khuvuc');

        if ($idKhuVuc) {
            $bans = Ban::where('ID_KhuVuc', $idKhuVuc)->get();
        } else {
            $bans = Ban::all();
        }

        return response()->json([
            'success' => true,
            'message' => 'Danh sách bàn',
            'data' => $bans
        ]);
    }

    /**
     * Tạo bàn mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'SoBang' => 'required|integer',
            'DungTich' => 'required|integer',
            'ID_KhuVuc' => 'required|exists:KhuVuc,ID_KhuVuc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tìm ID lớn nhất và tăng thêm 1
        $maxId = Ban::max('ID_Ban') ?? 0;
        $nextId = $maxId + 1;

        $ban = Ban::create([
            'ID_Ban' => $nextId,
            'SoBang' => $request->SoBang,
            'DungTich' => $request->DungTich,
            'ID_KhuVuc' => $request->ID_KhuVuc,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bàn thành công',
            'data' => $ban
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết bàn
     */
    public function show($id)
    {
        $ban = Ban::with('khuVuc')->find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông tin bàn',
            'data' => $ban
        ]);
    }

    /**
     * Cập nhật thông tin bàn
     */
    public function update(Request $request, $id)
    {
        $ban = Ban::find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'SoBang' => 'sometimes|required|integer',
            'DungTich' => 'sometimes|required|integer',
            'ID_KhuVuc' => 'sometimes|required|exists:KhuVuc,ID_KhuVuc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $ban->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bàn thành công',
            'data' => $ban
        ]);
    }

    /**
     * Xóa bàn
     */
    public function destroy($id)
    {
        $ban = Ban::find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        // Kiểm tra xem bàn có đang được đặt không
        // Có thể thêm logic kiểm tra ở đây nếu cần

        $ban->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bàn thành công'
        ]);
    }
}
