<?php

namespace GenSys\Unit\Resources\Dummy\Service;

use GenSys\Unit\Resources\Dummy\Object\DummyObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DummyServiceWithDependencyTest extends TestCase
{
	/** @var DummyObject|MockObject */
	public $dummyObject;


	public function setUp()
	{
		$this->dummyObject = $this->getMockBuilder('GenSys\Unit\Resources\Dummy\Object\DummyObject')->disableOriginalConstructor()->getMock();
	}


	public function testAddToDummyValueProperty()
	{
		$dummyObject = clone $this->dummyObject;
	}


	public function testAddToDummyValue()
	{
		$dummyObject = clone $this->dummyObject;
	}
}
