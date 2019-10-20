<?php

namespace GenSys\Unit\Model;

use ReflectionParameter;

class MockDependency
{
    /** @var ReflectionParameter */
    private $parameter;

    public function __construct(
        ReflectionParameter $parameter
    ) {
        $this->parameter = $parameter;
    }

    public function getClassName()
    {
        return $this->parameter->getClass()->getShortName();
    }
    
    public function getPropertyName()
    {
        return lcfirst($this->getClassName());
    }

    public function getFullyQualifiedClassName()
    {
        return '\\' . $this->parameter->getClass()->getName();
    }

    public function getBody()
    {
        return '$this->' . $this->getPropertyName() . ' = $this->getMockBuilder(\'' . $this->parameter->getClass()->getName() . '\')->disableOriginalConstructor()->getMock();';
    }
}
