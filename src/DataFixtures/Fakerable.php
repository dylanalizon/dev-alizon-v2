<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;

trait Fakerable
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
}
