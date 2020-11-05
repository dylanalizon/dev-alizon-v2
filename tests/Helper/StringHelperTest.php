<?php

namespace App\Tests\Helper;

use App\Helper\StringHelper;
use Generator;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    /**
     * @dataProvider provideTestContains
     *
     * @param string          $haystack
     * @param string|string[] $needles
     * @param bool            $expected
     */
    public function testContains(string $haystack, $needles, bool $expected): void
    {
        $service = new StringHelper();
        $result = $service->contains($haystack, $needles);
        $this->assertEquals($expected, $result);
    }

    public function provideTestContains(): Generator
    {
        yield ['test', 'es', true];
        yield ['test', 'toto', false];
        yield ['test', ['te', 'st'], true];
        yield ['test', ['toto', 'tata'], false];
        yield ['test', ['te', 'tata'], true];
    }
}
