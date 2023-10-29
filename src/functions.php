<?php

namespace Differ\Functions;

use function Functional\filter;
use function Differ\Parsers\parseJson;
use function Differ\Parsers\parseYaml;
use function Differ\Formatters\stylish;
use function Differ\Formatters\plain;
use function Differ\Formatters\toJson;
use function Functional\reduce_left;

function getDataByExtension(string $pathToFile): array
{
    return str_ends_with($pathToFile, 'json') ? parseJson($pathToFile) : parseYaml($pathToFile);
}

function checkForEmptyness(array $arr1, array $arr2): string
{
    if (!$arr1 && !$arr2) {
        return "Both files are empty";
    }
    if (!$arr1) {
        return "First file is empty";
    }
    if (!$arr2) {
        return "Second file is empty";
    }
    return "";
}

function treeSort(array $tree)
{
    usort($tree, fn($item1, $item2) => strcmp($item1["key"], $item2["key"]));
    return $tree;
}

function stringifyValue(mixed $val)
{
    if (is_array($val)) {
        return "[complex value]";
    }
    //$val = is_null($val) ? 'null' : $val;
    //$string = trim(var_export($val, true), "'");
    $string = var_export($val, true);
    // !is_string($sting) is invalid. each val must have own statement
    if ($string === 'NULL') {
        return strtolower($string);
    }
    return $string;
}


function stringifyPlain(string $path, array $node1, array $node2)
{
    ['key' => $key1, 'value' => $val1, 'sign' => $sign] = $node1;
    $val1 = stringifyValue($val1);

    if ($node1 && $node2) {
        ['key' => $key2, 'value' => $val2] = $node2;
        $val2 = stringifyValue($val2);
        return "Property '{$path}' was updated. From {$val1} to {$val2}";
    }
    if ($sign === '+') {
        return "Property '{$path}' was added with value: {$val1}";
    }
    return "Property '{$path}' was removed";
}

function getDiffByFormat(array $tree, string $renderFormat): string
{
     return match ($renderFormat) {
        'stylish' => stylish($tree),
        'plain' => plain($tree),
        'json' => toJson($tree)
     };
}

function hasValidExtension(string $fileName): bool
{
    return in_array(pathinfo($fileName, PATHINFO_EXTENSION), ['json', 'yaml', 'yml']);
}

function checkExtensions(...$filePaths): string
{
    return reduce_left(
        $filePaths,
        function ($path, $ind, $col, $acc) {
            $ind++;
            return hasValidExtension($path) ? $acc : $acc . "File {$ind} has invalid extension\n";
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
