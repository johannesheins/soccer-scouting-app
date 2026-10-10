<?php

namespace App\Contracts;

use App\Enums\AuditLogEventEnum;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface AuditLogInterface
{
    public function auditLogs(): MorphMany;

    public function writeAuditLog(AuditLogEventEnum $event, ?array $old, ?array $new): void;

    /**
     * BelongsToMany relations whose related ids are included in the "deleted" log.
     */
    public function auditedRelations(): array;

    public function snapshotAuditedRelations(): void;

    public function getAuditedRelationSnapshot(): array;
}
