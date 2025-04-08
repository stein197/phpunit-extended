<?php
namespace Test\Assert;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Stein197\PHPUnit\Assert\JsonAssert;
use Stein197\PHPUnit\TestCase;

final class JsonAssertTest extends PHPUnitTestCase {

	use TestCase;

	#[Test]
	#[DataProvider('dataAssertCount')]
	#[TestDox('assertCount()')]
	public function testAssertCount(?string $exceptionMessage, string $json, string $query, int $expectedCount): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query, $expectedCount): void {
			$assert->assertCount($query, $expectedCount);
		});
	}

	public static function dataAssertCount(): array {
		return [
			'passed' => [null, '{"user": [{}, {}]}', '$.user[*]', 2],
			'failed' => ['Expected to find 2 elements matching the JSONPath "$.user[*]", actual: 0', '{"user": []}', '$.user[*]', 2],
			'failed and JSONPath not exists' => ['Expected to find 2 elements matching the JSONPath "$.user[*]", actual: 0', '{}', '$.user[*]', 2],
		];
	}

	#[Test]
	#[DataProvider('dataAssertExists')]
	#[TestDox('assertExists()')]
	public function testAssertExists(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertExists($query);
		});
	}

	public static function dataAssertExists(): array {
		return [
			'passed' => [null, '{"user": [{}, {}]}', '$.user'],
			'failed' => ['Expected to find at least one element matching the JSONPath "$.user", actual: 0', '{}', '$.user'],
		];
	}

	private function assert(?string $exceptionMessage, string $json, callable $f): void {
		if ($exceptionMessage) {
			$this->expectException(AssertionFailedError::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		$f($this->json($json));
	}
}
