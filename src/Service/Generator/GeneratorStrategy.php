<?php

namespace GenSys\Unit\Service\Generator;

interface GeneratorStrategy
{
    public function createTest(string $className);
}