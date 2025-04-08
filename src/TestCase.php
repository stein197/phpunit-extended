<?php
namespace Stein197\PHPUnit;

use Dom\HTMLDocument;
use Dom\XMLDocument;
use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\Assert\DocumentAssert;
use Stein197\PHPUnit\Assert\ResponseAssert;
use Stein197\PHPUnit\Assert\XPathAssert;
use const Dom\HTML_NO_DEFAULT_NS;

/**
 * Extended PHPUnit assertions.
 */
trait TestCase {

	/**
	 * @inheritdoc
	 */
	public function response(ResponseInterface $response): ResponseAssert {
		return new ResponseAssert($this, $response);
	}

	/**
	 * @inheritdoc
	 */
	public function html(string $html): DocumentAssert {
		return new DocumentAssert($this, HTMLDocument::createFromString($html, HTML_NO_DEFAULT_NS));
	}

	/**
	 * @inheritdoc
	 */
	public function xml(string $xml): DocumentAssert {
		return new DocumentAssert($this, XMLDocument::createFromString($xml));
	}

	/**
	 * @inheritdoc
	 */
	public function xpathHtml(string $html): XPathAssert {
		return new XPathAssert($this, HTMLDocument::createFromString($html, HTML_NO_DEFAULT_NS));
	}

	/**
	 * @inheritdoc
	 */
	public function xpathXml(string $xml): XPathAssert {
		return new XPathAssert($this, XMLDocument::createFromString($xml));
	}
}
