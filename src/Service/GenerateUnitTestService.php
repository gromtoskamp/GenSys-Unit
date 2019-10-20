<?php

namespace GenSys\Unit\Service;

use GenSys\Unit\Service\Generator\GeneratorStrategy;
use ReflectionException;

class GenerateUnitTestService
{
    /** @var GeneratorStrategy */
    private $generatorStrategy;

    public function __construct(GeneratorStrategy $generatorStrategy)
    {
        $this->generatorStrategy = $generatorStrategy;
    }
    
    /**
     * @param string $className
     * @throws ReflectionException
     */
    public function generateUnitTest(string $className)
    {
        $this->generatorStrategy->createTest($className);
    }
}
