<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class FlatJsonDiffTest extends TestCase
{
    protected string $jsonPath1;
    protected $jsonPath2;
    protected $pathToYaml1;
    protected $pathToYaml2;
    protected $expectedFlatGendiff;

    public function getFixtureFullPath($fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp(): void
    {
        $this->jsonPath1 = $this->getFixtureFullPath('flat1.json');
        $this->jsonPath2 = $this->getFixtureFullPath('flat2.json');
        $this->pathToYaml1 = $this->getFixtureFullPath('flat1.yml');
        $this->pathToYaml2 = $this->getFixtureFullPath('flat2.yaml');

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
    }

    public function testJsonGenDiff(): void
    {
        $this->assertEquals($this->expectedFlatGendiff, genDiff($this->jsonPath1, $this->jsonPath2));
    }

    public function testFlatYamlGenDiff(): void
    {
        $this->assertEquals($this->expectedFlatGendiff, genDiff($this->pathToYaml1, $this->pathToYaml2));
    }
}
