<?php

namespace GenSys\Unit\Resources\Dummy\Object;

class DummyObject
{
    /** @var int */
    private $dummyValue;

    public function __construct(int $dummyValue)
    {
        $this->dummyValue = $dummyValue;
    }

    public function getDummyValue(): int
    {
        return $this->dummyValue;
    }
}