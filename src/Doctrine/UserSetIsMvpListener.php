<?php

namespace App\Doctrine;

use App\Entity\User;
use Symfony\Component\String\LazyString;

class UserSetIsMvpListener
{
    public function postLoad(User $user)
    {
        $user->setIsMvp(strpos($user->getUsername(), 'cheese') !== false);


        $bio = LazyString::fromCallable(function () {
            // sleep(1);

            return 'test: ' . mt_rand(0, 100);
        });

        $user->setBio($bio);
    }
}
