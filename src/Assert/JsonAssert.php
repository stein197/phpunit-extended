<?php
namespace Stein197\PHPUnit\Assert;

use JsonPath\JsonObject;
use PHPUnit\Framework\TestCase;

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
}
