<?php

namespace App\Models;

use App\Enums\AuditEvent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

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
            'event' => AuditEvent::class,
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function changedFields(): array
    {
        $old = $this->old_values;
        $new = $this->new_values;
        if ($old === null || $new === null) {
            return [];
        }

        $changes = [];
        foreach (Arr::except($new, ['updated_at']) as $field => $value) {
            if (($old[$field] ?? null) != $value) {
                $changes[] = $field;
            }
        }

        return $changes;
    }
}
