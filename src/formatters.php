<?php

namespace Differ\Formatters;

use function Docopt\dump as dumperDocopt;
use function Functional\reduce_left;
use function Differ\Functions\findPair;
use function Differ\Functions\markAsProcessed;
use function Differ\Functions\stringifyPlain;
use function Differ\Functions\reduceWithFor;

function stylish(array $node, int $indent = 0, int $spacesCount = 4)
{
    $lineIndent = str_repeat(" ", $indent + $spacesCount - 2);

    $result = array_reduce(
        $node,
        function ($acc, $item) use ($lineIndent, $indent, $spacesCount) {
            $value = $item['value'];
            $val = is_null($value) ? "null" : $value;
            $valueString = is_array($val)
                ? stylish($val, $indent + $spacesCount)
                : trim(var_export($val, true), "'");
            $acc .= "{$lineIndent}{$item['sign']} {$item['key']}: {$valueString}\n";
            return $acc;
        },
        ""
    );
    $closBracketIndent = $lineIndent = str_repeat(" ", $indent);
    return "{\n{$result}{$closBracketIndent}}";
}


function plain(array $tree)
{
    $iter = function ($tree, $path = '') use (&$iter) {
        return reduceWithFor(
            $tree,
            function ($item, $index, &$col, $acc) use ($path, $iter) {
                $key = $item['key'];
                $val = $item['value'];
                $sign = $item['sign'];
                if (array_key_exists('processed', $item)) {
                    return $acc;
                }
                markAsProcessed($col, $index);
                $path = $path ? "{$path}.{$key}" : $key;

                if ($sign === ' ') {
                    if (is_array($val)) {
                        $formatted = $iter($val, $path);
                        return [...$acc, ...$formatted];
                    }
                    return $acc;
                }

                $pair = findPair($col, $key);
                if ($pair) {
                    markAsProcessed($col, array_key_first($pair));
                    $pair = array_pop($pair);
                }
                $string = stringifyPlain($path, $item, $pair);
                $acc[] = $string;
                return $acc;
            },
            []
        );
    };
    $arr = $iter($tree);

    $res = implode("\n", $arr);
    return $res;
}

function toJson(array $tree)
{
    $iter = function ($tree) use (&$iter) {
        return reduce_left(
            $tree,
            function ($item, $ind, $col, $acc) use (&$iter) {
                $value = $item['value'];
                $key = "{$item['sign']} {$item['key']}";
                $value = is_array($value) ? $iter($value) : $value;
                $acc[$key] = $value;
                return $acc;
            },
            []
        );
    };
    $arr = $iter($tree);
    return json_encode($arr, JSON_PRETTY_PRINT);
}
