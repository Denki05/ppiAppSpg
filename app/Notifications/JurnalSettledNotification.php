<?php

namespace App\Notifications;

use App\Models\Penjualan\SalesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JurnalSettledNotification extends Notification
{
    use Queueable;

    protected $jurnal;

    /**
     * Buat instance notifikasi baru.
     *
     * @param  \App\Models\Penjualan\SalesOrder  $jurnal
     * @return void
     */
    public function __construct(SalesOrder $jurnal)
    {
        $this->jurnal = $jurnal;
    }

    /**
     * Menentukan saluran pengiriman notifikasi.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }
    
    /**
     * Menyusun notifikasi untuk saluran lain (jika ada).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'jurnal_id' => $this->jurnal->id,
            'jurnal_code' => $this->jurnal->code,
            'status' => $this->jurnal->status,
            'message' => 'Jurnal ' . $this->jurnal->code . ' telah di proses settel oleh sistem.',
            'tanggal_order' => $this->jurnal->tanggal_order,
            'settel_by' => $this->so->settel_by ?? null,
        ];
    }
}