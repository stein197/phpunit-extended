<?php
namespace Stein197\PHPUnit;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\GeneratorNotSupportedException;
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
// TODO: ?assertDownload, assertFile etc.
final class ResponseAssert {

	public function __construct(
		private TestCase $test,
		private ResponseInterface $response
	) {}

	/**
	 * Assert that the response the given content-type header.
	 * @param string $contentType Expected content-type.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the given content-type header.
	 * @throws AssertionFailedError If the response does not have the given content-type header.
	 * ```php
	 * $this->assertContentType('text/html');
	 * ```
	 */
	public function assertContentType(string $contentType): void {
		$this->assertHeaderEquals('Content-Type', $contentType);
	}

	/**
	 * Assert that the response has the given header with the given value.
	 * @param string $header Expected header. The name can be case-insensetive.
	 * @param string $value Expected value.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the given header with the given value.
	 * @throws AssertionFailedError If the response does not have the given header with the given value.
	 * ```php
	 * $this->assertHeaderEquals('Content-Type', 'text/html');
	 * ```
	 */
	public function assertHeaderEquals(string $header, string $value): void {
		foreach ($this->response->getHeader($header) as $v)
			if ($v === $value) {
				$this->test->assertEquals($value, $v);
				return;
			}
		$this->test->fail("Expected the response to have the header \"{$header}\" with value \"{$value}\"");
	}

	/**
	 * Assert that the response does not have the given header with the given value.
	 * @param string $header Expected header. The name can be case-insensetive.
	 * @param string $value Expected value.
	 * @return void
	 * @throws ExpectationFailedException If the response has the given header with the given value.
	 * ```php
	 * $this->assertHeaderNotEquals('Content-Type', 'text/html');
	 * ```
	 */
	public function assertHeaderNotEquals(string $header, string $value): void {
		foreach ($this->response->getHeader($header) as $v)
			$this->test->assertNotEquals($value, $v, "Expected the response not to have the header \"{$header}\" with value \"{$value}\"");
	}

	/**
	 * Assert that the response has the given header.
	 * @param string $header Expected header. The name can be case-insensetive.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the given header.
	 * @throws GeneratorNotSupportedException
	 * ```php
	 * $this->assertHeaderExists('Content-Type');
	 * ```
	 */
	public function assertHeaderExists(string $header): void {
		$this->test->assertNotEmpty($this->response->getHeader($header), "Expected the response to have the header \"{$header}\"");
	}

	/**
	 * Assert that the response does not have the given header.
	 * @param string $header Expected header. The name can be case-insensetive.
	 * @return void
	 * @throws ExpectationFailedException If the response has the given header.
	 * @throws GeneratorNotSupportedException
	 * ```php
	 * $this->assertHeaderNotExists('Content-Type');
	 * ```
	 */
	public function assertHeaderNotExists(string $header): void {
		$this->test->assertEmpty($this->response->getHeader($header), "Expected the response not to have the header \"{$header}\"");
	}

	/**
	 * Assert that the response status code equals to 404. The same as the `assertStatus(404)`.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the status code of 404.
	 */
	public function assertNotFound(): void {
		$this->assertStatus(404);
	}

	/**
	 * Assert that the response status code equals to 200. The same as the `assertStatus(200)`.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the status code of 200.
	 */
	public function assertOk(): void {
		$this->assertStatus(200);
	}

	/**
	 * Assert that the response status code is between 300 and 400 (or is 201) and the response has the location header.
	 * @param string $url Expected redirection URL.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have the location header or if the response status is not between 300 and 400 (or is not 201).
	 * @throws AssertionFailedError If the response does not have the location header or if the response status is not between 300 and 400 (or is not 201).
	 * ```php
	 * $this->assertRedirect('/redirecting-url');
	 * ```
	 */
	public function assertRedirect(string $url): void {
		$status = $this->response->getStatusCode();
		$this->test->assertTrue($status === 201 || 300 <= $status && $status < 400, "Expected the response to have the status 201 or 3xx, actual: {$status}");
		$this->assertHeaderEquals('Location', $url);
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
