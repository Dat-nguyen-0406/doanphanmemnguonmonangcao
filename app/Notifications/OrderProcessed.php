<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderProcessed extends Notification
{
    use Queueable;

    public $order; // Biến lưu thông tin đơn hàng

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Xác định kênh gửi thông báo.
     */
    public function via($notifiable): array
    {
        // Gửi qua Email và lưu vào Database của hệ thống
        return ['mail', 'database'];
    }

    /**
     * Nội dung Email xác nhận đơn hàng.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Xác nhận đơn hàng AEON Mall #' . $this->order->id)
            ->greeting('Chào Đạt!')
            ->line('Cảm ơn bạn đã tin tưởng và mua sắm tại hệ thống AEON Mall.')
            ->line('Thông tin đơn hàng của bạn:')
            ->line('- Mã đơn hàng: #' . $this->order->id)
            ->line('- Tổng thanh toán: ' . number_format($this->order->total_amount) . 'đ')
            ->line('- Trạng thái: Đã thanh toán qua VNPay')
            ->action('Xem chi tiết đơn hàng', url('/orders/' . $this->order->id))
            ->line('Đơn hàng của bạn đang được xử lý và sẽ sớm được giao.')
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    /**
     * Dữ liệu lưu vào bảng 'notifications' trong database.
     */
    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'message' => 'Đơn hàng #' . $this->order->id . ' đã thanh toán thành công.'
        ];
    }
}