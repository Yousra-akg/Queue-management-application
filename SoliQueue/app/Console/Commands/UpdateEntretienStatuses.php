<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateEntretienStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entretiens:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update entretien statuses based on the current date and time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entretiens = \App\Models\Entretien::all();
        foreach ($entretiens as $entretien) {
            $entretien->updateStatusBasedOnTime();
        }

        $this->info('Entretien statuses updated successfully.');
    }
}

