<?php
namespace Stein197\PHPUnit\Assert;

use Dom\Document;
use Dom\Node;
use Dom\XPath;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function array_map;
use function array_reduce;

// TODO: Delete
final class XPathAssert {

	private XPath $xpath;

	public function __construct(
		private TestCase $test,
		Document $doc
	) {
		$this->xpath = new XPath($doc);
	}

	/**
	 * Assert that the given xpath has `$count` child elements.
	 * @param string $xpath Xpath to find elements by.
	 * @param int $count Children count to expect.
	 * @return void
	 * @throws AssertionFailedError When there are no elements matching the given xpath.
	 * @throws ExpectationFailedException When the expected amount of children does not match the expected one.
	 * ```php
	 * $this->assertChildrenCount('//p', 1);
	 * ```
	 */
	public function assertChildrenCount(string $xpath, int $count): void {
		$actual = $this->getChildrenCount($xpath);
		$this->test->assertEquals($count, $actual, "Expected to find {$count} child elements for the xpath \"{$xpath}\", actual: {$actual}");
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
	 * Assert that the given xpath has no children. The same as `$this->assertChildrenCount($xpath, 0)`.
	 * @param string $xpath Xpath to find elements by.
	 * @return void
	 * @throws AssertionFailedError When there are no elements matching the given xpath.
	 * @throws ExpectationFailedException When the given xpath has child elements.
	 * ```php
	 * $this->assertEmpty('//p');
	 * ```
	 */
	public function assertEmpty(string $xpath): void {
		$this->assertChildrenCount($xpath, 0);
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

	private function getChildrenCount(string $xpath): int {
		$elements = $this->xpath->query($xpath);
		if (!$elements->count())
			$this->test->fail("Expected to find at least one element matching the xpath \"{$xpath}\"");
		return array_reduce(
			array_map(
				fn (Node $node): int => $node->childNodes->count(),
				[...$elements]
			),
			fn (int $prev, int $cur): int => $prev + $cur,
			0
		);
	}
}
