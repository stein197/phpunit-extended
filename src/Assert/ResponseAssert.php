<?php
namespace Stein197\PHPUnit\Assert;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\GeneratorNotSupportedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Stein197\PHPUnit\ExtendedTestCase;
use function explode;
use function join;
use function preg_split;
use function trim;
use const PREG_SPLIT_NO_EMPTY;

/**
 * PSR-7 response assertions.
 * @package Stein197\PHPUnit\Assert
 * @internal
 */
final class ResponseAssert {

	private ?DocumentAssert $doc = null;
	private ?JsonAssert $json = null;

	public function __construct(
		private TestCase & ExtendedTestCase $test,
		private ResponseInterface $response
	) {}

	/**
	 * Assert that the response content contains the given content.
	 * @param string $content Expected content.
	 * @return void
	 * @throws RuntimeException Error while reading body contents.
	 * @throws ExpectationFailedException If the response content does not contain the given one.
	 * ```php
	 * $this->assertContentContains('Hello, World!');
	 * ```
	 */
	public function assertContentContains(string $content): void {
		$actual = $this->getResponseContents();
		$this->test->assertStringContainsString($content, $actual, "Expected the response to contain the content \"{$content}\", actual: \"{$actual}\"");
	}

	/**
	 * Assert that the response content does not contain the given content.
	 * @param string $content Expected content.
	 * @return void
	 * @throws RuntimeException Error while reading body contents.
	 * @throws ExpectationFailedException If the response content contains the given one.
	 * ```php
	 * $this->assertContentNotContains('Hello, World!');
	 * ```
	 */
	public function assertContentNotContains(string $content): void {
		$actual = $this->getResponseContents();
		$this->test->assertStringNotContainsString($content, $actual, "Expected the response not to contain the content \"{$content}\", actual: \"{$actual}\"");
	}

	/**
	 * Assert that the response content equals to the given content.
	 * @param string $content Expected content.
	 * @return void
	 * @throws RuntimeException Error while reading body contents.
	 * @throws ExpectationFailedException If the response content does not equal to the given one.
	 * ```php
	 * $this->assertContentEquals('Hello, World!');
	 * ```
	 */
	public function assertContentEquals(string $content): void {
		$actual = $this->getResponseContents();
		$this->test->assertEquals($content, $actual, "Expected the response to have the content \"{$content}\", actual: \"{$actual}\"");
	}

	/**
	 * Assert that the response content does not equal to the given content.
	 * @param string $content Expected content.
	 * @return void
	 * @throws RuntimeException Error while reading body contents.
	 * @throws ExpectationFailedException If the response content equals to the given one.
	 * ```php
	 * $this->assertContentNotEquals('Hello, World!');
	 * ```
	 */
	public function assertContentNotEquals(string $content): void {
		$actual = $this->getResponseContents();
		$this->test->assertNotEquals($content, $actual, "Expected the response not to have the content \"{$content}\"");
	}

	/**
	 * Assert that the response content matches the given regular expression.
	 * @param string $regex Regular expression to match against.
	 * @return void
	 * @throws RuntimeException Error while reading body contents.
	 * @throws ExpectationFailedException If the response content does not match the given regular expression.
	 * ```php
	 * $this->assertContentRegex('/hello/i');
	 * ```
	 */
	public function assertContentRegex(string $regex): void {
		$content = $this->getResponseContents();
		$this->test->assertMatchesRegularExpression($regex, $content, "Expected the response to match the regular expression \"{$regex}\", actual: \"{$content}\"");
	}

	/**
	 * Assert that the response content does not match the given regular expression.
	 * @param string $regex Regular expression to match against.
	 * @return void
	 * @throws RuntimeException Error while reading body contents.
	 * @throws ExpectationFailedException If the response content matches the given regular expression.
	 * ```php
	 * $this->assertContentNotRegex('/hello/i');
	 * ```
	 */
	public function assertContentNotRegex(string $regex): void {
		$content = $this->getResponseContents();
		$this->test->assertDoesNotMatchRegularExpression($regex, $content, "Expected the response not to match the regular expression \"{$regex}\", actual: \"{$content}\"");
	}

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
	 * Assert that the response has the given cookie with the given value.
	 * @param string $name Cookie name.
	 * @param string $value Expected cookie value.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have a cookie with the given name and value.
	 * ```php
	 * $this->assertCookieEquals('ga', 'XXX');
	 * ```
	 */
	public function assertCookieEquals(string $name, string $value): void {
		$this->test->assertEquals($value, @$this->getResponseCookies()[$name], "Expected the response to have the cookie \"{$name}\" with the value \"{$value}\"");
	}

	/**
	 * Assert that the response does not have the given cookie with the given value.
	 * @param string $name Cookie name.
	 * @param string $value Expected cookie value.
	 * @return void
	 * @throws ExpectationFailedException If the response has a cookie with the given name and value.
	 * ```php
	 * $this->assertCookieNotEquals('ga', 'XXX');
	 * ```
	 */
	public function assertCookieNotEquals(string $name, string $value): void {
		$this->test->assertNotEquals($value, @$this->getResponseCookies()[$name], "Expected the response not to have the cookie \"{$name}\" with the value \"{$value}\"");
	}

	/**
	 * Assert that the response has the given cookie.
	 * @param string $name Cookie name.
	 * @return void
	 * @throws ExpectationFailedException If the response does not have a cookie with the given name.
	 * ```php
	 * $this->assertCookieExists('ga');
	 * ```
	 */
	public function assertCookieExists(string $name): void {
		$this->test->assertArrayHasKey($name, $this->getResponseCookies(), "Expected the response to have the cookie \"{$name}\"");
	}

	/**
	 * Assert that the response does not have the given cookie.
	 * @param string $name Cookie name.
	 * @return void
	 * @throws ExpectationFailedException If the response has a cookie with the given name.
	 * ```php
	 * $this->assertCookieNotExists('ga');
	 * ```
	 */
	public function assertCookieNotExists(string $name): void {
		$this->test->assertArrayNotHasKey($name, $this->getResponseCookies(), "Expected the response not to have the cookie \"{$name}\"");
	}

	/**
	 * Assert that the response is a download one.
	 * @param null|string $name Optional filename to download.
	 * @return void
	 * @throws AssertionFailedError When there is no header Content-Disposition or the name does not equal to the given one.
	 * ```php
	 * $this->assertDownload('file.txt');
	 * ```
	 */
	public function assertDownload(?string $name = null): void {
		$this->assertHeaderExists('Content-Disposition');
		$parts = preg_split('/\\s*;\\s*/', $this->response->getHeader('Content-Disposition')[0], -1, PREG_SPLIT_NO_EMPTY);
		$type = $parts[0];
		$this->test->assertEquals('attachment', $type, "Expected the response to have the Content-Disposition header to be attachment, actual: {$type}");
		if (!$name)
			return;
		foreach ($parts as $part) {
			$kv = explode('=', $part);
			if ($kv[0] !== 'filename' || !isset($kv[1]))
				continue;
			$filename = trim($kv[1], '"');
			$this->test->assertEquals($name, $filename, "Expected the response to download a file \"{$name}\", actual: \"{$filename}\"");
			return;
		}
		$this->test->fail("Expected the response to download a file \"{$name}\"");
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
		$values = $this->response->getHeader($header);
		$this->test->assertContains($value, $values, "Expected the response to have the header \"{$header}\" with value \"{$value}\", actual: \"" . join('", "', $values) . '"');
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

	/**
	 * Assert that the response is a valid JSON.
	 * @return void
	 * @throws ExpectationFailedException If the content-type is not "application/json" or the content contains invalid JSON.
	 * @throws AssertionFailedError If the content-type is not "application/json" or the content contains invalid JSON.
	 * @throws RuntimeException
	 * ```php
	 * $this->assertJson();
	 * ```
	 */
	public function assertJson(): void {
		$this->assertContentType('application/json');
		$this->test->assertJson($this->getResponseContents(), 'Expected the response to have a valid JSON');
	}

	/**
	 * Return a document assertion object containing the response content.
	 * @return DocumentAssert Document assertion object.
	 * @throws RuntimeException
	 * @throws AssertionFailedError If the content-type is not `text/html` nor `text/xml`.
	 * ```php
	 * $this->document()->query('body p')->assertExists();
	 * ```
	 */
	public function document(): DocumentAssert {
		if ($this->doc)
			return $this->doc;
		$contentType = @$this->response->getHeader('Content-Type')[0];
		if ($contentType === 'text/html')
			return $this->doc = $this->test->html($this->getResponseContents());
		if ($contentType === 'text/xml')
			return $this->doc = $this->test->xml($this->getResponseContents());
		$this->test->fail("Expected the response to have content-type of either \"text/html\" or \"text/xml\", actual: \"{$contentType}\"");
	}

	/**
	 * Return a JSON assertion object containing the response content.
	 * @return JsonAssert JSON assertion object.
	 * @throws AssertionFailedError If the content-type is not `application/json`.
	 * ```php
	 * $this->json()->assertCount('$.books[*]', 10);
	 * ```
	 */
	public function json(): JsonAssert {
		if ($this->json)
			return $this->json;
		$contentType = @$this->response->getHeader('Content-Type')[0];
		if ($contentType === 'application/json')
			return $this->json = $this->test->json($this->getResponseContents());
		$this->test->fail("Expected the response to have content-type \"application/json\", actual: \"{$contentType}\"");
	}

	private function getResponseContents(): string {
		$body = $this->response->getBody();
		$body->rewind();
		return $body->getContents();
	}

	private function getResponseCookies(): array {
		$result = [];
		$cookies = $this->response->getHeader('Set-Cookie');
		foreach ($cookies as $value) {
			[$k, $v] = explode('=', preg_split('/\\s*;\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY)[0]);
			$result[$k] = $v;
		}
		return $result;
	}
}
