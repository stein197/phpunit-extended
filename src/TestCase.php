<?php
namespace Stein197\PHPUnit;

use Dom\HTMLDocument;
use Dom\XMLDocument;
use JsonPath\InvalidJsonException;
use JsonPath\JsonObject;
use PHPUnit\Framework\ExpectationFailedException;
use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\Assert\DocumentAssert;
use Stein197\PHPUnit\Assert\JsonAssert;
use Stein197\PHPUnit\Assert\ResponseAssert;
use const Dom\HTML_NO_DEFAULT_NS;
use const LIBXML_NOERROR;

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
	public function json(string $json): JsonAssert {
		try {
			return new JsonAssert($this, new JsonObject($json));
		} catch (InvalidJsonException $ex) {
			$this->fail($ex->getMessage());
		}
	}

	/**
	 * @inheritdoc
	 */
	public function html(string $html, bool $error = true): DocumentAssert {
		return new DocumentAssert($this, HTMLDocument::createFromString($html, HTML_NO_DEFAULT_NS | ($error ? 0 : LIBXML_NOERROR)));
	}

	/**
	 * @inheritdoc
	 */
	public function xml(string $xml, bool $error = true): DocumentAssert {
		return new DocumentAssert($this, XMLDocument::createFromString($xml, $error ? 0 : LIBXML_NOERROR));
	}

	/**
	 * @inheritdoc
	 */
	public function pass(): void {
		try {
			$this->assertTrue(true);
		} catch (ExpectationFailedException $ex) {}
	}
}
