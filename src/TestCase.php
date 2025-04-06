<?php
namespace Stein197\PHPUnit;

use Dom\HTMLDocument;
use Dom\XMLDocument;
use Psr\Http\Message\ResponseInterface;
use const Dom\HTML_NO_DEFAULT_NS;

/**
 * Extended PHPUnit assertions.
 */
trait TestCase {

	/**
	 * Return an assertion object to test response objects.
	 * @param ResponseInterface $response PSR-7 response object.
	 * @return ResponseAssert Assertion object.
	 * ```php
	 * $this->request(new Response(...))->assertStatus(200);
	 * ```
	 */
	public function response(ResponseInterface $response): ResponseAssert {
		return new ResponseAssert($this, $response);
	}

	/**
	 * Return an assertion object to test HTML structure.
	 * @param string $html HTML string.
	 * @return XPathAssert Assertion object.
	 * ```php
	 * $this->xpathHtml('<p></p>')->assertCount('//p', 1);
	 * ```
	 */
	public function xpathHtml(string $html): XPathAssert {
		return new XPathAssert($this, HTMLDocument::createFromString($html, HTML_NO_DEFAULT_NS));
	}

	/**
	 * Return an assertion object to test XML structure.
	 * @param string $xml XML string.
	 * @return XPathAssert Assertion object.
	 * ```php
	 * $this->xpathXml('<p></p>')->assertCount('//p', 1);
	 * ```
	 */
	public function xpathXml(string $xml): XPathAssert {
		return new XPathAssert($this, XMLDocument::createFromString($xml));
	}
}
