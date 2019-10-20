<?php

namespace GenSys\Unit\Resources\Dummy\Service;

use GenSys\Unit\Resources\Dummy\Object\DummyObject;

class DummyServiceWithDependency
{
    /** @var DummyObject */
    private $dummyObject;

    public function __construct(DummyObject $dummyObject)
    {
        $this->dummyObject = $dummyObject;
    }

    public function addToDummyValue(int $addTo)
    {
        return $this->dummyObject->getDummyValue() + $addTo;
    }
}
