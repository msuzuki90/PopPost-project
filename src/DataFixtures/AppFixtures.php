<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\Product;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ){
    }


    public function load(ObjectManager $manager): void
    {
        $user1= new User();
        $user1->setEmail('rapper@mail.com');
        $user1->setPassword(
            $this->userPasswordHasher->hashPassword($user1,'tupac')
        );
        $manager->persist($user1);

        $user2= new User();
        $user2->setEmail('rock@mail.com');
        $user2->setPassword(
            $this->userPasswordHasher->hashPassword($user2,'Metallica')
        );
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('reggae@mail.com');
        $user3->setPassword(
            $this->userPasswordHasher->hashPassword($user3,'Marley')
        );
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail('pop@mail.com');
        $user4->setPassword(
            $this->userPasswordHasher->hashPassword($user4,'Swift')
        );
        $manager->persist($user4);
        
        $microPost1 = new MicroPost;
        $microPost1->setTitle('Kendrick Lamar');
        $microPost1->setText('The Modern King Kunta');
        $microPost1->setCreated(new DateTime);
        $microPost1->setAuthor($user1);
        $manager->persist($microPost1);

        $microPost2 = new MicroPost;
        $microPost2->setTitle('Drake');
        $microPost2->setText('Certified Lover Boy');
        $microPost2->setCreated(new DateTime);
        $microPost2->setAuthor($user2);
        $manager->persist($microPost2);

        $microPost3 = new MicroPost;
        $microPost3->setTitle('J Cole');
        $microPost3->setText('Nobody is Perfect');
        $microPost3->setCreated(new DateTime);
        $microPost3->setAuthor($user3);
        $manager->persist($microPost3);

        $manager->flush();

        $product = new Product;
        $product->setName("Product 1");
        $product->setPicture("logo.png");
        $product->setPrice(93);
        $product->setPriceIdStripe("prod_QRT9s0KifeiLza");
        $product->setDescription("super description qui déchire");
        $product->setStock(10);

        $manager->persist($product);

        $manager->flush();

    }
}
