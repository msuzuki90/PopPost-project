<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts, EntityManagerInterface $entityManager): Response
    {

        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAll(),
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority:2)]
    public function add(MicroPost $post, Request $request, MicroPostRepository $posts, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $post->setCreated(new DateTime());
            $entityManager->persist($post);
            $entityManager->flush();

            //add Flash Message
            $this->addFlash('success','Your PopPost has been created... Check it out!');
            //Redirect
            return $this->redirectToRoute(
                'app_micro_post'
            );

        }
        
        return $this->render('micro_post/add.html.twig',
        [
            'form'=> $form
        ]);
            
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $posts, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $entityManager->persist($post);
            $entityManager->flush();

            //add Flash Message
            $this->addFlash('success','Something had to change? Your PopPost has been updated!');
            //Redirect
            return $this->redirectToRoute(
                'app_micro_post'
            );

        }
        
        return $this->render('micro_post/edit.html.twig',
        [
            'form'=> $form
        ]);
            
    }


}
