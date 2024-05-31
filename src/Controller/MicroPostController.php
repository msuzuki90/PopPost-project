<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts, EntityManagerInterface $entityManager): Response
    {

        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments(),
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(
        MicroPost $post,
        Request $request,
        MicroPostRepository $posts, 
        EntityManagerInterface $entityManager
    ): Response
    {
        // $this->denyAccessUnlessGranted(
        //     'PUBLIC_ACCESS'
        // );
        
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $post->setAuthor($this->getUser());
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
    #[IsGranted(MicroPost::EDIT, 'post')]
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
            'form'=> $form,
            'post'=> $post
        ]);
            
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(
        MicroPost $post, 
        Request $request, 
        CommentRepository $comments, 
        EntityManagerInterface $entityManager): Response
    {
        // $this->denyAccessUnlessGranted(
        //     'IS_AUTHENTICATED_FULLY'
        // );
        $form = $this->createForm(CommentType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            //add Flash Message
            $this->addFlash('success','Express Yourself! Your Comment has been added!');
            //Redirect
            return $this->redirectToRoute(
                'app_micro_post_show',
                ['post'=>$post->getId()]
            );

        }
        
        return $this->render('micro_post/comment.html.twig',
        [
            'form'=> $form,
            'post'=>$post
        ]);
            
    }


}
