<?php

namespace App\Console\Commands;

use App\Models\Building;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use ReflectionMethod;

#[Signature('app:create-formula-prompt')]
#[Description('Command description')]
class CreateFormulaPrompt extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Create formula to calc best building in current period or update exists');
        $this->info('In start we have 0 buildings. 0 Happy level & 0 Attractiveness level');
        $this->info('Keep levels more then 0');
        $this->info('Earn as much money as possible and use it to build or upgrade buildings. The building limit is 100.');
        $this->info('Result of your work formula');
        $this->info('Bellow json of building list');
        $this->info('```');
        $this->info(json_encode(Building::$buildings));
        $this->info('```');
        $this->info('How calc upgrade price');
        $this->info('```');
        $this->info($this->getMethodCode(Building::class, 'getUpgradePrice'));
        $this->info('```');
        $this->info('How calc upgrade time in seconds');
        $this->info('```');
        $this->info($this->getMethodCode(Building::class, 'getUpgradeTime'));
        $this->info('```');
        $this->info('How calc income');
        $this->info('```');
        $this->info($this->getMethodCode(Building::class, 'getIncome'));
        $this->info('```');

    }

    function getMethodCode(string $class, string $method): string
    {
        // 1. Reflect the class method
        $reflection = new \ReflectionMethod($class, $method);

        // 2. Get the file path and the boundaries of the method
        $fileName = $reflection->getFileName();
        $startLine = $reflection->getStartLine() - 1; // 0-indexed adjustment
        $endLine = $reflection->getEndLine();
        $length = $endLine - $startLine;

        // 3. Read the file lines and slice out the method
        $source = file($fileName);
        $methodLines = array_slice($source, $startLine, $length);

        // 4. Join the lines back together
        return implode("", $methodLines);
    }
}
