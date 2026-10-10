<?php

namespace App\Models;

use App\Enums\AuditLogEventEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['auditable_type', 'auditable_id', 'user_id', 'event', 'old_values', 'new_values'])]
class AuditLog extends Model
{
    public const null UPDATED_AT = null;

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected function casts(): array
    {
        return [
            'event' => AuditLogEventEnum::class,
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }
}
