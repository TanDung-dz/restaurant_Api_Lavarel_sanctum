<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ThanhToan;
use App\Services\MoMoPaymentService;
use Illuminate\Http\Request;

class MoMoController extends Controller
{
    protected $momoService;

    public function checkMoMoConfig()
    {
        return response()->json([
            'endpoint' => config('services.momo.endpoint'),
            'redirect_url' => config('services.momo.redirect_url'),
            'ipn_url' => config('services.momo.ipn_url'),
            'partner_code_length' => strlen(config('services.momo.partner_code')),
            'access_key_length' => strlen(config('services.momo.access_key')),
            'secret_key_length' => strlen(config('services.momo.secret_key')),
        ]);
    }
    public function __construct(MoMoPaymentService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * Khởi tạo thanh toán qua MoMo
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'id_thanh_toan' => 'required|exists:ThanhToan,ID_ThanhToan',
        ]);

        $thanhToan = ThanhToan::find($request->id_thanh_toan);
        
        if (!$thanhToan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ], 404);
        }

        // Kiểm tra nếu đã thanh toán
        if ($thanhToan->TrangThaiThanhToan == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn này đã được thanh toán'
            ], 400);
        }

        // Tạo thanh toán MoMo
        $result = $this->momoService->createPayment($thanhToan);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo thanh toán MoMo thành công',
                'data' => [
                    'paymentUrl' => $result['paymentUrl']
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Xử lý IPN (Instant Payment Notification) từ MoMo
     */
    public function handleIpn(Request $request)
    {
        $result = $this->momoService->verifyPayment($request->all());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus($id)
    {
        $thanhToan = ThanhToan::with('thongTinDatBan')
            ->find($id);
        
        if (!$thanhToan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin thanh toán'
            ], 404);
        }
    
        // Đảm bảo cấu trúc response phù hợp với FE
        return response()->json([
            'success' => true,
            'message' => 'Thông tin trạng thái thanh toán',
            'data' => [
                'id_thanh_toan' => $thanhToan->ID_ThanhToan,
                'id_thongtin_datban' => $thanhToan->ID_ThongTinDatBan,
                'so_luong' => $thanhToan->SoLuong,
                'phuong_thuc_thanh_toan' => $thanhToan->PhuongThucThanhToan,
                'trang_thai_thanh_toan' => $thanhToan->TrangThaiThanhToan,
                'ngay_thanh_toan' => $thanhToan->NgayThanhToan,
                'ma_giao_dich' => $thanhToan->MaGiaoDich,
                'momo_result_code' => $thanhToan->MoMo_ResultCode,
                'momo_message' => $thanhToan->MoMo_Message
            ]
        ]);
    }
}