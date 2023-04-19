<?php

namespace Differ\Differ;

use function Differ\Functions\getDataByExtension;
use function Differ\Functions\checkForEmptyness;
use function Differ\Functions\isValidFormat;
use function Differ\Builder\buildTree;
use function Differ\Functions\getDiffByFormat;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string|bool
{
    if (!(file_exists($pathToFile1) && file_exists($pathToFile2))) {
        return false;
    }
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
