<?php

namespace Php\Project\Lvl2\Functions;

use function Docopt\dump as dumperDocopt;
use function Php\Project\Lvl2\Parsers\parseJson;
use function Php\Project\Lvl2\Parsers\parseYaml;
use function Php\Project\Lvl2\Builder\buildTree;
use function Php\Project\Lvl2\Formatters\stylish;

function getFilesPath()
{
    return [$_SERVER['argv'][1], $_SERVER['argv'][2]];
}

function stringifyBool($arr)
{
    return array_map(
        function ($value) {
            if ($value === true) {
                return 'true';
            } elseif ($value === false) {
                return 'false';
            } elseif ('$value' === "") {
                return "";
            }
            return $value;
        },
        $arr
    );
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

function gendiff($pathToFile1, $pathToFile2)
{
    $file1 = getDataByExtension($pathToFile1);
    $file2 = getDataByExtension($pathToFile2);
    $output = checkForEmptyness($file1, $file2);
    
    if ($output) {
        return $output;
    }

    $tree = buildTree($file1, $file2);
    dump($tree);
    $res = stylish($tree);
    return $res;
}
