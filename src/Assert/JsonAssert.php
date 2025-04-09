<?php
namespace Stein197\PHPUnit\Assert;

use JsonPath\InvalidJsonPathException;
use JsonPath\JsonObject;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function array_filter;
use function array_is_list;
use function gettype;
use function json_encode;
use function sizeof;

// TODO: assertPartial(array $expected)
// TODO: assertContains(string $query, mixed | array $partial)
// TODO: assertNotContains(string $query, mixed | array $partial)
// TODO: assertTextMatchesRegex(string $query, string $regex)
// TODO: assertTextNotMatchesRegex(string $query, string $regex)
// TODO: assertEmpty(string $query)
// TODO: assertNotEmpty(string $query)
// TODO: assertArray(string $query)
// TODO: assertNotArray(string $query)
// TODO: assertObject(string $query)
// TODO: assertNotObject(string $query)
// TODO: find(string $query): mixed
// TODO: assert[Not]Equals() should check ALL elements instead of at least single
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

	/**
	 * Assert that there are no elements matching the `$query`.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException If there is at least one element matching the given query.
	 * ```php
	 * $this->assertNotExists('$.user');
	 * ```
	 */
	public function assertNotExists(string $query): void {
		$this->assertCount($query, 0);
	}

	/**
	 * Assert that the value at the given JSONPath is equal to the passed one.
	 * @param string $query JSONPath to find elements by.
	 * @param mixed $value Expected value.
	 * @return void
	 * @throws InvalidJsonPathException When JSON is invalid.
	 * @throws Exception
	 * @throws ExpectationFailedException When JSONPath does not exist or none of the values equal to the passed one.
	 * ```php
	 * $this->assertEquals('$.user', ['name' => 'John']);
	 * ```
	 */
	public function assertEquals(string $query, mixed $value): void {
		$this->assertExists($query);
		$elements = $this->json->get($query) ?: [];
		$this->test->assertContains($value, $elements, 'Expected to find at least one element with the exact value ' . json_encode($value) . " matching the JSONPath \"{$query}\"");
	}

	/**
	 * Assert that the value at the given JSONPath is not equal to the passed one.
	 * @param string $query JSONPath to find elements by.
	 * @param mixed $value Not expected value.
	 * @return void
	 * @throws InvalidJsonPathException When JSON is invalid.
	 * @throws Exception
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements equal to the passed one.
	 * ```php
	 * $this->assertNotEquals('$.user', ['name' => 'John']);
	 * ```
	 */
	public function assertNotEquals(string $query, mixed $value): void {
		$this->assertExists($query);
		$elements = $this->json->get($query) ?: [];
		$this->test->assertNotContains($value, $elements, 'Expected to find none elements with the exact value ' . json_encode($value) . " matching the JSONPath \"{$query}\"");
	}

	/**
	 * Assert that the values at the given JSONPath to be null.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is not null.
	 * @throws Exception
	 * ```php
	 * $this->assertNull('$.user');
	 * ```
	 */
	public function assertNull(string $query): void {
		$this->assertThatType($query, 'null', true);
	}

	/**
	 * Assert that none elements at the given JSONPath are null.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is null.
	 * @throws Exception
	 * ```php
	 * $this->assertNotNull('$.user');
	 * ```
	 */
	public function assertNotNull(string $query): void {
		$this->assertThatType($query, 'null', false);
	}

	/**
	 * Assert that the values at the given JSONPath to be boolean.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is not boolean.
	 * @throws Exception
	 * ```php
	 * $this->assertBoolean('$.user');
	 * ```
	 */
	public function assertBoolean(string $query): void {
		$this->assertThatType($query, 'boolean', true);
	}

	/**
	 * Assert that none elements at the given JSONPath are boolean.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is boolean.
	 * @throws Exception
	 * ```php
	 * $this->assertNotBoolean('$.user');
	 * ```
	 */
	public function assertNotBoolean(string $query): void {
		$this->assertThatType($query, 'boolean', false);
	}

	/**
	 * Assert that the values at the given JSONPath to be number.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is not number.
	 * @throws Exception
	 * ```php
	 * $this->assertNumber('$.user');
	 * ```
	 */
	public function assertNumber(string $query): void {
		$this->assertThatType($query, 'number', true);
	}

	/**
	 * Assert that none elements at the given JSONPath are number.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is number.
	 * @throws Exception
	 * ```php
	 * $this->assertNotNumber('$.user');
	 * ```
	 */
	public function assertNotNumber(string $query): void {
		$this->assertThatType($query, 'number', false);
	}

	/**
	 * Assert that the values at the given JSONPath to be string.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is not string.
	 * @throws Exception
	 * ```php
	 * $this->assertString('$.user');
	 * ```
	 */
	public function assertString(string $query): void {
		$this->assertThatType($query, 'string', true);
	}

	/**
	 * Assert that none elements at the given JSONPath are string.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is string.
	 * @throws Exception
	 * ```php
	 * $this->assertNotString('$.user');
	 * ```
	 */
	public function assertNotString(string $query): void {
		$this->assertThatType($query, 'string', false);
	}

	private function assertThatType(string $query, string $expectedType, bool $assert): void {
		$this->assertExists($query);
		$elements = $this->json->get($query);
		$filtered = array_filter(
			$elements,
			fn (mixed $v): string => self::phpToJsonType($v) === $expectedType
		);
		if ($assert)
			$this->test->assertEquals(sizeof($elements), sizeof($filtered), "Expected all elements to be {$expectedType} for the JSONPath \"{$query}\"");
		else
			$this->test->assertEmpty($filtered, "Expected all elements not to be {$expectedType} for the JSONPath \"{$query}\"");
	}

	private static function phpToJsonType(mixed $v): string {
		$type = gettype($v);
		return match ($type) {
			'NULL' => 'null',
			'boolean' => 'boolean',
			'integer', 'double' => 'number',
			'string' => 'string',
			'array' => array_is_list($v) ? 'array' : 'object',
			default => $type
		};
	}
}
