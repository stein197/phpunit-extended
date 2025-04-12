<?php
namespace Stein197\PHPUnit\Assert;

use Dom\Document;
use Dom\Element;
use Dom\XPath;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Stein197\PHPUnit\ExtendedTestCase;
use function explode;
use function http_build_query;
use function parse_str;
use function Stein197\PHPUnit\array_is_subset;

// TODO: within(string $xpath, callable $f) - use query(, $contextNode)
/**
 * HTML/XML document assertions.
 * @package Stein197\PHPUnit\Assert
 * @internal
 */
final readonly class DocumentAssert {

	private XPath $xpath;

	/**
	 * @param TestCase&ExtendedTestCase $test PHPUnit test case object to call assertions from.
	 * @param Document $doc HTML/XML document.
	 */
	public function __construct(
		private TestCase & ExtendedTestCase $test,
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

	/**
	 * Assert that there is at least one `<a>` element with the expected URL, query and hash.
	 * @param string $path Path to expect. The part before `?` and `#`.
	 * @param array $query Query parameters to expect. Empty array means no query string. The comparison is done
	 *                     partially. All values and keys automatically get encoded and decoded. The array can be nested.
	 *                     Only string values are allowed.
	 * @param null|string $hash Hash to expect. Null means no hash part at all. Empty string denotes only the single `#` character.
	 * @return void
	 * @throws AssertionFailedError When there are no anchor elements with the passed href.
	 * ```php
	 * $this->assertAnchorExists('/url', ['a' => ['b' => 2]], 'hash'); // Expect '/url?a[b]=2#hash' href
	 * ```
	 */
	public function assertAnchorExists(string $path, array $query = [], ?string $hash = null): void {
		$elements = $this->xpath->query('//a[@href]');
		foreach ($elements as $a) {
			if (!$a instanceof Element)
				continue;
			if (self::isHrefEquals($a, $path, $query, $hash)) {
				$this->test->pass();
				return;
			}
		}
		$this->test->fail('Expected to find at least one <a> with href "' . self::toHref($path, $query, $hash) . '"');
	}

	/**
	 * Assert that there is no `<a>` element with the expected URL, query and hash.
	 * @param string $path Path not to expect. The part before `?` and `#`.
	 * @param array $query Query parameters not to expect. Empty array means no query string. The comparison is done
	 *                     partially. All values and keys automatically get encoded and decoded. The array can be nested.
	 *                     Only string values are allowed.
	 * @param null|string $hash Hash not to expect. Null means no hash part at all. Empty string denotes only the single `#` character.
	 * @return void
	 * @throws AssertionFailedError When there is an element with the passed href.
	 * ```php
	 * $this->assertAnchorNotExists('/url', ['a' => ['b' => 2]], 'hash'); // Not expecting '/url?a[b]=2#hash' href
	 * ```
	 */
	public function assertAnchorNotExists(string $path, array $query = [], ?string $hash = null): void {
		$elements = $this->xpath->query('//a[@href]');
		foreach ($elements as $a) {
			if (!$a instanceof Element)
				continue;
			$this->test->assertFalse(self::isHrefEquals($a, $path, $query, $hash), 'Expected to find no <a> with href "' . self::toHref($path, $query, $hash) . '"');
		}
		$this->test->pass();
	}

	private static function parseHref(string $href): array {
		@[$path, $hash] = explode('#', $href, 2);
		@[$path, $query] = explode('?', $path, 2);
		$queryArray = [];
		if ($query)
			parse_str($query, $queryArray);
		return [$path, $queryArray, $hash];
	}

	private static function toHref(string $path, array $query, ?string $hash): string {
		return $path . ($query ? ('?' . http_build_query($query)) : '') . ($hash === null ? '' : ('#' . $hash));
	}

	private function isHrefEquals(Element $a, string $path, array $query, ?string $hash): bool {
		[$hrefPath, $hrefQuery, $hrefHash] = self::parseHref($a->getAttribute('href'));
		return $path === $hrefPath && array_is_subset($hrefQuery, $query) && $hash === $hrefHash;
	}
}
