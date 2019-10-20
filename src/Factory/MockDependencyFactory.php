<?php

namespace GenSys\Unit\Factory;

use GenSys\Unit\Model\MockDependency;
use ReflectionParameter;

class MockDependencyFactory
{
    public function createFromReflectionParameter(ReflectionParameter $parameter)
    {
        return new MockDependency($parameter);
    }    
}
