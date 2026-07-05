<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;

#[Signature('refresh')]
#[Description('Refresh the tinker session')]
class RefreshTinkerCommand extends Command
{
    public function handle(): void
    {
        $this->line('<fg=yellow>Refreshing tinker...</>'.PHP_EOL);
        passthru('php artisan tinker');
    }
}
