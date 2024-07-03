<?php

namespace App\Controller;

use App\Repository\MicroPostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(MicroPostRepository $posts,
        Request $request,
        PaginatorInterface $paginator): Response
    {

        // Retrieve the search query from the request
        $query = $request->query->get('query');

        $results = $posts->findAllWithCommentsAndSearch($query);

        $pagination = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('micro_post/index.html.twig', [
            'query' => $query,
            'posts' => $pagination,
        ]);

    }
}
