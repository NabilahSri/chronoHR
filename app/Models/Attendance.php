<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
class Attendance extends Model
{
    use HasUuids;

    protected $appends = ['face_image_url', 'checkout_face_image_url'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_late' => 'boolean',
            'overtime_hours' => 'integer',
        ];
    }

    public function getFaceImageUrlAttribute(): ?string
    {
        return $this->face_image_path
            ? url('storage/' . ltrim($this->face_image_path, '/'))
            : null;
    }

    public function getCheckoutFaceImageUrlAttribute(): ?string
    {
        return $this->checkout_face_image_path
            ? url('storage/' . ltrim($this->checkout_face_image_path, '/'))
            : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
