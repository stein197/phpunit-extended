<?php
namespace Stein197\PHPUnit;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

// TODO: assertContentEquals
// TODO: assertContentNotEquals
// TODO: assertContentRegex
// TODO: assertContentNotRegex
// TODO: assertContentContains
// TODO: assertContentNotContains
// TODO: assertCookieEquals
// TODO: assertCookieExists
// TODO: assertCookieNotExists
// TODO: assertHeaderExists
// TODO: assertHeaderNotExists
// TODO: assertHeaderEquals
// TODO: assertHeaderNotEquals
// TODO: assertNotFound
// TODO: assertRedirect
// TODO: ?assertDownload, assertFile etc.
final class RequestAssert {

	public function __construct(
		private TestCase $test,
		private ResponseInterface $response
	) {}

	/**
	 * Assert that the response status code equals to 200. The same as the `assertStatus(200)`.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the status code of 200.
	 */
	public function assertOk(): void {
		$this->assertStatus(200);
	}

	/**
	 * Assert that the response status code equals to the `$status`.
	 * @param int $status Expected status.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the status code of `$status`.
	 * ```php
	 * $this->assertStatus(200);
	 * ```
	 */
	public function assertStatus(int $status): void {
		$actual = $this->response->getStatusCode();
		$this->test->assertEquals($status, $actual, "Expected the response to have the status {$status}, actual: {$actual}");
	}
}
