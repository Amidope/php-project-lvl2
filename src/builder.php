<?php

namespace Differ\Builder;

use function Differ\Functions\treeSort;
use function Functional\map;
use function Differ\Functions\isAssocArray;

function buildTree(mixed $data1, mixed $data2 = []): mixed
{
    if ($data2 = []) {
        return isAssocArray($data1)
            ? map($data1, fn ($val, $key) => buildNode('unchanged', $key, $val))
            : $data1;
    }

    $addedPairs = array_diff_key($data2, $data1);
    $addedPairsTree = map($addedPairs, fn ($value, $key, $col) => buildNode('added', $key, $value));

    $deletedPairs = array_diff_key($data1, $data2);
    $deletedPairsTree = map($deletedPairs, fn ($value, $key, $col) => buildNode('deleted', $key, $value));

    $matchedPairs = array_intersect_key($data1, $data2);
    $matchedTree = map(
        $matchedPairs,
        function ($item, $key) use ($data1, $data2) {
            $val1 = $data1[$key];
            $val2 = $data2[$key];
            if ($val1 === $val2) {
                return buildNode('unchanged', $key, $val1);
            }
            if (isAssocArray($val1) && isAssocArray($val2)) {
                return buildNode('updated', $key, $val1, $val2);
            }
            return buildNode('changed', $key, $val1, $val2);
        }
    );
    return treeSort([...$deletedPairsTree, ...$addedPairsTree, ...$matchedTree]);
}

function buildNode(string $type, string $key, mixed $val1, mixed $val2 = []): array
{
    if ($type === 'changed') {
        return [
            'type' => $type,
            'value' => [
                'oldValue' => buildTree($val1),
                'newValue' => buildTree($val2)
            ]
        ];
    }
    return ['type' => $type, 'value' => buildTree($val1, $val2)];
}
