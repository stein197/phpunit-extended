<?php
namespace Stein197\PHPUnit\Assert;

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
 */
final readonly class NodeListAssert {

	/**
	 * @param TestCase $test PHPUnit test case object to call assertions from.
	 * @param NodeList $nodeList HTML/XML nodes.
	 */
	public function __construct(
		private TestCase $test,
		private NodeList $nodeList
	) {}
}