<?php
namespace Stein197\PHPUnit\Assert;

use Dom\Node;
use Dom\NodeList;
use PHPUnit\Framework\TestCase;

// TODO: assertContains(string $xpath, string $content)
// TODO: assertNotContains(string $xpath, string $content)
// TODO: assertRegexExists(string $xpath, string $regex)
// TODO: assertRegexNotExists(string $xpath, string $regex)
/**
 * HTML/XML nodes assertions.
 * @package Stein197\PHPUnit\Assert
 * @internal
 */
final readonly class NodeListAssert {

	/**
	 * @param TestCase $test PHPUnit test case object to call assertions from.
	 * @param NodeList $nodeList HTML/XML nodes.
	 * @param string $query Query used to find the list. Only for debugging purpose.
	 */
	public function __construct(
		private TestCase $test,
		private NodeList $nodeList,
		private string $query
	) {}

	/**
	 * Assert that there are child elements.
	 * @param int $count Children count to expect.
	 * @return void
	 * @throws AssertionFailedError When there are no nodes in the list at all.
	 * @throws ExpectationFailedException When the expected amount of children does not match the expected one.
	 * ```php
	 * $this->assertChildrenCount(1);
	 * ```
	 */
	public function assertChildrenCount(int $count): void {
		$actual = $this->getChildrenCount();
		$this->test->assertEquals($count, $actual, "Expected to find {$count} child elements for the query \"{$this->query}\", actual: {$actual}");
	}

	/**
	 * Assert that there are `$expectedCount` elements in the list.
	 * @param int $expectedCount Expected amount of elements to find.
	 * @return void
	 * @throws ExpectationFailedException If the amount of the found elements is not equal to the `$expectedCount`.
	 * ```php
	 * $this->assertCount(1);
	 * ```
	 */
	public function assertCount(int $expectedCount): void {
		$length = $this->nodeList->count();
		$this->test->assertEquals($expectedCount, $length, "Expected to find {$expectedCount} elements matching the query \"{$this->query}\", actual: {$length}");
	}

	/**
	 * Assert that the list elements have no children. The same as `$this->assertChildrenCount(0)`.
	 * @return void
	 * @throws AssertionFailedError When there are no nodes in the list at all.
	 * @throws ExpectationFailedException When the list elements have no child elements.
	 * ```php
	 * $this->assertEmpty();
	 * ```
	 */
	public function assertEmpty(): void {
		$this->assertChildrenCount(0);
	}

	/**
	 * Assert that there is at least one child.
	 * @return void
	 * @throws AssertionFailedError When there are no children.
	 * @throws ExpectationFailedException When there are no nodes in the list at all.
	 * ```php
	 * $this->assertNotEmpty();
	 * ```
	 */
	public function assertNotEmpty(): void {
		$actual = $this->getChildrenCount();
		$this->test->assertGreaterThan(0, $actual, "Expected to find at least one child element matching the query \"{$this->query}\"");
	}

	/**
	 * Assert that elements exist.
	 * @return void
	 * @throws ExpectationFailedException If there are no elements.
	 * ```php
	 * $this->assertExists();
	 * ```
	 */
	public function assertExists(): void {
		$length = $this->nodeList->count();
		$this->test->assertGreaterThan(0, $length, "Expected to find at least one element matching the query \"{$this->query}\"");
	}

	/**
	 * Assert that there are no elements in the list at all.
	 * @return void
	 * @throws ExpectationFailedException If there is at least one element in the list.
	 * ```php
	 * $this->assertNotExists();
	 * ```
	 */
	public function assertNotExists(): void {
		$this->assertCount(0);
	}

	/**
	 * Assert that at least one element has the given text.
	 * @param string $text Text to expect.
	 * @return void
	 * @throws ExpectationFailedException If there are no elements with the given text or the elements do not exist.
	 * ```php
	 * $this->assertTextEquals('Hello, World!');
	 * ```
	 */
	public function assertTextEquals(string $text): void {
		$this->assertExists();
		$this->test->assertContains($text, $this->getTextContent(), "Expected to find at least one element matching the query \"{$this->query}\" with the text \"{$text}\"");
	}

	/**
	 * Assert that no element has the given text.
	 * @param string $text Text not to expect.
	 * @return void
	 * @throws ExpectationFailedException If there is at least one element with the given text or the elements do not exist.
	 * ```php
	 * $this->assertTextNotEquals('Hello, World!');
	 * ```
	 */
	public function assertTextNotEquals(string $text): void {
		$this->assertExists();
		$this->test->assertNotContains($text, $this->getTextContent(), "Expected to find no elements matching the query \"{$this->query}\" with the text \"{$text}\"");
	}

	private function getChildrenCount(): int {
		if (!$this->nodeList->count())
			$this->test->fail("Expected to find at least one element matching the query \"{$this->query}\"");
		return array_reduce(
			array_map(
				fn (Node $node): int => $node->childNodes->count(),
				[...$this->nodeList]
			),
			fn (int $prev, int $cur): int => $prev + $cur,
			0
		);
	}

	private function getTextContent(): array {
		return array_map(
			fn (Node $node) => $node->textContent,
			[...$this->nodeList]
		);
	}
}
