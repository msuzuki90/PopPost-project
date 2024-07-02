<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BanHammerController extends AbstractController
{
    #[Route('/admin/ban/{id}', name: 'admin_ban_hammer')]
    public function banUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $banDuration = new \DateInterval('P7D'); // Ban duration of 7 days
        $banExpiresAt = (new \DateTime())->add($banDuration);
        $user->setBanHammer($banExpiresAt);
        
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'User has been banned until ' . $banExpiresAt->format('Y-m-d H:i:s'));

        return $this->redirectToRoute('admin');
    }
}
