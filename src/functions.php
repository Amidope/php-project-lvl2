<?php

namespace Php\Project\Lvl2\Functions;


use function Docopt\dump as dumperDocopt;
//use function Symfony\Component\VarDumper\dump;
use function Functional\sort;
use function Functional\map;
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
	uksort($tree, fn($key1, $key2) => strcmp($key1, $key2));
	return $tree;
}

function buildLeaf($key, $val, string $sign = "")
{
	return ['sign' => $sign, "key" => $key, 'value' => $val];
}

// ADD YAML TREE
function buildTree($arr1, $arr2 = "")
{	
	$result = [];
	
	if (!$arr2) {
		return array_map(function($key, $val) {
			$value = is_array($val) ? buildTree($val) : $val;
			
			return buildLeaf($key, $value);
		}, array_keys($arr1), array_values($arr1));
	}
	$arr1UniqueByKeys = array_diff_key($arr1, $arr2);

	$tree1 = array_map(function($key, $val) {
		return buildLeaf($key, is_array($val) ? buildTree($val) : $val,"-");
	}, array_keys($arr1UniqueByKeys), array_values($arr1UniqueByKeys));
	
	$arr2UniqueByKeys = array_diff_key($arr2, $arr1); 

	$tree2 = array_map(function($key, $val) {
		return buildLeaf($key, is_array($val) ? buildTree($val) : $val,"+");
	}, array_keys($arr2UniqueByKeys), array_values($arr2UniqueByKeys));

	$matchedKeys = array_intersect(array_keys($arr1), array_keys($arr2));
	$matchedTree = array_reduce($matchedKeys, function($acc, $key) use ($arr1, $arr2) {
		$val1 = $arr1[$key];
		$val2 = $arr2[$key];
		if (is_array($val1) && is_array($val2)) {
			$children = buildTree($val1, $val2);
			$acc[] = buildLeaf($key, $children);
			return $acc;
		} elseif (!is_array($val1) && !is_array($val2)) {
			if ($val1 === $val2 ) {
				$acc[] = buildLeaf($key, $val1);
			} else {
				$acc[] = buildLeaf($key, $val1, "-");
				$acc[] = buildLeaf($key, $val2, "+");				
			}
			return $acc;
		}
			
		$value1 = is_array($val1) ? buildTree($val1) : $val1;
		$value2 = is_array($val2) ? buildTree($val2) : $val2;
		$acc[] = buildLeaf($key, $value1, "-");
		$acc[] = buildLeaf($key, $value2, "+");
		return $acc;
	}, []);
	$result = [...$matchedTree, ...$tree1, ...$tree2];
	$sorted = treeSort($result);
	dump('OOOOOOOOOOOO');
	dump($sorted);
	return $sorted;

}

function stringifyTree($tree, $replacer = " ", $spacesCount = 2)
{

}

function gendiff($pathToFile1, $pathToFile2)
{
    $file1 = getDataByExtension($pathToFile1);
    $file2 = getDataByExtension($pathToFile2);

	$output = checkForEmptyness($file1, $file2);

	if ($output) {
		return $output;
	}

	// if 1 file is empty return string
    $res = buildTree($file1,$file2);
    //dump($res);   
}
