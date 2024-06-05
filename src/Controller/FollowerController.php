<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FollowerController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(User $userToFollow, ManagerRegistry $doctrine, Request $request ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($userToFollow->getId() !== $currentUser->getId()) {
            $currentUser->follow($userToFollow);
            $doctrine->getManager()->flush();
        }

        
        return $this->redirect($request->headers->get('referer'));

    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(User $userToUnFollow, ManagerRegistry $doctrine, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($userToUnFollow->getId() !== $currentUser->getId()) {
            $currentUser->unfollow($userToUnFollow);
            $doctrine->getManager()->flush();
        }

        
        return $this->redirect($request->headers->get('referer'));
        
    }
}
