<?php

namespace App\Traits;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
                $snapshot[$relation] = $this->snapshotRelation($relation);
            }
        }

        return $snapshot;
    }

    private function snapshotRelation(string $relation): array
    {
        $query = $this->{$relation}();

        if($query instanceof BelongsToMany){
            return $query->allRelatedIds()->sort()->values()->all();
        }

        $except = [$query->getForeignKeyName(), 'created_at', 'updated_at'];
        $snapshot = [];
        foreach($this->{$relation} as $related){
            $snapshot[] = Arr::except($related->getAttributes(), [$related->getKeyName(), ...$except]);
        }

        usort($snapshot, fn(array $a, array $b) => json_encode($a) <=> json_encode($b));

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
