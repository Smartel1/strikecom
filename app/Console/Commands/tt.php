<?php

namespace App\Console\Commands;

use App\Entities\Conflict;
use App\Entities\User;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Illuminate\Console\Command;
use LaravelDoctrine\ORM\Facades\EntityManager;

class tt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tt';

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
     * @return mixed
     */
    public function handle()
    {
        /** @var $em EntityManager*/
        $em = app('em');
/** @var User $user */
        $user = $em->find(User::class, 1636);

        dd(in_array('MODERATOR', $user->getRoles()));

        $em->flush();
    }
}
