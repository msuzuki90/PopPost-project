<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();
        
        $form = $this->createForm(
            UserProfileType::class, $userProfile
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $entityManager->persist($user);
            $entityManager->flush();

            //add Flash
            $this->addFlash('success','Nice, your user profile has been saved!');
            //redirect route
            return $this->redirectToRoute(
                'app_settings_profile'
            );

        }

        return $this->render(
            'settings_profile/profile.html.twig', 
        [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/settings/profile-image', name: 'app_settings_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage( 
        User $user, 
        Request $request, 
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
    ):Response
    //slugger Interface symfony tool: symfony will know what to inject when we type hint anything with this interface
    {
        $form = $this->createForm(ProfileImageType::class);
        /** @var User $user */
        //doc block to specify that the user variable would be of the type User
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //profileImage is the name we used in our ProfileImageType form
            $profileImageFile = $form->get('profileImage')->getData();

            if($profileImageFile){
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(), PATHINFO_FILENAME
                    //PHP docs: Returns information about a file path (in our case the original file name from the user)
                );
                $saveFileName = $slugger->slug($originalFileName);
                //symfony doc: safeFileName
                $newFileName = $saveFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();
                //uniqid() function : when users will start uploading files the very file will be overwritten all the time 
                //instead of creating a new id.
                //guessExtension() Symfony Doc: we will get the original extension of the file that was sent.
                
                // dd(
                //     $originalFileName,
                //     $saveFileName,
                //     $newFileName,
                // );

                try {
                    $profileImageFile->move(
                        $this->getParameter('profiles_directory'),
                        //configuration should be moved away from logic and code 
                        $newFileName
                    );
                }catch (FileException $e) {

                }
                $profile = $user->getUserProfile() ?? new UserProfile();
                $profile->setImage($newFileName);
                $entityManager->persist($user);
                $entityManager->flush();
                //addFlash under
                $this->addFlash('success', 'Nice new profile pic!');
                //php uploads file to a temporary directory. We want them moved to the destination directory
                return $this->redirectToRoute('app_settings_profile_image');
            }
        }



        return $this->render(
            'settings_profile/profile_image.html.twig',
            [ 
                'form' => $form->createView()
            ]
        );

    }


}
