<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountVerified extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->role === 'Petani'
            ? $notifiable->petani->nama_petani
            : $notifiable->mitra->nama_mitra;

        return (new MailMessage)
            ->subject('Akun Anda Telah Aktif!')
            ->greeting("Halo, $name!")
            ->line('Selamat! Akun Anda telah diverifikasi oleh admin.')
            ->line('Sekarang Anda sudah bisa login dan menggunakan semua fitur di aplikasi ASUP Ciamis.')
            ->action('Login Sekarang', url('/login'))
            ->line('Terima kasih telah bergabung bersama kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Akun Anda telah berhasil diverifikasi oleh admin.',
            'type' => 'verification_success'
        ];
    }
}
