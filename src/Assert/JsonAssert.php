<?php
namespace Stein197\PHPUnit\Assert;

use JsonPath\JsonObject;
use PHPUnit\Framework\TestCase;
use function sizeof;

// TODO: response()->json()
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
	 * @throws ExpectationFailedException If the amount of the found elements is not equal to the `$expectedCount`.
	 * ```php
	 * $this->assertCount('$.user', 10);
	 * ```
	 */
	public function assertCount(string $query, int $expectedCount): void {
		$length = sizeof($this->json->get($query));
		$this->test->assertEquals($expectedCount, $length, "Expected to find {$expectedCount} elements matching the JSONPath \"{$query}\", actual: {$length}");
	}
}
