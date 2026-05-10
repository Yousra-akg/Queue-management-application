<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateSessionStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update session statuses based on the current date and time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessions = \App\Models\Session::all();
        foreach ($sessions as $session) {
            $session->updateStatusBasedOnTime();
        }

        $this->info('Session statuses updated successfully.');
    }
}
