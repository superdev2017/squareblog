<?php

namespace App\Common\Doctrine\Fixtures;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Marino Di Clemente <kernelfolla@gmail.com>
 */
abstract class AbstractYamlFixture extends AbstractFixture
{
    const EXTENSION = 'yml';

    protected function parseFile($fileName)
    {
        return Yaml::parse(file_get_contents($fileName));
    }
}