<?php

namespace Differ\Formatters;

use function Differ\Functions\isList;
use function Functional\reduce_left;

function formatToStylish(array $tree, bool $isList = false, int $indent = 0, int $spaceCount = 4): string
{
    $indentBeforeSign = str_repeat(" ", $indent + $spaceCount - 2);
    $closBracketIndent = str_repeat(" ", $indent);

    if ($isList) {
        $lastKey = array_key_last($tree);
        $result = reduce_left(
            $tree,
            function ($primitive, $key, $col, $acc) use ($indentBeforeSign, $indent, $spaceCount, $lastKey) {
                $comma = $key === $lastKey ? '' : ',';
                $value = getPrimitiveValueAsString($primitive);
                return "{$acc}{$indentBeforeSign}  {$value}{$comma}\n";
            },
            ""
        );
        return "[\n{$result}{$closBracketIndent}]";
    }

    $result = reduce_left(
        $tree,
        function ($item, $nodeKey, $col, $acc) use ($indentBeforeSign, $indent, $spaceCount) {
            [
                'type' => $type,
                'value' => $nodeValue,
            ] = $item;
            if ($type === 'changed') {
                ['oldValue' => $oldValue, 'newValue' => $newValue] = $nodeValue;
                $oldIsList = isList($oldValue);
                $newIsList = isList($newValue);

                $oldValue = is_array($oldValue)
                    ? formatToStylish($oldValue, $oldIsList, $indent + $spaceCount)
                    : getPrimitiveValueAsString($oldValue);
                $newValue = is_array($newValue)
                    ? formatToStylish($newValue, $newIsList, $indent + $spaceCount)
                    : getPrimitiveValueAsString($newValue);
                $renderedOld = "{$indentBeforeSign}- $nodeKey: {$oldValue}\n";
                $renderedNew = "{$indentBeforeSign}+ $nodeKey: {$newValue}\n";
                return "{$acc}{$renderedOld}{$renderedNew}";
            }
            $isList = isList($nodeValue);
            $sign = getSignByType($type);

            $renderedValue = is_array($nodeValue)
                ? formatToStylish($nodeValue, $isList, $indent + $spaceCount)
                : getPrimitiveValueAsString($nodeValue);
            return "{$acc}{$indentBeforeSign}{$sign} $nodeKey: {$renderedValue}\n";
        },
        ""
    );
    return "{\n{$result}{$closBracketIndent}}";
}

function formatToPlain(array $tree, $path = ''): string
{
    $result = reduce_left(
        $tree,
        function ($item, $nodeKey, $col, $acc) use ($path) {
            [
                'type' => $type,
                'value' => $nodeValue,
            ] = $item;
            $separator = $path ? '.' : '';
            $currentPath = "{$path}{$separator}{$nodeKey}";
            switch ($type) {
                case 'updated':
                    return $acc . formatToPlain($nodeValue, $currentPath);
                case 'changed':
                    return $acc . renderProperty($type, $currentPath, $nodeValue['oldValue'], $nodeValue['newValue']);
                case 'added':
                case 'deleted':
                    return $acc . renderProperty($type, $currentPath, $nodeValue);
            }
            return $acc;
        },
        ''
    );
    return $path === '' ? substr($result, 0,-1) : $result;
}

function formatToJson(array $tree): string
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}

function renderProperty(string $type, string $path, mixed $val1, mixed $val2 = []): string
{
    $stringValue1 = is_array($val1) ? '[complex value]' : getPrimitiveValueAsString($val1, false);
    switch ($type) {
        case 'changed':
            $stringValue2 = is_array($val2) ? '[complex value]' : getPrimitiveValueAsString($val2, false);
            return "Property '{$path}' was updated. From {$stringValue1} to {$stringValue2}\n";
        case 'added':
            return "Property '{$path}' was added with value: {$stringValue1}\n";
    }
    return "Property '{$path}' was removed\n";
}

function getPrimitiveValueAsString(mixed $val, bool $trim = true): string
{
    $char = $trim && is_string($val) ? "'" : '';
    return is_null($val) ? 'null' : trim(var_export($val, true), $char);
}

function getSignByType(string $type): ?string
{
    return match ($type) {
        'added' => '+',
        'deleted' => '-',
        'unchanged', 'updated' => ' ',
        'changed' => null
    };
}
