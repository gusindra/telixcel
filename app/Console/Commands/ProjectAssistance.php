<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProjectAssistance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assistance:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assist project base on the contract';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $contract = Contract::where('status', 'approved')->whereDate('expired_at', '<=', Carbon::now())->get('id');
        $this->info("Found : {$contract->count()}!");
        $this->comment($contract);
        Log::debug("hit project assistance");
        return 'hit handle project';
    }
}
