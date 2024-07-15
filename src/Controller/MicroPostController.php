<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $postRepo, 
        PaginatorInterface $paginator, 
        Request $request): Response
    {

        $sort = $request->query->get('sort', 'desc');
        $queryBuilder = $postRepo->findAllWithComments();

        if ($sort === 'asc') {
            $queryBuilder->orderBy('p.Created', 'ASC');
        } else {
            $queryBuilder->orderBy('p.Created', 'DESC');
        }
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('micro_post/index.html.twig', [
            'posts' => $pagination,
            'filter'=>$sort,
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
            'isCommentPage' => false 
        ]);
    }


    #[Route('/micro-post/add', name: 'app_micro_post_add', priority:2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_VERIFIED')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response {
        $post = new MicroPost();
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());

            $commentImageFile = $form->get('commentImage')->getData();
            if ($commentImageFile) {
                $originalFileName = pathinfo($commentImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $commentImageFile->guessExtension();

                try {
                    $commentImageFile->move($this->getParameter('comment_directory'), $newFileName);
                    $post->setPicture($newFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image');
                    return $this->redirectToRoute('app_micro_post_add');
                }
            }

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Your PopPost has been created... Check it out!');

            return $this->redirectToRoute('app_micro_post_show', ['post' => $post->getId()]);
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            
            $commentImageFile = $form->get('commentImage')->getData();
            if ($commentImageFile) {
                $originalFileName = pathinfo($commentImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $commentImageFile->guessExtension();
    
                try {
                    $commentImageFile->move($this->getParameter('comment_directory'), $newFileName);
                    $post->setPicture($newFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image');
                    return $this->redirectToRoute('app_micro_post_edit', ['post' => $post->getId()]);
                }
            }
    
            $entityManager->persist($post);
            $entityManager->flush();
    
            $this->addFlash('success','Something had to change? Your PopPost has been updated!');
            return $this->redirectToRoute('app_micro_post');
        }
    
        return $this->render('micro_post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }
    
    
    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_VERIFIED')]
    public function addComment(
        MicroPost $post, 
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CommentType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            // Add Flash Message
            $this->addFlash('success', 'Express Yourself! Your Comment has been added!');

            // Redirect
            return $this->redirectToRoute(
                'app_micro_post_show',
                ['post' => $post->getId()]
            );
        }

        return $this->render('micro_post/comment.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'isCommentPage' => true  // Add this parameter to indicate it's the comment page
        ]);
    }


    #[Route('/micro-post/{post}/delete', name: 'app_micro_post_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_VERIFIED')]
    //#[IsGranted('ROLE_COMMENTER')]
    public function delete(MicroPost $post, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your post has been deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token. Deletion failed.');
        }

        return $this->redirectToRoute('app_micro_post');
    }

    

}
