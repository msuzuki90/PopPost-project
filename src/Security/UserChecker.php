<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface{

    /** 
     *@param User $user 
    */

    public function checkPreAuth(UserInterface $user)
    {
        if (null === $user->getBanHammer()) {
            return;

        }

        $now = new DateTime();

        if($now < $user->getBanHammer()){
            throw new AccessDeniedHttpException('This User is Banned!');
        }
    }

    /** 
     *@param User $user 
    */
    public function checkPostAuth(UserInterface $user)
    {
    }
}


