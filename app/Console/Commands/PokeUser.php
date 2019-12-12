<?php

namespace App\Console\Commands;

use App\Notifications\JustPoking;
use App\User;
use Illuminate\Console\Command;

class PokeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poke:user {user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poke a given user by his/her id';

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
        $user = User::findOrFail($this->argument('user')); 
        $user->notify(new JustPoking());

        $this.info('You have just poked ' . $user->name);
    }
}
