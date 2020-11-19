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
            $file = $this->public_path('storage');
            if(is_dir($file)){
                app()->make('files')->deleteDirectory($file, true);
                rmdir($file);
            }
            $publicPath = storage_path('app/public');
            if(is_dir($publicPath)){
                app()->make('files')->link($publicPath, $file);
                echo "link file to public";
            }else{
                echo "Public File Not Found";
            }
        } catch (\Exception $e) {
            echo "Fail : ".$e->getMessage();
        }
    }

    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}
