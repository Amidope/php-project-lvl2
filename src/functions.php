<?php

namespace Php\Project\Lvl2\Functions;

use function Docopt\dump as dumperDocopt;
use function Functional\filter;
use function Php\Project\Lvl2\Parsers\parseJson;
use function Php\Project\Lvl2\Parsers\parseYaml;
use function Php\Project\Lvl2\Builder\buildTree;
use function Php\Project\Lvl2\Formatters\stylish;
use function Php\Project\Lvl2\Formatters\plain;
use function Functional\reduce_left;

function getFilesPath()
{
    #TODO: doopt input parse
    return [$_SERVER['argv'][1], $_SERVER['argv'][2]];
}

function getDataByExtension($pathToFile)
{
    if (str_ends_with($pathToFile, 'json')) {
        return parseJson($pathToFile);
    } elseif (str_ends_with($pathToFile, 'yaml') || str_ends_with($pathToFile, 'yml')) {
        return parseYaml($pathToFile);
    }
}

function checkForEmptyness($file1, $file2)
{
    if (!$file1 && !$file2) {
        return "Both files are empty";
    }
    if (!$file1) {
        return "First file is empty";
    }
    if (!$file2) {
        return "Second file is empty";
    }
    return "";
}

function treeSort($tree)
{
    usort($tree, fn($item1, $item2) => strcmp($item1["key"], $item2["key"]));
    return $tree;
}

function buildNode($key, $val, string $sign = " ")
{
    return ['sign' => $sign, "key" => $key, 'value' => $val];
}

function findPair($col, $key)
{
    
    return filter(
        $col,
        function ($item, $ind, $col) use ($key)
        {
            if ($item['processed'] ?? false) {
                return false;
            }
            return $item['key'] === $key ?: false;
        }
    );
}

function markAsProcessed(&$col, $index)
{
    $col[$index]['processed'] = true;
}

function stringifyValue($val)
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

function reduceWithFor(array $col, callable $callback, $initial = null)
{
    for ($index = 0; $index < count($col); $index++) { 
        $initial = $callback($col[$index], $index, $col, $initial);
    }
    return $initial;
}

function stringifyPlain($path, $node1, $node2)
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

function getDiffByFormat($tree, $format)
{
    switch ($format) {
        case 'stylish':
            return stylish($tree);
        case 'plain':
            return plain($tree);
        case 'json':
            return plain($tree);
        # TODO : wrtie json func    
    }
}


function gendiff($arr1, $arr2, $format = 'stylish')
{
    $tree = buildTree($arr1, $arr2);
    return getDiffByFormat($tree, $format);
}
