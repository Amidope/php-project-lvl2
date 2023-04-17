<?php

namespace Differ\Differ;

use function Differ\Functions\getDataByExtension;
use function Differ\Functions\checkForEmptyness;
use function Differ\Functions\isValidFormat;
use function Differ\Builder\BuildTree;
use function Differ\Functions\getDiffByFormat;

function genDiff($pathToFile1, $pathToFile2, $format = 'stylish'): string
{
    $arr1 = getDataByExtension($pathToFile1);
    $arr2 = getDataByExtension($pathToFile2);
    $message = checkForEmptyness($arr1, $arr2);

    if ($message) {
        return $message;
    }
    if (!isValidFormat($format)) {
        return "Invalid format\n";
    }

    $tree = buildTree($arr1, $arr2);
    return getDiffByFormat($tree, $format);
}
