<?php
namespace Stein197\PHPUnit\Assert;

use JsonPath\InvalidJsonPathException;
use JsonPath\JsonObject;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\GeneratorNotSupportedException;
use PHPUnit\Framework\TestCase;
use Stein197\PHPUnit\ExtendedTestCase;
use function array_filter;
use function array_is_list;
use function array_key_exists;
use function gettype;
use function is_array;
use function is_string;
use function json_encode;
use function sizeof;
use function str_contains;

// TODO: assertTextMatchesRegex(string $query, string $regex)
// TODO: assertTextNotMatchesRegex(string $query, string $regex)
// TODO: find(string $query): mixed
// TODO: assert[Not]Equals() should check ALL elements instead of at least single
/**
 * JSON document assertions by JSONPath queries.
 * @package Stein197\PHPUnit\Assert
 * @internal
 */
final readonly class JsonAssert {

	/**
	 * @param TestCase&ExtendedTestCase $test PHPUnit test case object to call assertions from.
	 * @param JsonObject $json JSON object.
	 */
	public function __construct(
		private TestCase & ExtendedTestCase $test,
		private JsonObject $json
	) {}

	/**
	 * Assert that there are `$expectedCount` elements matching the `$query`.
	 * @param string $query JSONPath to find elements by.
	 * @param int $expectedCount Expected amount of elements to find.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * Assert that elements at the given JSONPath are null, false, 0, "", [] or {}.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When the JSONPath does not exist or one of the elements is not null, false, 0, "", [] or {}.
	 * @throws GeneratorNotSupportedException
	 */
	public function assertEmpty(string $query): void {
		$this->assertExists($query);
		foreach ($this->json->get($query) as $i => $item)
			$this->test->assertEmpty($item, "Expected to find an empty element at position {$i} matching the JSONPath \"{$query}\", actual: " . json_encode($item));
	}

	/**
	 * Assert that elements at the given JSONPath are not null, false, 0, "", [] or {}.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When the JSONPath does not exist or one of the elements is null, false, 0, "", [] or {}.
	 * @throws GeneratorNotSupportedException
	 */
	public function assertNotEmpty(string $query): void {
		$this->assertExists($query);
		foreach ($this->json->get($query) as $i => $item)
			$this->test->assertNotEmpty($item, "Expected to find a non-empty element at position {$i} matching the JSONPath \"{$query}\", actual: " . json_encode($item));
	}

	/**
	 * Assert that there is at least one element matching the `$query`.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws Exception
	 * @throws ExpectationFailedException When JSONPath does not exist or none of the values equal to the passed one.
	 * ```php
	 * $this->assertEquals('$.user', ['name' => 'John']);
	 * ```
	 */
	public function assertEquals(string $query, mixed $value): void {
		$this->assertExists($query);
		$elements = $this->json->get($query);
		$this->test->assertContains($value, $elements, 'Expected to find at least one element with the exact value ' . json_encode($value) . " matching the JSONPath \"{$query}\"");
	}

	/**
	 * Assert that the value at the given JSONPath is not equal to the passed one.
	 * @param string $query JSONPath to find elements by.
	 * @param mixed $value Not expected value.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws Exception
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements equal to the passed one.
	 * ```php
	 * $this->assertNotEquals('$.user', ['name' => 'John']);
	 * ```
	 */
	public function assertNotEquals(string $query, mixed $value): void {
		$this->assertExists($query);
		$elements = $this->json->get($query);
		$this->test->assertNotContains($value, $elements, 'Expected to find none elements with the exact value ' . json_encode($value) . " matching the JSONPath \"{$query}\"");
	}

	/**
	 * Assert that at least one element at the given JSONPath contains the passed value. If the `$value` is string, it
	 * asserts for strings that contain the value as a substring. If it's an array, it asserts for arrays (maps) that
	 * contain the value as a subset (partial comparison).
	 * @param string $query JSONPath to find elements by.
	 * @param string|array $value Expected value.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist.
	 * @throws AssertionFailedError When there are no matches.
	 * ```php
	 * $this->assertContains('$.user.name', 'john');
	 * $this->assertContains('$.user', ['age' => 12]);
	 * ```
	 */
	public function assertContains(string $query, string | array $value): void {
		$this->assertExists($query);
		foreach ($this->json->get($query) as $element)
			if (self::contains($element, $value)) {
				$this->test->pass();
				return;
			}
		$this->test->fail("Expected to find at least one element matching the JSONPath \"{$query}\" and containing " . json_encode($value));
	}

	/**
	 * Assert that no elements at the given JSONPath contain the passed value. If the `$value` is string, it asserts for
	 * strings that contain the value as a substring. If it's an array, it asserts for arrays (maps) that contain the
	 * value as a subset (partial comparison).
	 * @param string $query JSONPath to find elements by.
	 * @param string|array $value Value not to expect.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist.
	 * @throws AssertionFailedError When there is at least one element containing the value.
	 * ```php
	 * $this->assertNotContains('$.user.name', 'john');
	 * $this->assertNotContains('$.user', ['age' => 12]);
	 * ```
	 */
	public function assertNotContains(string $query, string | array $value): void {
		$this->assertExists($query);
		foreach ($this->json->get($query) as $element)
			if (self::contains($element, $value))
				$this->test->fail("Expected to find no elements matching the JSONPath \"{$query}\" and containing " . json_encode($value));
		$this->test->pass();
	}

	/**
	 * Assert that the values at the given JSONPath to be null.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
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
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is string.
	 * @throws Exception
	 * ```php
	 * $this->assertNotString('$.user');
	 * ```
	 */
	public function assertNotString(string $query): void {
		$this->assertThatType($query, 'string', false);
	}

	/**
	 * Assert that the elements at the given JSONPath to be array. Empty JSON objects ({}) are considered as arrays.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is not array.
	 * @throws GeneratorNotSupportedException
	 */
	public function assertArray(string $query): void {
		$this->assertThatType($query, 'array', true);
	}

	/**
	 * Assert that none elements at the given JSONPath are array. Empty JSON objects ({}) are considered as arrays.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is array.
	 * @throws GeneratorNotSupportedException
	 */
	public function assertNotArray(string $query): void {
		$this->assertThatType($query, 'array', false);
	}

	/**
	 * Assert that the elements at the given JSONPath to be objects. Empty JSON objects ({}) are considered as arrays.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is not object.
	 * @throws GeneratorNotSupportedException
	 */
	public function assertObject(string $query): void {
		$this->assertThatType($query, 'object', true);
	}

	/**
	 * Assert that none elements at the given JSONPath are objects. Empty JSON objects ({}) are considered as arrays.
	 * @param string $query JSONPath to find elements by.
	 * @return void
	 * @throws InvalidJsonPathException When JSONPath is invalid.
	 * @throws ExpectationFailedException When JSONPath does not exist or one of the elements is object.
	 * @throws GeneratorNotSupportedException
	 */
	public function assertNotObject(string $query): void {
		$this->assertThatType($query, 'object', false);
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

	private static function isSubsetOf(array $superset, array $subset): bool {
		foreach ($subset as $k => $subsetValue) {
			if (!array_key_exists($k, $superset))
				return false;
			$supersetValue = $superset[$k];
			if (gettype($supersetValue) !== gettype($subsetValue) || (is_array($subsetValue) ? !self::isSubsetOf($supersetValue, $subsetValue) : $supersetValue !== $subsetValue))
				return false;
		}
		return true;
	}

	private static function contains(mixed $superset, mixed $subset): bool {
		return is_string($subset) && is_string($superset) && str_contains($superset, $subset) || is_array($subset) && is_array($superset) && self::isSubsetOf($superset, $subset);
	}
}
