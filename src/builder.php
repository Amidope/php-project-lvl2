<?php

namespace Php\Project\Lvl2\Builder;

use function Php\Project\Lvl2\Functions\buildNode;
use function Php\Project\Lvl2\Functions\treeSort;
use function Docopt\dump as dumperDocopt;

function buildTree($arr1, $arr2 = [])
{
    $result = [];
    if (!$arr2) {
        $result = array_map(function ($key, $val) {
            $value = is_array($val) ? buildTree($val) : $val;
            return buildNode($key, $value);
        }, array_keys($arr1), array_values($arr1));
        return treeSort($result);
    }

    $arr1UniqueByKeys = array_diff_key($arr1, $arr2);
    $tree1 = array_map(function ($key, $val) {
        return buildNode($key, is_array($val) ? buildTree($val) : $val, "-");
    }, array_keys($arr1UniqueByKeys), array_values($arr1UniqueByKeys));

    $arr2UniqueByKeys = array_diff_key($arr2, $arr1);
    $tree2 = array_map(function ($key, $val) {
        return buildNode($key, is_array($val) ? buildTree($val) : $val, "+");
    }, array_keys($arr2UniqueByKeys), array_values($arr2UniqueByKeys));

    $matchedKeys = array_intersect(array_keys($arr1), array_keys($arr2));
    $matchedTree = array_reduce(
        $matchedKeys,
        function ($acc, $key) use ($arr1, $arr2) {
            $val1 = $arr1[$key];
            $val2 = $arr2[$key];
            if (is_array($val1) && is_array($val2)) {
                $children = buildTree($val1, $val2);
                $acc[] = buildNode($key, $children);
                return $acc;
            } elseif (!is_array($val1) && !is_array($val2)) {
                if ($val1 === $val2) {
                    $acc[] = buildNode($key, $val1);
                } else {
                    $acc[] = buildNode($key, $val1, "-");
                    $acc[] = buildNode($key, $val2, "+");
                }
                return $acc;
            }

            $value1 = is_array($val1) ? buildTree($val1) : $val1;
            $value2 = is_array($val2) ? buildTree($val2) : $val2;
            $acc[] = buildNode($key, $value1, "-");
            $acc[] = buildNode($key, $value2, "+");
            return $acc;
        },
        []
    );
    $result = [...$matchedTree, ...$tree1, ...$tree2];
    $sorted = treeSort($result);
    return $sorted;
}
