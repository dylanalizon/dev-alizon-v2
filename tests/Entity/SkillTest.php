<?php

namespace App\Tests\Entity;

use App\Entity\Skill;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class SkillTest extends TestCase
{
    public function testSkill(): Skill
    {
        $name = 'test skill';
        $image = null;
        $description = 'test description';
        $parent = null;
        $position = 1;
        $skill = new Skill();
        $skill->setName($name);
        $skill->setImage($image);
        $skill->setDescription($description);
        $skill->setPosition($position);
        $this->assertNull($skill->getId());
        $this->assertSame($name, $skill->getName());
        $this->assertSame($image, $skill->getImage());
        $this->assertSame($description, $skill->getDescription());
        $this->assertSame($position, $skill->getPosition());
        $this->assertInstanceOf(ArrayCollection::class, $skill->getChildren());
        $this->assertNull($skill->getParent());
        $this->assertSame($name, (string) $skill);

        return $skill;
    }

    /**
     * @depends testSkill
     */
    public function testChildren(Skill $skill): void
    {
        $skill2 = new Skill();
        $skill->addChild($skill2);
        $this->assertCount(1, $skill->getChildren());
        $skill->removeChild($skill2);
        $this->assertEmpty($skill->getChildren());
    }
}
