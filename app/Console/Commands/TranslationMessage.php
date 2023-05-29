<?php

namespace App\Console\Commands;

use App\Events\SendMessage;
use Illuminate\Console\Command;

class TranslationMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:messages {id} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fire event';

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
       broadcast(new SendMessage($this->argument('message'), $this->argument('id')));
    }
}
