<?php
namespace Stein197\PHPUnit\Assert;

use Dom\Node;
use Dom\NodeList;
use PHPUnit\Framework\TestCase;

// TODO: assertTextNotEquals(string $xpath, string $content)
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
	 * Assert that there is at least one child.
	 * @return void
	 * @throws AssertionFailedError When there are no children.
	 * @throws ExpectationFailedException When there are no nodes at all.
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
	 * Assert that at least one element has the given text.
	 * @param string $text Text to expect.
	 * @return void
	 * @throws ExpectationFailedException
	 * @throws AssertionFailedError If there are no elements that contain the given text.
	 * ```php
	 * $this->assertTextEquals('Hello, World!');
	 * ```
	 */
	public function assertTextEquals(string $text): void {
		$contents = array_map(
			fn (Node $node) => $node->textContent,
			[...$this->nodeList]
		);
		$this->test->assertContains($text, $contents, "Expected to find at least one element matching the query \"{$this->query}\" and containing the text \"{$text}\"");
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
}
