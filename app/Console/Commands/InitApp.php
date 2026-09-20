<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:init {--force : Skip the confirmation prompt}')]
#[Description('Initialize the application by running the setup command chain')]
class InitApp extends Command
{
    /**
     * Commands run in order, each with its own arguments.
     *
     * @var list<array{command: string, description: string, arguments: array<string, mixed>}>
     */
    private array $steps = [
        [
            'command' => 'migrate:fresh',
            'description' => 'Refreshing migrations',
            'arguments' => ['--force' => true],
        ],
        [
            'command' => 'buildings:create',
            'description' => 'Creating buildings',
            'arguments' => [],
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToContinue()) {
            $this->components->warn('Aborted. Nothing was changed.');

            return self::FAILURE;
        }

        foreach ($this->steps as $step) {
            $this->components->info($step['description']);

            $exitCode = $this->call($step['command'], $step['arguments']);

            if ($exitCode !== self::SUCCESS) {
                $this->components->error("Step failed: {$step['command']}");

                return $exitCode;
            }
        }

        $this->components->info('Application initialized.');

        return self::SUCCESS;
    }

    /**
     * Refreshing migrations drops every table, so ask first unless forced.
     */
    private function confirmToContinue(): bool
    {
        if ($this->option('force')) {
            return true;
        }

        return $this->confirm('This drops all tables and re-runs migrations. Continue?', false);
    }
}
