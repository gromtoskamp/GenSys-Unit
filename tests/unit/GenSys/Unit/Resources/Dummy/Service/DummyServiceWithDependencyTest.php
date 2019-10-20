<?php

namespace GenSys\Unit\Resources\Dummy\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DummyServiceWithDependencyTest extends TestCase
{
	/** @var \GenSys\Unit\Resources\Dummy\Object\DummyObject|MockObject */
	public $dummyObject;

    /** @var DummyServiceWithDependency */
    private $dummyService;


    public function setUp()
	{
		$this->dummyObject = $this->getMockBuilder('GenSys\Unit\Resources\Dummy\Object\DummyObject')->disableOriginalConstructor()->getMock();

		$this->dummyService = new DummyServiceWithDependency();
	}

	public function testAddToDummyValue()
    {
        $dummyObject = clone $this->dummyObject;
        $dummyObject->expects($this->any())->method('getDummyValue')->willReturn(5);

        $this->assertSame(
            10,
            $this->dummyService->addToDummyValue($dummyObject, 5)
        );
    }
}
