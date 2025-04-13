<?php

namespace App\Services;

use App\Models\ThanhToan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoMoPaymentService
{
    protected $endpoint;
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $redirectUrl;
    protected $ipnUrl;

    public function __construct()
    {
        $this->endpoint = config('services.momo.endpoint');
        $this->partnerCode = config('services.momo.partner_code');
        $this->accessKey = config('services.momo.access_key');
        $this->secretKey = config('services.momo.secret_key');
        $this->redirectUrl = config('services.momo.redirect_url');
        $this->ipnUrl = config('services.momo.ipn_url');
    }

    public function createPayment(ThanhToan $thanhToan)
{
    try {
        $orderId = time() . "_" . $thanhToan->ID_ThanhToan;
        $amount = (int)$thanhToan->SoLuong;
        $orderInfo = "Thanh toán đơn đặt bàn #" . $thanhToan->ID_ThongTinDatBan;
        $requestId = (string)Str::uuid();
        $extraData = json_encode(['ID_ThanhToan' => $thanhToan->ID_ThanhToan]);

        // Log thông tin trước khi gửi
        \Illuminate\Support\Facades\Log::info('MoMo Payment Request Data', [
            'orderId' => $orderId,
            'amount' => $amount,
            'orderInfo' => $orderInfo
        ]);

        $rawHash = "accessKey=" . $this->accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $this->ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $this->partnerCode .
            "&redirectUrl=" . $this->redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=captureWallet";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $requestData = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Nhà hàng',
            'storeId' => $this->partnerCode,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature,
        ];

        // Log dữ liệu request đầy đủ
        \Illuminate\Support\Facades\Log::info('MoMo Full Request', $requestData);

        $response = Http::post($this->endpoint, $requestData);
        $responseData = $response->json();

        // Log response
        \Illuminate\Support\Facades\Log::info('MoMo Response', $responseData);

        if ($response->successful() && isset($responseData['payUrl'])) {
            // Cập nhật thông tin thanh toán MoMo
            $thanhToan->update([
                'MoMo_RequestId' => $requestId,
                'MoMo_OrderId' => $orderId,
                'MoMo_PaymentUrl' => $responseData['payUrl'],
                'PhuongThucThanhToan' => 4, // 4: MoMo
                'NgayCapNhap' => now()
            ]);

            return [
                'success' => true,
                'paymentUrl' => $responseData['payUrl'],
                'qrCodeUrl' => $responseData['qrCodeUrl'] ?? null // MoMo API có thể trả về qrCodeUrl
            ];
        }

        \Illuminate\Support\Facades\Log::error('MoMo Error Response', $responseData);

        return [
            'success' => false,
            'message' => $responseData['message'] ?? 'Lỗi khi tạo thanh toán MoMo'
        ];
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('MoMo Exception: ' . $e->getMessage());
        
        return [
            'success' => false,
            'message' => 'Đã xảy ra lỗi trong quá trình kết nối với MoMo: ' . $e->getMessage()
        ];
    }
}

    public function verifyPayment($requestData)
    {
        $accessKey = $requestData['accessKey'] ?? '';
        $amount = $requestData['amount'] ?? '';
        $extraData = $requestData['extraData'] ?? '';
        $message = $requestData['message'] ?? '';
        $orderId = $requestData['orderId'] ?? '';
        $orderInfo = $requestData['orderInfo'] ?? '';
        $orderType = $requestData['orderType'] ?? '';
        $partnerCode = $requestData['partnerCode'] ?? '';
        $payType = $requestData['payType'] ?? '';
        $requestId = $requestData['requestId'] ?? '';
        $responseTime = $requestData['responseTime'] ?? '';
        $resultCode = $requestData['resultCode'] ?? '';
        $transId = $requestData['transId'] ?? '';
        $signature = $requestData['signature'] ?? '';

        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&message=" . $message .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType .
            "&partnerCode=" . $partnerCode .
            "&payType=" . $payType .
            "&requestId=" . $requestId .
            "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode .
            "&transId=" . $transId;

        $calculatedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);

        if ($signature !== $calculatedSignature) {
            return [
                'success' => false,
                'message' => 'Invalid signature'
            ];
        }

        if ($resultCode == '0') {
            // Thanh toán thành công
            $extraDataDecoded = json_decode($extraData, true);
            $idThanhToan = $extraDataDecoded['ID_ThanhToan'] ?? null;

            if ($idThanhToan) {
                $thanhToan = ThanhToan::find($idThanhToan);
                if ($thanhToan) {
                    $thanhToan->update([
                        'TrangThaiThanhToan' => 1, // 1: Đã thanh toán
                        'NgayThanhToan' => now(),
                        'MaGiaoDich' => $transId,
                        'MoMo_TransId' => $transId,
                        'MoMo_ResultCode' => $resultCode,
                        'MoMo_Message' => $message,
                        'NgayCapNhap' => now()
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Thanh toán thành công',
                        'data' => $thanhToan
                    ];
                }
            }
        }

        return [
            'success' => false,
            'message' => 'Thanh toán thất bại',
            'resultCode' => $resultCode,
            'resultMessage' => $message
        ];
    }
}