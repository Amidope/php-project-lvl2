<?php

namespace Differ\Differ;

use function Differ\Builder\buildTree;
use function Differ\Functions\getDiffByFormat;
use function Differ\Functions\throwErrors;
use function Differ\Parsers\parseFile;

function genDiff(string $pathToFile1, string $pathToFile2, string $renderFormat = 'stylish'): string
{
    $e = throwErrors($pathToFile1, $pathToFile2, $renderFormat);

    $arr1 = parseFile($pathToFile1);
    $arr2 = parseFile($pathToFile2);

    $tree = buildTree($arr1, $arr2);
    return getDiffByFormat($tree, $renderFormat);
}
