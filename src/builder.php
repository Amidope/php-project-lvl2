<?php
namespace Differ\Builder;
use function Differ\Functions\treeSort;
use function Functional\map;

function buildTree(mixed $data1, mixed $data2 = []): array
{
    if (!$data2) {
        $result = ['isList' => false, 'value' => $data1];
        if (!is_array($data1)) {
            return $result;
        }
        if (array_is_list($data1)) {
            return array_merge($result, ['isList' => true]);
        }
        $value = array_map(
            fn ($key, $val) => buildNode('unchanged', $key, $val),
            array_keys($data1),
            array_values($data1)
        );
        return array_merge($result, ['value' => $value]);
    }

    $addedPairs = array_diff_key($data2, $data1);
    $addedPairsTree = map($addedPairs, fn ($value, $key, $col) => buildNode('added', $key, $value));

    $deletedPairs = array_diff_key($data1, $data2);
    $deletedPairsTree = map($deletedPairs, fn ($value, $key, $col) => buildNode('deleted', $key, $value));

    $matchedKeys = array_intersect(array_keys($data1), array_keys($data2));
    $matchedTree = array_map(function ($key) use ($data1, $data2) {
        $val1 = $data1[$key];
        $val2 = $data2[$key];
        if ($val1 === $val2) {
            return buildNode('unchanged', $key, $val1);
        }
        if (isAssocArray($val1) && isAssocArray($val2)) {
            return buildNode('updated', $key, $val1, $val2);
        }
        return buildNode('changed', $key, $val1, $val2);
    }, $matchedKeys);
    return treeSort([...$deletedPairsTree, ...$addedPairsTree, ...$matchedTree]);
}

function buildNode(string $type, string $key, mixed $val1, mixed $val2 = []): array
{
    $res = [
        'type' => $type,
        'key' => $key,
        'isList' => false
    ];
    if ($val2 === []) {
        return array_merge($res, buildTree($val1));
    }
    if ($type === 'updated') {
        return array_merge($res, ['value' => buildTree($val1, $val2)]);
    }
    $oldValue = buildTree($val1);
    $newValue = buildTree($val2);
    return array_merge(
        $res,
        [
            'value' => [
                'oldValue' => $oldValue['value'],
                'newValue' => $newValue['value']
            ]
        ]
    );
}
function isAssocArray($value): bool
{
    if (is_array($value)) {
        if (!array_is_list($value)) {
            return true;
        }
    }
    return false;
}

