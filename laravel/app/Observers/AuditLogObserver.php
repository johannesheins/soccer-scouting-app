<?php

namespace App\Observers;

use App\Contracts\AuditLogInterface;
use App\Enums\AuditLogEventEnum;
use Illuminate\Support\Arr;

class AuditLogObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(AuditLogInterface $model): void
    {
        $this->log($model, AuditLogEventEnum::CREATED);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(AuditLogInterface $model): void
    {
        $this->log($model, AuditLogEventEnum::UPDATED);
    }

    /**
     * Handle the Model "deleting" event.
     * Pivot rows may be removed by a cascading delete, so the related ids are captured beforehand.
     */
    public function deleting(AuditLogInterface $model): void
    {
        $model->snapshotAuditedRelations();
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(AuditLogInterface $model): void
    {
        $this->log($model, AuditLogEventEnum::DELETED);
    }

    private function log(AuditLogInterface $model, AuditLogEventEnum $event): void
    {
        [$old, $new] = match ($event) {
            AuditLogEventEnum::CREATED => [null, $this->withoutHidden($model, $model->getAttributes())],
            AuditLogEventEnum::UPDATED => [
                $this->withoutHidden($model, $model->getPrevious()),
                $this->withoutHidden($model, $model->getChanges()),
            ],
            AuditLogEventEnum::DELETED => [
                [...$this->withoutHidden($model, $model->getAttributes()), ...$model->getAuditedRelationSnapshot()],
                null,
            ],
        };

        if ($event === AuditLogEventEnum::UPDATED && $new === []) {
            return;
        }

        $model->writeAuditLog($event, $old, $new);
    }

    /**
     * Hidden attributes (e.g. password hashes) must never end up in the audit log.
     */
    private function withoutHidden(AuditLogInterface $model, array $values): array
    {
        return Arr::except($values, $model->getHidden());
    }
}
