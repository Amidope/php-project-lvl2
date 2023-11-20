<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile): array
{
    $fileData = file_get_contents($pathToFile);
    $read = is_string($fileData) ? $fileData : '';
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($extension === 'json') {
        return json_decode($read, true);
    }
    return Yaml::parse($read);
}
