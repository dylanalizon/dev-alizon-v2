<?php

namespace App\DataFixtures;

use App\Entity\TimelineItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TimelineItemFixtures extends Fixture
{
    use Fakerable;

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; ++$i) {
            $timeLineItem = new TimelineItem();
            $timeLineItem->setTitle($this->faker->city);
            $timeLineItem->setSubtitle($this->faker->sentence);
            $timeLineItem->setDescription($this->faker->paragraph);
            $manager->persist($timeLineItem);
        }

        $manager->flush();
    }
}
