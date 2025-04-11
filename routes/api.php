<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BanController;
use App\Http\Controllers\API\BangChiTietDatBanController;
use App\Http\Controllers\API\ChiTietDatMonController;
use App\Http\Controllers\API\DanhGiaController;
use App\Http\Controllers\API\KhuVucController;
use App\Http\Controllers\API\LoaiMonAnController;
use App\Http\Controllers\API\MonAnController;
use App\Http\Controllers\API\NhaHangController;
use App\Http\Controllers\API\ThanhToanController;
use App\Http\Controllers\API\ThongBaoController;
use App\Http\Controllers\API\ThongTinDatBanController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// Routes không cần xác thực
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes cho thực đơn công khai
Route::get('/loai-mon-an', [LoaiMonAnController::class, 'index']);
Route::get('/mon-an', [MonAnController::class, 'index']);
Route::get('/mon-an/{id}', [MonAnController::class, 'show']);

// Routes cần xác thực
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // NhaHang routes
    Route::get('/nhahang', [NhaHangController::class, 'index']);
    Route::post('/nhahang', [NhaHangController::class, 'store']);
    Route::get('/nhahang/{id}', [NhaHangController::class, 'show']);
    Route::match(['put', 'post'], '/nhahang/{id}', [NhaHangController::class, 'update']);
    Route::delete('/nhahang/{id}', [NhaHangController::class, 'destroy']);

     // Routes KhuVuc
     Route::get('/khuvuc', [KhuVucController::class, 'index']);
     Route::post('/khuvuc', [KhuVucController::class, 'store']);
     Route::get('/khuvuc/{id}', [KhuVucController::class, 'show']);
     Route::match(['put', 'post'], '/khuvuc/{id}', [KhuVucController::class, 'update']);
     Route::delete('/khuvuc/{id}', [KhuVucController::class, 'destroy']);
     
     // Routes Ban
     Route::get('/ban', [BanController::class, 'index']);
     Route::post('/ban', [BanController::class, 'store']);
     Route::get('/ban/{id}', [BanController::class, 'show']);
     Route::match(['put', 'post'], '/ban/{id}', [BanController::class, 'update']);
     Route::delete('/ban/{id}', [BanController::class, 'destroy']);


     // Routes LoaiMonAn (chỉ những thao tác cần xác thực)
    Route::post('/loai-mon-an', [LoaiMonAnController::class, 'store']);
    Route::get('/loai-mon-an/{id}', [LoaiMonAnController::class, 'show']);
    Route::match(['put', 'post'], '/loai-mon-an/{id}', [LoaiMonAnController::class, 'update']);
    Route::delete('/loai-mon-an/{id}', [LoaiMonAnController::class, 'destroy']);
    
    // Routes MonAn (chỉ những thao tác cần xác thực)
    Route::post('/mon-an', [MonAnController::class, 'store']);
    Route::post('/mon-an/{id}', [MonAnController::class, 'update']);
    Route::match(['put', 'post'], '/mon-an/{id}', [MonAnController::class, 'update']);
    Route::delete('/mon-an/{id}', [MonAnController::class, 'destroy']);

    // Routes ThongTinDatBan
    Route::get('/dat-ban', [ThongTinDatBanController::class, 'index']);
    Route::post('/dat-ban', [ThongTinDatBanController::class, 'store']);
    Route::get('/dat-ban/{id}', [ThongTinDatBanController::class, 'show']);
    Route::match(['post', 'put'], '/dat-ban/{id}', [ThongTinDatBanController::class, 'update']);
    Route::delete('/dat-ban/{id}', [ThongTinDatBanController::class, 'destroy']);
    
    // Routes BangChiTietDatBan
    Route::post('/chi-tiet-dat-ban/gan-ban', [BangChiTietDatBanController::class, 'assignTable']);
    Route::post('/chi-tiet-dat-ban/huy-ban', [BangChiTietDatBanController::class, 'removeTable']);

    // Routes ChiTietDatMon
    Route::get('/chi-tiet-dat-mon', [ChiTietDatMonController::class, 'index']);
    Route::post('/chi-tiet-dat-mon/them-mon', [ChiTietDatMonController::class, 'store']);
    Route::post('/chi-tiet-dat-mon/cap-nhat', [ChiTietDatMonController::class, 'update']);
    Route::post('/chi-tiet-dat-mon/xoa-mon', [ChiTietDatMonController::class, 'destroy']);
    
    // Routes ThanhToan
    Route::get('/thanh-toan', [ThanhToanController::class, 'index']);
    Route::post('/thanh-toan', [ThanhToanController::class, 'store']);
    Route::get('/thanh-toan/{id}', [ThanhToanController::class, 'show']);
    Route::match(['post', 'put'], '/thanh-toan/{id}', [ThanhToanController::class, 'update']);
    Route::delete('/thanh-toan/{id}', [ThanhToanController::class, 'destroy']);

    // Routes DanhGia
    Route::get('/danh-gia', [DanhGiaController::class, 'index']);
    Route::post('/danh-gia', [DanhGiaController::class, 'store']);
    Route::get('/danh-gia/{id}', [DanhGiaController::class, 'show']);
    Route::post('/danh-gia/{id}', [DanhGiaController::class, 'update']);
    Route::delete('/danh-gia/{id}', [DanhGiaController::class, 'destroy']);
    Route::post('/danh-gia/{id}/tra-loi', [DanhGiaController::class, 'reply']);

    Route::get('/thong-bao', [ThongBaoController::class, 'index']);
    Route::get('/thong-bao/{id}', [ThongBaoController::class, 'show']);
    Route::post('/thong-bao/{id}/mark-read', [ThongBaoController::class, 'markAsRead']);
    Route::post('/thong-bao/mark-all-read', [ThongBaoController::class, 'markAllAsRead']);
    Route::delete('/thong-bao/{id}', [ThongBaoController::class, 'destroy']);
    
    // Routes xác nhận đặt bàn và thanh toán
    Route::post('/dat-ban/{id}/confirm', [ThongTinDatBanController::class, 'confirmBooking']);
    Route::post('/thanh-toan/{id}/send-confirmation', [ThanhToanController::class, 'sendConfirmation']);
});

// Routes cần xác thực và quyền admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // User management routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Thêm vào routes/api.php
Route::middleware(['auth:sanctum', 'admin'])->post('/thong-bao/create', [ThongBaoController::class, 'createNotification']);
// Thêm vào routes/api.php nếu chưa có
Route::middleware(['auth:sanctum'])->post('/thong-bao/create', [ThongBaoController::class, 'createNotification']);