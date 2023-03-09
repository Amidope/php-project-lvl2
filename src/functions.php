<?php

namespace Php\Project\Lvl2\Functions;

use function Functional\sort;
use function Php\Project\Lvl2\parsers\parseJson;
use function Php\Project\Lvl2\parsers\parseYaml;

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
            } elseif ('$value' === null) {
                return 'null';
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

function treeSort($tree)
{
    ksort();
	//usort($tree, fn($innerArr1, $innerArr2) => strcmp($innerArr1, $innerArr2));
	return $tree;
}

function buildTree($arr1, $arr2 = null)
{
	$result = [];

	// если $arr2 == null
	if (!$arr2) {
		return array_map(function($key, $value) {
			if (is_array($value)) {
				return ['sign' => null, "key" => $key, 'children' => buildTree($value)]; 
			}
			return ['sign' => null, "key" => $key, 'children' => $value];
		}, array_keys($arr1), array_values($arr1));
	}

	foreach ($arr1 as $key => $arr1Value) {
		$arr2Value = $arr2[$key] ?? null;

		// если ключ есть в $arr2
		if ($arr2Value) {
			if (is_array($arr1Value)) {
				$result[] = ['sign' => null, "key" => $key, 'children' => buildTree($arr1Value, $arr2Value)];
			} elseif ($arr1Value == $arr2Value) {
				$result[] = ['sign' => null, "key" => $key, 'children' => $arr1Value];
			} else {
				$result[] = ['sign' => "-", "key" => $key, 'children' => $arr1Value];
				$result[] = ['sign' => "+", "key" => $key, 'children' => $arr2Value];
			}
		} else {
			$result[] = ['sign' => "-", "key" => $key, 'children' => is_array($arr1Value) ? buildTree($arr1Value) : $arr1Value];
		}
	}

	$secondArrDiff = array_diff_key($arr2, $arr1);
	foreach ($secondArrDiff as $key => $value) {
		$result[] = ['sign' => "+", "key" => $key, 'children' => is_array($arr1Value) ? buildTree($arr1Value) : $arr1Value];
	}
    return treeSort($result);
}

function gendiff($pathToFile1, $pathToFile2)
{
    $file1 = getDataByExtension($pathToFile1);
    $file2 = getDataByExtension($pathToFile2);
    // $stringifiedArr1 = stringifyBool($file1);
    // $stringifiedArr2 = stringifyBool($file2);
    $res = buildTree($file1,$file2);
    var_dump($res);   
}
