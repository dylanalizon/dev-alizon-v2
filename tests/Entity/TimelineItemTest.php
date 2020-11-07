<?php

namespace App\Tests\Entity;

use App\Entity\TimelineItem;
use PHPUnit\Framework\TestCase;

class TimelineItemTest extends TestCase
{
    public function testTimelineItem(): void
    {
        $title = 'title';
        $description = 'description';
        $position = 1;
        $subtitle = 'subtitle';
        $image = null;
        $timelineItem = new TimelineItem();
        $timelineItem->setTitle($title);
        $timelineItem->setDescription($description);
        $timelineItem->setPosition($position);
        $timelineItem->setSubtitle($subtitle);
        $timelineItem->setImage($image);
        $this->assertNull($timelineItem->getId());
        $this->assertSame($title, $timelineItem->getTitle());
        $this->assertSame($description, $timelineItem->getDescription());
        $this->assertSame($position, $timelineItem->getPosition());
        $this->assertSame($subtitle, $timelineItem->getSubtitle());
        $this->assertSame($image, $timelineItem->getImage());
        $this->assertSame($title, (string) $timelineItem);
    }
}
