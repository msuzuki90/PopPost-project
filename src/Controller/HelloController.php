<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Repository\UserProfileRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{

    private array $messages = [
        ['message' => 'Hello', 'created' => '2023/01/03'],
        ['message' => 'Hi', 'created' => '2024/02/01'],
        ['message' => 'Bye!', 'created' => '2024/03/02']
    ];


    #[Route('/', name: 'app_index')]
    public function index(
        UserProfileRepository $profiles, 
        EntityManagerInterface $entityManager,
        MicroPost $post,
        Comment $comment, 
        MicroPostRepository $posts, 
        CommentRepository $comments
        ): Response
    {

        // $post = new MicroPost();
        // $post->setTitle('Test controller');
        // $post->setText('This is a Test');
        // $post->setCreated(new DateTime());

        //$post = $posts->find(24);
        //$post->getComments()->count();

        //$comment = new Comment();
        //$comment->setText('Hey this test is kinda annoying');
        //$comment->setPost($post);

        //$post->addComment($comment);
        //dd($post);
        //$entityManager->persist($comment);

        //$entityManager->flush();

        //$comments->add($comment, true);

        // TO DELETE:
        // $post = $posts->find(19);
        // $entityManager->remove($post);
        // $entityManager->flush();

        // $user = new User();
        // $user->setEmail('email@email.com');
        // $user->setPassword('Tunisia');


        // $profile = new UserProfile();
        // $profile->setUser($user);
        // $entityManager->persist($profile);
        // $entityManager->flush();

        // $profile = $profiles->find(1);
        // $entityManager->remove($profile);
        // $entityManager->flush();


        return $this->render(
            'hello/index.html.twig',[
                'messages'=>$this->messages,
                'limit'=>3
            ]
        );
        //return new Response(implode(',', array_slice($this->messages, 0,$limit)));
    }


    #[Route('/messages/{id<\d+>}', name: 'app_show_one')]
    public function showOne($id): Response
    {
        return $this->render(
            'hello/show_one.html.twig', [
                'message'=> $this->messages[$id]
            ]
        );
        //return new Response($this->messages[$id]);
    } 


}
