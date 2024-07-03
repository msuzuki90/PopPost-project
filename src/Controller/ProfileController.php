<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MicroPostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function show(User $user, Request $request, PaginatorInterface $paginator,
    MicroPostRepository $microPostRepository): Response
    {
        
        $queryBuilder = $microPostRepository->findByUserOrderedByDateDesc($this->getUser());

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // page number
            4 // limit per page
        );

        return $this->render('profile/show.html.twig', [
            'user'=>$user,
            'posts' => $pagination,
        ]);
    }

    #[Route('/profile/{id}/follows', name: 'app_profile_follows')]
    public function follows(User $user): Response
    {
        return $this->render('profile/follows.html.twig', [
            'user'=>$user
        ]);
    }

    #[Route('/profile/{id}/followers', name: 'app_profile_followers')]
    public function followers(User $user): Response
    {
        return $this->render('profile/followers.html.twig', [
            'user'=>$user
        ]);
    }
}
