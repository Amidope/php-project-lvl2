<?php

namespace Php\Project\Lvl2\Formatters;

use function Docopt\dump as dumperDocopt;
use function Functional\reduce_left;
use function Php\Project\Lvl2\Functions\findPair;
use function Php\Project\Lvl2\Functions\markAsProcessed;
use function Php\Project\Lvl2\Functions\stringifyPlain;
use function Php\Project\Lvl2\Functions\reduceWithFor;

function stylish($node, $indent = 0, $spacesCount = 4)
{
    $lineIndent = str_repeat(" ", $indent + $spacesCount - 2);

    $result = array_reduce(
        $node,
        function ($acc, $item) use ($lineIndent, $indent, $spacesCount) {
            $value = $item['value'];
            $value = is_null($value) ? "null" : $value;
            $valueString = is_array($value)
                ? stylish($value, $indent + $spacesCount)
                : trim(var_export($value, true), "'");
            $valueString = $valueString === "" ? $valueString : " {$valueString}";
            $acc .= "{$lineIndent}{$item['sign']} {$item['key']}:{$valueString}\n";
            return $acc;
        },
        ""
    );
    $closBracketIndent = $lineIndent = str_repeat(" ", $indent);
    return "{\n{$result}{$closBracketIndent}}";
}


function plain($tree)
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

function toJson($tree)
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
