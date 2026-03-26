<?php

use App\Console\Commands\MakeRepositoryCommand;
use App\Console\Commands\MakeServiceCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Register custom commands
Artisan::command('make:repository', function (MakeRepositoryCommand $command) {
    $command->setApplication($this->getApplication());
    $command->setLaravel(app());
    $this->runCommand($command);
})->describe('Create a new repository class');

Artisan::command('make:service', function (MakeServiceCommand $command) {
    $command->setApplication($this->getApplication());
    $command->setLaravel(app());
    $this->runCommand($command);
})->describe('Create a new service class');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
