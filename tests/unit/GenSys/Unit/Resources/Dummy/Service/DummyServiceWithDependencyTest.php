<?php

namespace GenSys\Unit\Resources\Dummy\Service;

class DummyServiceWithDependencyTest extends \PHPUnit\Framework\TestCase
{
	public function setUp()
	{
		$this->getMockBuilder('GenSys\Unit\Resources\Dummy\Object\DummyObject')->disableOriginalConstructor()->getMock();
	}
}
