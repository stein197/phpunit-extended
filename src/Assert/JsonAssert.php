<?php
namespace Stein197\PHPUnit\Assert;

use JsonPath\InvalidJsonPathException;
use JsonPath\JsonObject;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function sizeof;

// TODO: assertPartial(array $expected)
// TODO: assertEquals(string $query, mixed $value)
// TODO: assertNotEquals(string $query, mixed $value)
// TODO: assertEmpty(string $query)
// TODO: assertNotEmpty(string $query)
// TODO: assertNotExists(string $query)
// TODO: find(string $query): mixed
/**
 * JSON document assertions by JSONPath queries.
 * @package Stein197\PHPUnit\Assert
 * @internal
 */
final readonly class JsonAssert {

	/**
	 * @param TestCase $test PHPUnit test case object to call assertions from.
	 * @param JsonObject $json JSON object.
	 */
	public function __construct(
		private TestCase $test,
		private JsonObject $json
	) {}

	/**
	 * Assert that there are `$expectedCount` elements matching the `$query`.
	 * @param string $query JSONPath to find elements by.
	 * @param int $expectedCount Expected amount of elements to find.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException If the amount of the found elements is not equal to the `$expectedCount`.
	 * ```php
	 * $this->assertCount('$.user', 10);
	 * ```
	 */
	public function assertCount(string $query, int $expectedCount): void {
		$length = sizeof($this->json->get($query));
		$this->test->assertEquals($expectedCount, $length, "Expected to find {$expectedCount} elements matching the JSONPath \"{$query}\", actual: {$length}");
	}

	/**
	 * Assert that there is at least one element matching the `$query`.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException If there are no elements matching the given query.
	 * ```php
	 * $this->assertExists('$.user');
	 * ```
	 */
	public function assertExists(string $query): void {
		$length = sizeof($this->json->get($query));
		$this->test->assertGreaterThan(0, $length, "Expected to find at least one element matching the JSONPath \"{$query}\"");
	}
}
