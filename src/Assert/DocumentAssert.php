<?php
namespace Stein197\PHPUnit\Assert;

use Dom\Document;
use Dom\XPath;
use PHPUnit\Framework\TestCase;

// TODO: assertAnchorExists(string $href, array $query = [], ?string $hash = null)
// TODO: assertAnchorNotExists(string $href, array $query = [], ?string $hash = null)
// TODO: within(string $xpath, callable $f) - use query(, $contextNode)
/**
 * HTML/XML document assertions.
 * @package Stein197\PHPUnit\Assert
 * @internal
 */
final readonly class DocumentAssert {

	private XPath $xpath;

	/**
	 * @param TestCase $test PHPUnit test case object to call assertions from.
	 * @param Document $doc HTML/XML document.
	 */
	public function __construct(
		private TestCase $test,
		private Document $doc
	) {
		$this->xpath = new XPath($doc);
	}

	/**
	 * Return an assertion object to test a structure based on an xpath query.
	 * @param string $xpath Xpath to find elements by.
	 * @return NodeListAssert Assertion object containing matched nodes.
	 * ```php
	 * $this->xpath('//p');
	 * ```
	 */
	public function xpath(string $xpath): NodeListAssert {
		return new NodeListAssert($this->test, $this->xpath->query($xpath), $xpath);
	}

	/**
	 * Return an assertion object to test a structure based on a query selector.
	 * @param string $query Query selector to find elements by.
	 * @return NodeListAssert Assertion object containing matched nodes.
	 * ```php
	 * $this->query('#main');
	 * ```
	 */
	public function query(string $query): NodeListAssert {
		return new NodeListAssert($this->test, $this->doc->querySelectorAll($query), $query);
	}
}
