<?php

namespace App\Enums;

enum AuditLogEventEnum: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
}
