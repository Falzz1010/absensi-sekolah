<?php

namespace App\Events;

use App\Models\Absensi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QrCodeScanned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Absensi $absensi,
        public string $muridName,
        public string $status
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('absensi'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'murid_name' => $this->muridName,
            'status' => $this->status,
            'kelas' => $this->absensi->kelas,
            'tanggal' => $this->absensi->tanggal,
            'waktu' => now()->format('H:i:s'),
        ];
    }
}
