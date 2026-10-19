<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Guarded([])]
class Device extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'last_ping_at' => 'datetime',
        ];
    }

    public function isOnline(): bool
    {
        if ($this->status === 'MAINTENANCE') {
            return false;
        }

        // ponytail: 5-minute timeout threshold; move to Setting if dynamic heartbeat timeout needed
        return $this->last_ping_at !== null && $this->last_ping_at->greaterThanOrEqualTo(now()->subMinutes(5));
    }

    public function toggleMaintenance(): void
    {
        if ($this->status === 'MAINTENANCE') {
            $this->update([
                'status' => 'ONLINE',
                'mode' => 'ATTENDANCE',
            ]);
        } else {
            $this->update([
                'status' => 'MAINTENANCE',
                'mode' => 'MAINTENANCE',
            ]);
        }
    }

    public function setMode(string $mode): void
    {
        $status = ($mode === 'MAINTENANCE') ? 'MAINTENANCE' : 'ONLINE';

        $this->update([
            'mode' => $mode,
            'status' => $status,
        ]);
    }

    public function recordHeartbeat(?string $ipAddress = null): void
    {
        $payload = ['last_ping_at' => now()];

        if ($ipAddress) {
            $payload['ip_address'] = $ipAddress;
        }

        if ($this->status !== 'MAINTENANCE') {
            $payload['status'] = 'ONLINE';
        }

        $this->update($payload);
    }
}
