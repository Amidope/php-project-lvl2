<?php

namespace Php\Project\Lvl2\Formatters;

use function Docopt\dump as dumperDocopt;

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
