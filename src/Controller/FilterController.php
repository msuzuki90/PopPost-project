<?php

namespace App\Controller;

use App\Repository\MicroPostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FilterController extends AbstractController
{

    #[Route('/filter', name: 'app_filter')]
    public function index(MicroPostRepository $posts, Request $request, PaginatorInterface $paginator): Response
    {
        // Retrieve the search query from the request
        $filter = $request->query->get('filter'); 
    
        $results = $paginator->paginate(
            $posts->findAllWithCommentsByFilter($filter),
            $request->query->getInt('page', 1),
            5
        );
    
        return $this->render('micro_post/index.html.twig', [
            'filter' => $filter,
            'posts' => $results,
        ]);
    }
}
