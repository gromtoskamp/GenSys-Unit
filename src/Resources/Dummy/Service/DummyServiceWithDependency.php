<?php

namespace GenSys\Unit\Resources\Dummy\Service;

use GenSys\Unit\Resources\Dummy\Object\DummyObject;

class DummyServiceWithDependency
{
    public function __construct(){}

    public function addToDummyValue(DummyObject $dummyObject, int $addTo)
    {
        return $this->privateAddToDummyValue($dummyObject, $addTo);
    }

    private function privateAddToDummyValue(DummyObject $dummyObject, int $addTo)
    {
        return $dummyObject->getDummyValue() + $addTo;
    }
}
