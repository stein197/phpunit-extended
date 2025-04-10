<?php
namespace Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Stein197\PHPUnit\TestCase;

final class TestCaseTest extends PHPUnitTestCase {

	use TestCase;

	#[Test]
	#[TestDox('pass()')]
	public function testPass(): void {
		$this->pass();
	}
}
