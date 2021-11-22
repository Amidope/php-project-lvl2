<?php

use function Functional\sort;

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

function gendiff($pathToFile1, $pathToFile2)
{
    $json1 = file_get_contents($pathToFile1);
    $json2 = file_get_contents($pathToFile2);

    $arrayJson1 = json_decode($json1, true);
    $sarrayJson2 = json_decode($json2, true);

    $stringifiedArr1 = stringifyBool($arrayJson1);
    $stringifiedArr2 = stringifyBool($sarrayJson2);

    $unsorted = [];
    foreach ($stringifiedArr1 as $key => $value) {
        if (array_key_exists($key, $stringifiedArr2)) {
            if ($value === $stringifiedArr2[$key]) {
                $unsorted[] = "    {$key}: {$value}";
            } else {
                $unsorted[] = "  - {$key}: {$value}";
                $unsorted[] = "  + {$key}: {$stringifiedArr2[$key]}";
            }
        } else {
            $unsorted[] = "  - {$key}: {$value}";
        }
    }
    $diff = array_diff_key($stringifiedArr2, $stringifiedArr1);
    foreach ($diff as $key => $value) {
        $unsorted[] = "  + {$key}: {$value}";
    }

    $sorted = sort(
        $unsorted,
        function ($a, $b) {
            //        print_r(substr ($a, 4, strpos($a, ':') - 4 . PHP_EOL));
            $strcmpx = strcmp(substr($a, 4, strpos($a, ':') - 4), substr($b, 4, strpos($b, ':') - 4));

            if ($strcmpx == 0) {
                return substr($a, 2) === '-' ? 1 : -1;
            }
            return $strcmpx;
        }
    );
    $resWithoutBrackets = array_reduce($sorted, fn ($acc, $item) => $acc . $item . PHP_EOL, "");
    return '{' . PHP_EOL . $resWithoutBrackets . '}';
}
