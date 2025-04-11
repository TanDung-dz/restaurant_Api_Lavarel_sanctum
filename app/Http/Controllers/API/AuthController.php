<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenDangNhap' => 'required|string|unique:User,TenDangNhap',
            'MatKhau' => 'required|string|min:6',
            'HoVaTen' => 'required|string',
            'Email' => 'required|email|unique:User,Email',
            'Sdt' => 'required|string|min:10',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Tìm ID_USER lớn nhất và tăng thêm 1
        $maxId = User::max('ID_USER') ?? 0;
        $nextId = $maxId + 1;
    
        $user = User::create([
            'ID_USER' => $nextId,
            'TenDangNhap' => $request->TenDangNhap,
            'MatKhau' => Hash::make($request->MatKhau),
            'HoVaTen' => $request->HoVaTen,
            'Email' => $request->Email,
            'Sdt' => $request->Sdt,
            'Quyen' => $request->Quyen ?? 0, // 0: người dùng thường, 1: admin
            'NgayDK' => now(),
            'Hide' => 0,
            'NgayTao' => now(),
            'NgayCapNhap' => now(),
        ]);
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('TenDangNhap', $request->TenDangNhap)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->MatKhau)) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không chính xác'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Thông tin người dùng',
            'data' => $request->user()
        ]);
    }
}