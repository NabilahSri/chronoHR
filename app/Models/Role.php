<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Guarded([])]
class Role extends Model
{
    use HasUuids;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_users')->withTimestamps();
    }
}
