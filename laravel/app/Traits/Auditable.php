<?php

namespace App\Traits;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait Auditable
{
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function createSnapshot(): array
    {
        $snapshot = Arr::except($this->getAttributes() ?? [], $this->getHidden());

        if(isset($this->auditedRelations)){
            $this->load($this->auditedRelations);

            foreach($this->auditedRelations as $relation){
                $snapshot[$relation] = $this->{$relation}()->allRelatedIds()->sort()->values()->all();
            }
        }

        return $snapshot;
    }

    public function saveLog(AuditEvent $event, ?array $old, ?array $new): void
    {
        $this->auditLogs()->create([
            'user_id' => auth()->id(),
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
