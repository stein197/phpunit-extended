<?php
namespace Stein197\PHPUnit;

use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\Assert\DocumentAssert;
use Stein197\PHPUnit\Assert\JsonAssert;
use Stein197\PHPUnit\Assert\ResponseAssert;

/**
 * Extended PHPUnit assertions.
 */
interface ExtendedTestCaseInterface {

	/**
	 * Return an assertion object to test response objects.
	 * @param ResponseInterface $response PSR-7 response object.
	 * @return ResponseAssert Assertion object.
	 * ```php
	 * $this->createResponseAssertion(new Response(...))->assertStatus(200);
	 * ```
	 */
	public function createResponseAssertion(ResponseInterface $response): ResponseAssert;

	/**
	 * Return an assertion object to test JSON structures by JSONPath.
	 * @param string $json JSON string.
	 * @return JsonAssert JSON assertion object.
	 * ```php
	 * $this->createJsonAssertion('{"name":"John"}')->assertExists('$.name');
	 * ```
	 */
	public function createJsonAssertion(string $json): JsonAssert;

	/**
	 * Return an assertion object to test HTML structures.
	 * @param string $html HTML string.
	 * @param bool $error Show parsing error messages.
	 * @return DocumentAssert Assertion object.
	 * ```php
	 * $this->createHtmlAssertion(('<p></p>')->query('p')->assertCount(1);
	 * ```
	 */
	public function createHtmlAssertion(string $html, bool $error = false): DocumentAssert;

	/**
	 * Return an assertion object to test XML structures.
	 * @param string $xml XML string.
	 * @param bool $error Show parsing error messages.
	 * @return DocumentAssert Assertion object.
	 * ```php
	 * $this->createXmlAssertion(('<p></p>')->xpath('//p')->assertCount(1);
	 * ```
	 */
	public function createXmlAssertion(string $xml, bool $error = false): DocumentAssert;

	/**
	 * Mark test as passed.
	 * @return void
	 */
	public function pass(): void;
}
