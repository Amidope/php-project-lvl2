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
    protected $expected1;
}