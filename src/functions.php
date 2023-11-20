<?php

namespace Differ\Functions;

use function Differ\Parsers\parseJson;
use function Differ\Parsers\parseYaml;
use function Differ\Formatters\formatToStylish;
use function Differ\Formatters\formatToPlain;
use function Differ\Formatters\formatToJson;
use function Functional\reduce_left;

function getDataByExtension(string $pathToFile): ?array
{
    return str_ends_with($pathToFile, 'json') ? parseJson($pathToFile) : parseYaml($pathToFile);
}

function checkForEmptyness(?array $arr1, ?array $arr2): string
{
    if (empty($arr1) && empty($arr2)) {
        return "Both files are empty";
    }
    if (empty($arr1)) {
        return "First file is empty";
    }
    if (empty($arr2)) {
        return "Second file is empty";
    }
    return "";
}

function treeSort(array $tree): array
{
    ksort($tree);
    return $tree;
}

function getDiffByFormat(array $tree, string $renderFormat): string
{
     return match ($renderFormat) {
        'stylish' => formatToStylish($tree),
        'plain' => formatToPlain($tree),
        default => formatToJson($tree)
     };
}

function hasValidExtension(string $fileName): bool
{
    return in_array(pathinfo($fileName, PATHINFO_EXTENSION), ['json', 'yaml', 'yml'], true);
}

function checkExtensions(string ...$filePaths): string
{
    return reduce_left(
        $filePaths,
        function ($path, $ind, $col, $acc) {
            return hasValidExtension($path) ? $acc : $acc . "File {${$ind++}} has invalid extension\n";
        },
        ""
    );
}

function isInvalidFormat(string $format): bool
{
    return match ($format) {
        'stylish', 'plain', 'json' => false,
        default => true
    };
}
function isList(mixed $value): bool
{
    if (is_array($value)) {
        if (array_is_list($value)) {
            return true;
        }
    }
    return false;
}
function isAssocArray(mixed $value): bool
{
    if (is_array($value)) {
        if (!array_is_list($value)) {
            return true;
        }
    }
    return false;
}
