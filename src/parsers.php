<?php

namespace Php\Project\Lvl2\parsers;
use Symfony\Component\Yaml\Yaml;

function parseJson($pathToFile)
{
    return json_decode(file_get_contents($pathToFile), true);
}
function parseYaml($pathToFile)
{
    $yamlString = file_get_contents($pathToFile);
    return (array) Yaml::parse($yamlString, Yaml::PARSE_OBJECT_FOR_MAP);
}