<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class storageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish storage to public';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            app()->make('files')->link(storage_path('app/public'), $this->public_path('storage'));
            echo "link file to public";
        } catch (\Exception $e) {
            echo "Fail : ".$e->getMessage();
        }
    }

    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}
