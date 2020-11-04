<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $image1 = new Image();
        $image1->setFileName('image1-hash123.jpg');
        $image1->setFileSize(100000);
        $image1->setCreatedAt((new \DateTime())->modify('-1 year'));
        $manager->persist($image1);

        $image2 = new Image();
        $image2->setFileName('image2-hash123.jpg');
        $image2->setFileSize(500000);
        $manager->persist($image2);

        $image3 = new Image();
        $image3->setFileName('image3-hash123.jpg');
        $image3->setFileSize(800000);
        $manager->persist($image3);

        $manager->flush();
    }
}
