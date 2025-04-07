<?php
namespace Stein197\PHPUnit;

use Psr\Http\Message\ResponseInterface;

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
	 * Return an assertion object to test HTML structure.
	 * @param string $html HTML string.
	 * @return XPathAssert Assertion object.
	 * ```php
	 * $this->xpathHtml('<p></p>')->assertCount('//p', 1);
	 * ```
	 */
	public function xpathHtml(string $html): XPathAssert;

	/**
	 * Return an assertion object to test XML structure.
	 * @param string $xml XML string.
	 * @return XPathAssert Assertion object.
	 * ```php
	 * $this->xpathXml('<p></p>')->assertCount('//p', 1);
	 * ```
	 */
	public function xpathXml(string $xml): XPathAssert;
}
