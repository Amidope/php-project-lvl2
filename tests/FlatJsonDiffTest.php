<?php

namespace Php\Project\Lvl2\Tests;

use PHPUnit\Framework\TestCase;
use function Php\Project\Lvl2\Functions\gendiff;

class FlatJsonDiffTest extends TestCase
{
    protected $jsonPath1;
    protected $jsonPath2;
    protected $pathToEmptyJson;

    public function getFixtureFullPath($fixtureName):string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp():void
    {
        $this->jsonPath1 = $this->getFixtureFullPath('file1.json');
        $this->jsonPath2 = $this->getFixtureFullPath('file2.json');
        $this->pathToEmptyJson = $this->getFixtureFullPath('empty.json');
    }

    public function testFlatGenDiff():void
    {
        $expected = <<<RES
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        RES;
        $this->assertEquals($expected, gendiff($this->jsonPath1, $this->jsonPath2));
    }
    public function testEmptyGenDiff():void
    {
        $expected = <<<RES
        {
          + host: hexlet.io
          + timeout: 20
          + verbose: true
        }
        RES;
        $this->assertEquals($expected, gendiff($this->pathToEmptyJson, $this->jsonPath2));
    }

}
