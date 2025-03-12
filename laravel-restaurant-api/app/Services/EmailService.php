<?php

namespace App\Services;

use App\Mail\BookingConfirmation;
use App\Mail\PaymentConfirmation;
use App\Models\ThanhToan;
use App\Models\ThongTinDatBan;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Gửi email xác nhận đặt bàn
     */
    public function sendBookingConfirmation($bookingId)
    {
        try {
            $datBan = ThongTinDatBan::with(['user', 'chiTietDatBans.ban.khuVuc'])->find($bookingId);
            
            if (!$datBan || !$datBan->user || !$datBan->user->Email) {
                return false;
            }
            
            Mail::to($datBan->user->Email)->send(new BookingConfirmation($datBan));
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email xác nhận thanh toán
     */
    public function sendPaymentConfirmation($paymentId)
    {
        try {
            $thanhToan = ThanhToan::with(['thongTinDatBan.user', 'thongTinDatBan.chiTietDatMons.monAn'])->find($paymentId);
            
            if (!$thanhToan || !$thanhToan->thongTinDatBan || !$thanhToan->thongTinDatBan->user || !$thanhToan->thongTinDatBan->user->Email) {
                return false;
            }
            
            Mail::to($thanhToan->thongTinDatBan->user->Email)->send(new PaymentConfirmation($thanhToan));
            
            return true;
        } catch (\Exception $e) {      
            \Illuminate\Support\Facades\Log::error('Error sending payment confirmation email: ' . $e->getMessage());
            return false;
        }
    }
}