<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ReadOrderExcel;


class ReadOrderExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:read_order_excel {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Begin read excel order");
        $path = $this->argument('path');
        // execInForeground('C:\xampp\php\php C:\xampp\htdocs\cargomall\BackendServices\artisan queue:work --timeout=1000 --queue=read_order_excel');
        ReadOrderExcel::dispatch($path)->onQueue('read_order_excel');
        
        
    }
}
