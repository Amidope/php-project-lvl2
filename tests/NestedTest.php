<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Functions\genDiff;

class NestedTest extends TestCase
{
    protected $jsonPath1;
    protected $jsonPath2;
    protected $YamlPath1;
    protected $YamlPath2;
    protected $expectedStylish;
    protected $expectedPlain;
    protected $expectedJson;

    public function getFixtureFullPath($fixtureName):string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp(): void
    {
        $this->jsonPath1 = $this->getFixtureFullPath('nestedFile1.json');
        $this->jsonPath2 = $this->getFixtureFullPath('nestedFile2.json');
        $this->YamlPath1 = $this->getFixtureFullPath('nestedFile1.yml');
        $this->YamlPath2 = $this->getFixtureFullPath('nestedFile2.yml');  
        $this->expectedJson = file_get_contents($this->getFixtureFullPath('formatted.json'));

        $this->expectedStylish = <<<RES
        {
            common: {
              + follow: false
                setting1: Value 1
              - setting2: 200
              - setting3: true
              + setting3: null
              + setting4: blah blah
              + setting5: {
                    key5: value5
                }
                setting6: {
                    doge: {
                      - wow:
                      + wow: so much
                    }
                    key: value
                  + ops: vops
                }
            }
            group1: {
              - baz: bas
              + baz: bars
                foo: bar
              - nest: {
                    key: value
                }
              + nest: str
            }
          - group2: {
                abc: 12345
                deep: {
                    id: 45
                }
            }
          + group3: {
                deep: {
                    id: {
                        number: 45
                    }
                }
                fee: 100500
            }
        }
        RES;
        $this->expectedPlain = <<<PL
        Property 'common.follow' was added with value: false
        Property 'common.setting2' was removed
        Property 'common.setting3' was updated. From true to null
        Property 'common.setting4' was added with value: 'blah blah'
        Property 'common.setting5' was added with value: [complex value]
        Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
        Property 'common.setting6.ops' was added with value: 'vops'
        Property 'group1.baz' was updated. From 'bas' to 'bars'
        Property 'group1.nest' was updated. From [complex value] to 'str'
        Property 'group2' was removed
        Property 'group3' was added with value: [complex value]
        PL;
    }
    

    public function testStylish():void
    {
        $this->assertEquals($this->expectedStylish, genDiff($this->jsonPath1, $this->jsonPath2, 'stylish'));
    }
    public function testPlain():void
    {
        $this->assertEquals($this->expectedPlain, genDiff($this->jsonPath1, $this->jsonPath2, 'plain'));
    }
    public function testToJson():void
    {
        $this->assertEquals($this->expectedJson, genDiff($this->jsonPath1, $this->jsonPath2, 'json'));
    }
}

