<?php
namespace Stein197\PHPUnit;

use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\Assert\DocumentAssert;
use Stein197\PHPUnit\Assert\JsonAssert;
use Stein197\PHPUnit\Assert\ResponseAssert;

/**
 * Extended PHPUnit assertions.
 */
interface ExtendedTestCase {

	/**
	 * Return an assertion object to test response objects.
	 * @param ResponseInterface $response PSR-7 response object.
	 * @return ResponseAssert Assertion object.
	 * ```php
	 * $this->request(new Response(...))->assertStatus(200);
	 * ```
	 */
	public function response(ResponseInterface $response): ResponseAssert;

	/**
	 * Return an assertion object to test JSON structures by JSONPath.
	 * @param string $json JSON string.
	 * @return JsonAssert JSON assertion object.
	 * ```php
	 * $this->json('{"name":"John"}')->assertExists('$.name');
	 * ```
	 */
	public function json(string $json): JsonAssert;

	/**
	 * Return an assertion object to test HTML structures.
	 * @param string $html HTML string.
	 * @return DocumentAssert Assertion object.
	 * ```php
	 * $this->html('<p></p>')->query('p')->assertCount(1);
	 * ```
	 */
	public function html(string $html): DocumentAssert;

	/**
	 * Return an assertion object to test XML structures.
	 * @param string $xml XML string.
	 * @return DocumentAssert Assertion object.
	 * ```php
	 * $this->xml('<p></p>')->xpath('//p')->assertCount(1);
	 * ```
	 */
	public function xml(string $xml): DocumentAssert;
}
