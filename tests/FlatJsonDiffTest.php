<?php

namespace Php\Project\Lvl2\Tests;

use PHPUnit\Framework\TestCase;
use function Php\Project\Lvl2\Functions\gendiff;

class FlatJsonDiffTest extends TestCase
{
    protected $jsonPath1;
    protected $jsonPath2;
    protected $pathToEmptyJson;
    protected $pathToYaml1;
    protected $pathToYaml2;
    protected $pathToEmptyYaml;
    protected $expectedFlatGendiff;
    protected $expectedFirstEmptyFile;
    protected $expectedSecondEmptyFile;
    protected $expectedBothEmpty;

    public function getFixtureFullPath($fixtureName):string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp():void
    {
        $this->jsonPath1 = $this->getFixtureFullPath('flat1.json');
        $this->jsonPath2 = $this->getFixtureFullPath('flat2.json');
        $this->pathToEmptyJson = $this->getFixtureFullPath('empty.json');
        $this->pathToYaml1 = $this->getFixtureFullPath('flat1.yml');
        $this->pathToYaml2 = $this->getFixtureFullPath('flat2.yaml');
        $this->pathToEmptyYaml = $this->getFixtureFullPath('empty.yml');

        $this->expectedFlatGendiff = <<<RES
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        RES;
        $this->expectedFirstEmptyFile = "First file is empty";
        $this->expectedSecondEmptyFile = "Second file is empty";
        $this->expectedBothEmpty = "Both files are empty";
    }

    public function testJsonGenDiff():void
    {
        $this->assertEquals($this->expectedFlatGendiff, gendiff($this->jsonPath1, $this->jsonPath2));
    }
    public function testEmptyJsonGenDiff():void
    {
        $this->assertEquals($this->expectedFirstEmptyFile, gendiff($this->pathToEmptyJson, $this->jsonPath2));
        $this->assertEquals($this->expectedSecondEmptyFile, gendiff($this->jsonPath1, $this->pathToEmptyJson));
    }
    public function testFlatYamlGenDiff():void
    {
        $this->assertEquals($this->expectedFlatGendiff, gendiff($this->pathToYaml1, $this->pathToYaml2));
    }
    public function testEmptyYamlGenDiff():void
    {
        $this->assertEquals($this->expectedFirstEmptyFile, gendiff($this->pathToEmptyYaml, $this->pathToYaml2));
        $this->assertEquals($this->expectedSecondEmptyFile, gendiff($this->pathToYaml1, $this->pathToEmptyYaml));
    }
}
