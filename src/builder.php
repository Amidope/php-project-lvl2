<?php

namespace Differ\Builder;

use function Differ\Functions\treeSort;
use function Functional\map;

function buildTree(mixed $arr1, mixed $arr2 = []): array
{
    if (!$arr2) {
        $result = ['isList' => false, 'value' => $arr1];
        if (!is_array($arr1)) {
            return $result;
        }
        if (array_is_list($arr1)) {
            return array_merge($result, ['isList' => true]);
        }
        $value = array_map(
            fn ($key, $val) => buildUnchanged($key, $val),
            array_keys($arr1),
            array_values($arr1)
        );
        return array_merge($result, ['value' => $value]);
    }

    $addedPairs = array_diff_key($arr2, $arr1);
    $addedPairsTree = map($addedPairs, fn ($value, $key, $col) => buildAdded($key, $value));

    $deletedPairs = array_diff_key($arr1, $arr2);
    $deletedPairsTree = map($deletedPairs, fn ($value, $key, $col) => buildDeleted($key, $value));

    $matchedKeys = array_intersect(array_keys($arr1), array_keys($arr2));
    $matchedTree = array_map(function ($key) use ($arr1, $arr2) {
        $val1 = $arr1[$key];
        $val2 = $arr2[$key];
        if ($val1 === $val2) {
            return buildUnchanged($key, $val1);
        }
        if (isAssocArray($val1) && isAssocArray($val2)) {
            return buildUpdated($key, $val1, $val2);
        }
        return buildChanged($key, $val1, $val2);
    }, $matchedKeys);
    return treeSort([...$deletedPairsTree, ...$addedPairsTree, ...$matchedTree]);
}
function buildAdded(string $key, mixed $value): array
{
    return array_merge(
        ['type' => 'added', 'key' => $key],
        buildTree($value)
    );
}
;;
function buildDeleted(string $key, mixed $value): array
{
    return array_merge(
        ['type' => 'deleted', 'key' => $key],
        buildTree($value)
    );
}
function buildUnchanged(string $key, mixed $value): array
{
    return array_merge(
        ['type' => 'unchanged', 'key' => $key],
        buildTree($value)
    );
}
function buildChanged(string $key, mixed $val1, mixed $val2): array
{
    return [
        'type' => 'changed',
        'key' => $key,
        'oldValue' => buildTree($val1),
        'newValue' => buildTree($val2)
    ];
}
function buildUpdated(string $key, array $val1, array $val2): array
{
    return [
        'type' => 'updated',
        'key' => $key,
        'value' => buildTree($val1, $val2)
    ];
}
function isAssocArray($arr)
{
    if (is_array($arr)) {
        if (!array_is_list($arr)) {
            return true;
        }
    }
    return false;
}