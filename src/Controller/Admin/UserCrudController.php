<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\BanUserType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [

            EmailField::new('email'),
            BooleanField::new('isVerified'),
            DateTimeField::new('banHammer')->hideOnForm(),
            ChoiceField::new('roles')->setChoices([
                'USER' => 'ROLE_COMMENTER',
                'ADMIN' => 'ROLE_EDITOR',
            ])->allowMultipleChoices(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $banUser = Action::new('ban', 'Ban', 'fas fa-gavel')
            ->linkToRoute('admin_ban_hammer', function (User $user): array {
                return ['id' => $user->getId()];
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $banUser)
            ->add(Crud::PAGE_DETAIL, $banUser);
    }
}





    


   
    

