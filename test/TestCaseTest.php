<?php
namespace Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Stein197\PHPUnit\ExtendedTestCase;

final class TestCaseTest extends TestCase {

	use ExtendedTestCase;

	#[Test]
	#[TestDox('pass()')]
	public function testPass(): void {
		$this->pass();
	}
}
