<?php
namespace Stein197\PHPUnit;

use Dom\Document;
use Dom\Node;
use Dom\XPath;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function array_map;

// TODO: assertChildrenCount(string $xpath, int $count)
// TODO: assertEmpty(string $xpath)
// TODO: assertNotEmpty(string $xpath)
// TODO: assertTextNotEquals(string $xpath, string $content)
// TODO: assertContains(string $xpath, string $content)
// TODO: assertNotContains(string $xpath, string $content)
// TODO: assertRegexExists(string $xpath, string $regex)
// TODO: assertRegexNotExists(string $xpath, string $regex)
// TODO: assertAnchorExists(string $href, array $query = [], ?string $hash = null)
// TODO: assertAnchorNotExists(string $href, array $query = [], ?string $hash = null)
// TODO: assertClassCount(string $class, int $expectedCount)
// TODO: within(string $xpath, callable $f) - use query(, $contextNode)
final class XPathAssert {

	private XPath $xpath;

	public function __construct(
		private TestCase $test,
		Document $doc
	) {
		$this->xpath = new XPath($doc);
	}

	/**
	 * Assert that there are `$expectedCount` elements matching the `$xpath`.
	 * @param string $xpath XPath to find elements by.
	 * @param int $expectedCount Expected amount of elements to find by the xpath.
	 * @return void
	 * @throws ExpectationFailedException If the amount of the found elements is not equal to the `$expectedCount`.
	 * ```php
	 * $this->assertCount('//p', 1);
	 * ```
	 */
	public function assertCount(string $xpath, int $expectedCount): void {
		$length = $this->xpath->query($xpath)->count();
		$this->test->assertEquals($expectedCount, $length, "Expected to find {$expectedCount} elements matching the xpath \"{$xpath}\", actual: {$length}");
	}

	/**
	 * Assert that the given xpath exists.
	 * @param string $xpath XPath to find elements by.
	 * @return void
	 * @throws ExpectationFailedException If there are no elements matching the given xpath.
	 * ```php
	 * $this->assertExists('//p');
	 * ```
	 */
	public function assertExists(string $xpath): void {
		$length = $this->xpath->query($xpath)->count();
		$this->test->assertGreaterThan(0, $length, "Expected to find at least one element matching the xpath \"{$xpath}\"");
	}

	/**
	 * Assert that the given xpath does not exist.
	 * @param string $xpath XPath to find elements by.
	 * @return void
	 * @throws ExpectationFailedException If there are at least one element matching the given xpath.
	 * ```php
	 * $this->assertNotExists('//p');
	 * ```
	 */
	public function assertNotExists(string $xpath): void {
		$this->assertCount($xpath, 0);
	}

	/**
	 * Assert that at least one element by the given xpath has the given text.
	 * @param string $xpath XPath to find elements by.
	 * @param string $text Text to expect.
	 * @return void
	 * @throws ExpectationFailedException
	 * @throws AssertionFailedError If there are no elements that contain the given text.
	 * ```php
	 * $this->assertTextEquals('//p', 'Hello, World!');
	 * ```
	 */
	public function assertTextEquals(string $xpath, string $text): void {
		$contents = array_map(
			fn (Node $node) => $node->textContent,
			[...$this->xpath->query($xpath)]
		);
		$this->test->assertContains($text, $contents, "Expected to find at least one element matching the xpath \"{$xpath}\" and containing the text \"{$text}\"");
	}
}
