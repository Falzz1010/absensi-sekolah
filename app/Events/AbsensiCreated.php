<?php

namespace App\Events;

use App\Models\Absensi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsensiCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Absensi $absensi) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('absensi'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->absensi->id,
            'murid_id' => $this->absensi->murid_id,
            'status' => $this->absensi->status,
            'kelas' => $this->absensi->kelas,
            'tanggal' => $this->absensi->tanggal,
        ];
    }
}
