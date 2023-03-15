<?php

namespace Php\Project\Lvl2\Tests;

use PHPUnit\Framework\TestCase;
use function Php\Project\Lvl2\Functions\gendiff;

class NestedTest extends TestCase
{
    protected $jsonPath1;
    protected $jsonPath2;
    protected $pathToYaml1;
    protected $pathToYaml2;
    protected $expectedString;

    public function getFixtureFullPath($fixtureName):string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp(): void
    {
        $this->jsonPath1 = $this->getFixtureFullPath('nestedFile1.json');
        $this->jsonPath2 = $this->getFixtureFullPath('nestedFile2.json');
        
        
        $this->expectedString = <<<RES        
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
    }
    
    public function testNestedDiff():void
    {
        $this->assertEquals($this->expectedString, gendiff($this->jsonPath1, $this->jsonPath2));
    }
}

