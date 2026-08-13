<?php

namespace Modules\Attendance\Console;

use Illuminate\Console\Command;
use Modules\Attendance\Jobs\MarkAbsentEmployeesJob;

class MarkAbsentEmployeesCommand extends Command
{
    protected $signature = 'attendance:mark-absent';

    protected $description = 'Automatically mark absent or on-leave employees for today';

    public function handle(): int
    {
        MarkAbsentEmployeesJob::dispatch();

        $this->info('Daily attendance check job dispatched successfully.');

        return Command::SUCCESS;
    }
}