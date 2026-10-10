<?php

namespace App\Traits;

use App\Enums\AuditLogEventEnum;
use App\Models\AuditLog;
use App\Observers\AuditLogObserver;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    private array $auditedRelationSnapshot = [];

    public static function bootAuditable(): void
    {
        static::whenBooted(fn () => static::observe(AuditLogObserver::class));
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function writeAuditLog(AuditLogEventEnum $event, ?array $old, ?array $new): void
    {
        $this->auditLogs()->create([
            'user_id' => auth()->id(),
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function auditedRelations(): array
    {
        return [];
    }

    /**
     * Stores the related ids of the audited relations, e.g. before pivot rows are removed by a cascading delete.
     */
    public function snapshotAuditedRelations(): void
    {
        $this->auditedRelationSnapshot = [];

        foreach ($this->auditedRelations() as $relation) {
            $this->auditedRelationSnapshot[$relation] = $this->getAuditedRelatedIds($relation);
        }
    }

    public function getAuditedRelationSnapshot(): array
    {
        return $this->auditedRelationSnapshot;
    }

    /**
     * Syncs a BelongsToMany relation and logs the old and new ids if anything changed.
     */
    public function syncAudited(string $relation, array $ids): array
    {
        $old = $this->getAuditedRelatedIds($relation);

        $changes = $this->{$relation}()->sync($ids);

        if ($changes['attached'] !== [] || $changes['detached'] !== []) {
            $new = $this->getAuditedRelatedIds($relation);
            $this->writeAuditLog(AuditLogEventEnum::UPDATED, [$relation => $old], [$relation => $new]);
        }

        return $changes;
    }

    private function getAuditedRelatedIds(string $relation): array
    {
        return $this->{$relation}()->allRelatedIds()->sort()->values()->all();
    }
}
