<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $microPost1 = new MicroPost;
        $microPost1->setTitle('Kendrick Lamar');
        $microPost1->setText('The Modern King Kunta');
        $microPost1->setCreated(new DateTime);
        $manager->persist($microPost1);

        $microPost2 = new MicroPost;
        $microPost2->setTitle('Drake');
        $microPost2->setText('Certified Lover Boy');
        $microPost2->setCreated(new DateTime);
        $manager->persist($microPost2);

        $microPost3 = new MicroPost;
        $microPost3->setTitle('J Cole');
        $microPost3->setText('Nobody is Perfect');
        $microPost3->setCreated(new DateTime);
        $manager->persist($microPost3);

        $manager->flush();
    }
}
