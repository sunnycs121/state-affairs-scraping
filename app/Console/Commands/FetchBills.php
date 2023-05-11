<?php

namespace App\Console\Commands;

use App\Http\Controllers\LatestBillsController;
use Illuminate\Console\Command;

class FetchBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-bills {body}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches bills for given body (house or senate) and saves in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $body = strtolower($this->argument('body'));
        if(!in_array($body, ['house', 'senate'])) {
            echo "Invalid body given, possible options are 'house' or 'senate'.";
            exit;
        }
        $fetchBills = new LatestBillsController($body);
        $fetchStatus = $fetchBills->fetchLatestBills();
        print_r($fetchStatus);
        exit;
    }
}
