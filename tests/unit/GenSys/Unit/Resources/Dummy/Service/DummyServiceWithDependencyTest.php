<?php

namespace GenSys\Unit\Resources\Dummy\Service;

use PHPUnit\Framework\TestCase;

class DummyServiceWithDependencyTest extends TestCase
{
	/** @var \GenSys\Unit\Resources\Dummy\Object\DummyObject */
	public $dummyObject;


	public function setUp()
	{
		$this->dummyObject = $this->getMockBuilder('GenSys\Unit\Resources\Dummy\Object\DummyObject')->disableOriginalConstructor()->getMock();
	}
}
