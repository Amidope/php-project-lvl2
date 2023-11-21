<?php

namespace Differ\Functions;

use function Differ\Formatters\formatToStylish;
use function Differ\Formatters\formatToPlain;
use function Differ\Formatters\formatToJson;
use function Functional\reduce_left;

function throwErrors(string $filepath1, string $filepath2, string $format): bool
{
    $e = array_map(
        function ($filepath) {
            return is_readable($filepath) ? '' : throw new \Exception("File {$filepath} is not readable");
        },
        [$filepath1, $filepath2]
    );
    $errors = getExtensionErrorMessages($filepath1, $filepath2);
    if ($errors !== '') {
        throw new \Exception($errors);
    }
    if (isInvalidFormat($format)) {
        throw new \Exception("Invalid format\n");
    }
    return false;
}

function treeSort(array $tree): array
{
    $sortedKeys = \Functional\sort(
        array_keys($tree),
        fn($k1, $k2) => $k1 <=> $k2,
        false
    );
    return array_reduce(
        $sortedKeys,
        fn ($acc, $key) => [...$acc, ...[$key => $tree[$key]]],
        []
    );
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

function getExtensionErrorMessages(string ...$filePaths): string
{
    return reduce_left(
        $filePaths,
        function ($path, $ind, $col, $acc) {
            return hasValidExtension($path) ? $acc : $acc . "File {${$ind + 1}} has invalid extension\n";
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
