<?php

namespace App\CustomSchema;

use App\Enums\EvaluationTypes;
use Illuminate\Support\Facades\Schema;

class EvaluationSchema extends Schema
{
    public static function create(string $table, \Closure $callback): void
    {
        foreach(EvaluationTypes::cases() as $type) {
            $t = "{$type->value}_{$table}";
            parent::create($t, $callback);
        }
    }

    public static function table(string $table, \Closure $callback): void
    {
        foreach(EvaluationTypes::cases() as $type) {
            $t = "{$type->value}_{$table}";
            parent::table($t, $callback);
        }
    }

    public static function drop(string $table): void
    {
        foreach(EvaluationTypes::cases() as $type) {
            $t = "{$type->value}_{$table}";
            parent::drop($t);
        }
    }
}
