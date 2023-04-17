<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseJson($pathToFile)
{
    return json_decode(file_get_contents($pathToFile), true);
}
function parseYaml($pathToFile)
{
    return Yaml::parse(file_get_contents($pathToFile));
}
