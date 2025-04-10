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
	#[DataProvider('dataAssertEmpty')]
	#[TestDox('assertEmpty()')]
	public function testAssertEmpty(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertEmpty($query);
		});
	}

	public static function dataAssertEmpty(): array {
		return [
			'passed' => [null, '{"user": [null, false, 0, "", [], {}]}', '$.user[*]'],
			'failed' => ['Expected to find an empty element at position 4 matching the JSONPath "$.user[*]", actual: [null]', '{"user": [null, false, 0, "", [null], {}]}', '$.user[*]'],
			'failed and JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotEmpty')]
	#[TestDox('assertNotEmpty()')]
	public function testAssertNotEmpty(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNotEmpty($query);
		});
	}

	public static function dataAssertNotEmpty(): array {
		return [
			'passed' => [null, '{"user": [true, 1, "string", [null], {"a": 1}]}', '$.user[*]'],
			'failed' => ['Expected to find a non-empty element at position 2 matching the JSONPath "$.user[*]", actual: ""', '{"user": [true, 1, "", [null], {"a": 1}]}', '$.user[*]'],
			'failed and JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
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
			'failed' => ['Expected to find at least one element matching the JSONPath "$.user"', '{}', '$.user'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotExists')]
	#[TestDox('assertNotExists()')]
	public function testAssertNotExists(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNotExists($query);
		});
	}

	public static function dataAssertNotExists(): array {
		return [
			'passed' => [null, '{}', '$.user'],
			'failed' => ['Expected to find 0 elements matching the JSONPath "$.user", actual: 1', '{"user": [{}, {}]}', '$.user'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertEquals')]
	#[TestDox('assertEquals()')]
	public function testAssertEquals(?string $exceptionMessage, string $json, string $query, mixed $value): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query, $value): void {
			$assert->assertEquals($query, $value);
		});
	}

	public static function dataAssertEquals(): array {
		return [
			'passed when null and one element matches the query' => [null, '{"user": null}', '$.user', null],
			'passed when boolean and one element matches the query' => [null, '{"user": false}', '$.user', false],
			'passed when number and one element matches the query' => [null, '{"user": 12}', '$.user', 12],
			'passed when string and one element matches the query' => [null, '{"user": "string"}', '$.user', 'string'],
			'passed when array and one element matches the query' => [null, '{"user": [null, false, 12, "string", [], {}]}', '$.user', [null, false, 12, "string", [], []]],
			'passed when object and one element matches the query' => [null, '{"user": {"age": 12}}', '$.user', ['age' => 12]],
			'passed when null and many elements match the query' => [null, '{"user": [1, 2, null, 3]}', '$.user[*]', null],
			'passed when boolean and many elements match the query' => [null, '{"user": [1, 2, false, 3]}', '$.user[*]', false],
			'passed when number and many elements match the query' => [null, '{"user": [1, 2, 12, 3]}', '$.user[*]', 12],
			'passed when string and many elements match the query' => [null, '{"user": [1, 2, "string", 3]}', '$.user[*]', 'string'],
			'passed when array and many elements match the query' => [null, '{"user": [1, 2, [null, false, 12, "string", [], {}], 3]}', '$.user[*]', [null, false, 12, "string", [], []]],
			'passed when object and many elements match the query' => [null, '{"user": [1, 2, {"age": 12}, 3]}', '$.user[*]', ['age' => 12]],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user"', '{}', '$.user', null],
			'failed when one element matches JSONPath and no elements equal' => ['Expected to find at least one element with the exact value {"age":12} matching the JSONPath "$.user"', '{"user": {"age": 12, "name": "John"}}', '$.user', ['age' => 12]],
			'failed when many elements match JSONPath and no elements equal' => ['Expected to find at least one element with the exact value {"age":12} matching the JSONPath "$.user[*]"', '{"user": [{}, {"age": 12, "name": "John"}]}', '$.user[*]', ['age' => 12]],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotEquals')]
	#[TestDox('assertNotEquals()')]
	public function testAssertNotEquals(?string $exceptionMessage, string $json, string $query, mixed $value): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query, $value): void {
			$assert->assertNotEquals($query, $value);
		});
	}

	public static function dataAssertNotEquals(): array {
		return [
			'passed when one element matches the query' => [null, '{"user": "abc"}', '$.user', 'def'],
			'passed when many elements match the query' => [null, '{"user": ["abc", "def"]}', '$.user[*]', 'ghi'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user"', '{}', '$.user', null],
			'failed when one element matches JSONPath and one element is equal' => ['Expected to find none elements with the exact value "string" matching the JSONPath "$.user"', '{"user": "string"}', '$.user', 'string'],
			'failed when one element matches JSONPath and many elements are equal' => ['Expected to find none elements with the exact value "string" matching the JSONPath "$.user[*]"', '{"user": ["first", "string"]}', '$.user[*]', 'string'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNull')]
	#[TestDox('assertNull()')]
	public function testAssertNull(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNull($query);
		});
	}

	public static function dataAssertNull(): array {
		return [
			'passed' => [null, '{"user": [null, null]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element not null' => ['Expected all elements to be null for the JSONPath "$.user[*]"', '{"user": [null, 1]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotNull')]
	#[TestDox('assertNotNull()')]
	public function testAssertNotNull(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNotNull($query);
		});
	}

	public static function dataAssertNotNull(): array {
		return [
			'passed' => [null, '{"user": [1, 2]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element is null' => ['Expected all elements not to be null for the JSONPath "$.user[*]"', '{"user": [null, 1]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertBoolean')]
	#[TestDox('assertBoolean()')]
	public function testAssertBoolean(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertBoolean($query);
		});
	}

	public static function dataAssertBoolean(): array {
		return [
			'passed' => [null, '{"user": [true, false]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element not boolean' => ['Expected all elements to be boolean for the JSONPath "$.user[*]"', '{"user": [1, true]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotBoolean')]
	#[TestDox('assertNotBoolean()')]
	public function testAssertNotBoolean(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNotBoolean($query);
		});
	}

	public static function dataAssertNotBoolean(): array {
		return [
			'passed' => [null, '{"user": [1, 2]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element is boolean' => ['Expected all elements not to be boolean for the JSONPath "$.user[*]"', '{"user": [null, false]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNumber')]
	#[TestDox('assertNumber()')]
	public function testAssertNumber(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNumber($query);
		});
	}

	public static function dataAssertNumber(): array {
		return [
			'passed integer' => [null, '{"user": [12, 12.5]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element not number' => ['Expected all elements to be number for the JSONPath "$.user[*]"', '{"user": ["string", 1]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotNumber')]
	#[TestDox('assertNotNumber()')]
	public function testAssertNotNumber(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNotNumber($query);
		});
	}

	public static function dataAssertNotNumber(): array {
		return [
			'passed' => [null, '{"user": [true, false]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element is integer' => ['Expected all elements not to be number for the JSONPath "$.user[*]"', '{"user": [null, 1]}', '$.user[*]'],
			'failed when one element is float' => ['Expected all elements not to be number for the JSONPath "$.user[*]"', '{"user": [null, 1.5]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertString')]
	#[TestDox('assertString()')]
	public function testAssertString(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertString($query);
		});
	}

	public static function dataAssertString(): array {
		return [
			'passed' => [null, '{"user": ["abc", "def"]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element not string' => ['Expected all elements to be string for the JSONPath "$.user[*]"', '{"user": ["string", 1]}', '$.user[*]'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotString')]
	#[TestDox('assertNotString()')]
	public function testAssertNotString(?string $exceptionMessage, string $json, string $query): void {
		$this->assert($exceptionMessage, $json, static function (JsonAssert $assert) use ($query): void {
			$assert->assertNotString($query);
		});
	}

	public static function dataAssertNotString(): array {
		return [
			'passed' => [null, '{"user": [1, 2]}', '$.user[*]'],
			'failed when JSONPath not exists' => ['Expected to find at least one element matching the JSONPath "$.user[*]"', '{}', '$.user[*]'],
			'failed when one element is string' => ['Expected all elements not to be string for the JSONPath "$.user[*]"', '{"user": [1, "string"]}', '$.user[*]'],
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
