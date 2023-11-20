<?php

namespace Differ\Differ;

use function Differ\Functions\getDataByExtension;
use function Differ\Builder\buildTree;
use function Differ\Functions\getDiffByFormat;
use function Differ\Functions\throwErrors;

function genDiff(string $pathToFile1, string $pathToFile2, string $renderFormat = 'stylish'): string
{
    throwErrors($pathToFile1, $pathToFile2, $renderFormat);

    $arr1 = getDataByExtension($pathToFile1);
    $arr2 = getDataByExtension($pathToFile2);

    $tree = buildTree($arr1, $arr2);
    return getDiffByFormat($tree, $renderFormat);
}
