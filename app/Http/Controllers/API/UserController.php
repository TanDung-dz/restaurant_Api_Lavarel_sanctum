<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Lấy danh sách tất cả người dùng
     */
    public function index()
    {
        $users = User::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh sách người dùng',
            'data' => $users
        ]);
    }

    /**
     * Lấy thông tin chi tiết của một người dùng
     */
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Thông tin người dùng',
            'data' => $user
        ]);
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'TenDangNhap' => 'sometimes|required|string|unique:User,TenDangNhap,' . $id . ',ID_USER',
            'HoVaTen' => 'sometimes|required|string',
            'Email' => 'sometimes|required|email|unique:User,Email,' . $id . ',ID_USER',
            'Sdt' => 'sometimes|required|string|min:10',
            'MatKhau' => 'sometimes|required|string|min:6',
            'Quyen' => 'sometimes|required|integer',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $userData = $request->all();
        
        // Nếu có cập nhật mật khẩu thì mã hóa mật khẩu
        if (isset($userData['MatKhau'])) {
            $userData['MatKhau'] = Hash::make($userData['MatKhau']);
        }
        
        $user->update($userData);
        $user->NgayCapNhap = now();
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật người dùng thành công',
            'data' => $user
        ]);
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    }
}