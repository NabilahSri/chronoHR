<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
class LeaveRequest extends Model
{
    use HasUuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User|string $approver): void
    {
        $approverId = $approver instanceof User ? $approver->id : $approver;

        $this->update([
            'status' => 'APPROVED',
            'approved_by' => $approverId,
        ]);

        $period = \Carbon\CarbonPeriod::create($this->start_date, $this->end_date);

        foreach ($period as $date) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $this->user_id,
                    'date' => $date->toDateString(),
                ],
                [
                    'status' => $this->type,
                    'method' => 'FORM',
                    'is_late' => false,
                    'overtime_hours' => 0,
                ]
            );
        }
    }

    public function reject(User|string $approver, string $reason): void
    {
        $approverId = $approver instanceof User ? $approver->id : $approver;

        $this->update([
            'status' => 'REJECTED',
            'approved_by' => $approverId,
            'rejection_reason' => $reason,
        ]);
    }
}
