<?php
namespace Test\Assert;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Stein197\PHPUnit\Assert\NodeListAssert;
use Stein197\PHPUnit\ExtendedTestCaseInterface;
use Stein197\PHPUnit\TestCase;

final class NodeListAssertTest extends PHPUnitTestCase implements ExtendedTestCaseInterface {

	use TestCase;

	#[Test]
	#[DataProvider('dataAssertChildrenCount')]
	#[TestDox('assertChildrenCount()')]
	public function testAssertChildrenCount(?string $exceptionMessage, string $format, string $content, string $query, int $expectedCount): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert) use ($expectedCount): void {
			$assert->assertChildrenCount($expectedCount);
		});
	}

	public static function dataAssertChildrenCount(): array {
		return [
			'HTML when xpath exists and there are children' => [null, 'html', '<!DOCTYPE html><body><p></p></body>', 'body', 1],
			'HTML when xpath exists and there are children with text' => [null, 'html', '<!DOCTYPE html><body>text<p></p></body>', 'body', 2],
			'HTML when xpath exists and children mismatch' => ['Expected to find 1 child elements for the query "body", actual: 0', 'html', '<!DOCTYPE html><body></body>', 'body', 1],
			'HTML when xpath not exists' => ['Expected to find at least one element matching the query "p"', 'html', '<!DOCTYPE html><body></body>', 'p', 1],
			'XML when xpath exists and there are children' => [null, 'xml', '<body><p></p></body>', '//body', 1],
			'XML when xpath exists and there are children with text' => [null, 'xml', '<body>text<p></p></body>', '//body', 2],
			'XML when xpath exists and children mismatch' => ['Expected to find 1 child elements for the query "//body", actual: 0', 'xml', '<body></body>', '//body', 1],
			'XML when xpath not exists' => ['Expected to find at least one element matching the query "//p"', 'xml', '<body></body>', '//p', 1],
		];
	}

	#[Test]
	#[DataProvider('dataAssertCount')]
	#[TestDox('assertCount()')]
	public function testAssertCount(?string $exceptionMessage, string $format, string $content, string $query, int $expectedCount): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert) use ($expectedCount): void {
			$assert->assertCount($expectedCount);
		});
	}

	public static function dataAssertCount(): array {
		return [
			'HTML passed' => [null, 'html', '<!DOCTYPE html><body><p></p><p></p></body>', 'body p', 2],
			'HTML failed' => ['Expected to find 2 elements matching the query "body p", actual: 1', 'html', '<!DOCTYPE html><body><p></p></body>', 'body p', 2],
			'XML passed' => [null, 'xml', '<body><p></p><p></p></body>', '//body/p', 2],
			'XML failed' => ['Expected to find 2 elements matching the query "//body/p", actual: 1', 'xml', '<body><p></p></body>', '//body/p', 2],
		];
	}

	#[Test]
	#[DataProvider('dataAssertEmpty')]
	#[TestDox('assertEmpty()')]
	public function testAssertEmpty(?string $exceptionMessage, string $format, string $content, string $query): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert): void {
			$assert->assertEmpty();
		});
	}

	public static function dataAssertEmpty(): array {
		return [
			'HTML passed' => [null, 'html', '<!DOCTYPE html><body></body>', 'body'],
			'HTML failed' => ['Expected to find 0 child elements for the query "body", actual: 1', 'html', '<!DOCTYPE html><body><p></p></body>', 'body'],
			'HTML failed when xpath not exists' => ['Expected to find at least one element matching the query "p"', 'html', '<!DOCTYPE html><body></body>', 'p'],
			'XML passed' => [null, 'xml', '<body></body>', '//body'],
			'XML failed' => ['Expected to find 0 child elements for the query "//body", actual: 1', 'xml', '<body><p></p></body>', '//body'],
			'XML failed when xpath not exists' => ['Expected to find at least one element matching the query "//p"', 'xml', '<body></body>', '//p'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotEmpty')]
	#[TestDox('assertNotEmpty()')]
	public function testAssertNotEmpty(?string $exceptionMessage, string $format, string $content, string $query): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert): void {
			$assert->assertNotEmpty();
		});
	}

	public static function dataAssertNotEmpty(): array {
		return [
			'HTML passed' => [null, 'html', '<!DOCTYPE html><body><p></p></body>', 'body'],
			'HTML failed' => ['Expected to find at least one child element matching the query "body"', 'html', '<!DOCTYPE html><body></body>', 'body'],
			'HTML failed when xpath not exists' => ['Expected to find at least one element matching the query "p"', 'html', '<!DOCTYPE html><body></body>', 'p'],
			'XML passed' => [null, 'xml', '<body><p></p></body>', '//body'],
			'XML failed' => ['Expected to find at least one child element matching the query "//body"', 'xml', '<body></body>', '//body'],
			'XML failed when xpath not exists' => ['Expected to find at least one element matching the query "//p"', 'xml', '<body></body>', '//p'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertExists')]
	#[TestDox('assertExists()')]
	public function testAssertExists(?string $exceptionMessage, string $format, string $content, string $query): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert): void {
			$assert->assertExists();
		});
	}

	public static function dataAssertExists(): array {
		return [
			'HTML passed' => [null, 'html', '<!DOCTYPE html><body><p></p></body>', 'body p'],
			'HTML failed' => ['Expected to find at least one element matching the query "body p"', 'html', '<!DOCTYPE html><body></body>', 'body p'],
			'XML passed' => [null, 'xml', '<body><p></p><p></p></body>', '//body/p'],
			'XML failed' => ['Expected to find at least one element matching the query "//body/p"', 'xml', '<body></body>', '//body/p'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotExists')]
	#[TestDox('assertNotExists()')]
	public function testAssertNotExists(?string $exceptionMessage, string $format, string $content, string $query): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert): void {
			$assert->assertNotExists();
		});
	}

	public static function dataAssertNotExists(): array {
		return [
			'HTML passed' => [null, 'html', '<!DOCTYPE html><body></body>', 'body p'],
			'HTML failed' => ['Expected to find 0 elements matching the query "body p", actual: 1', 'html', '<!DOCTYPE html><body><p></p></body>', 'body p'],
			'XML passed' => [null, 'xml', '<body></body>', '//body/p'],
			'XML failed' => ['Expected to find 0 elements matching the query "//body/p", actual: 2', 'xml', '<body><p></p><p></p></body>', '//body/p'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertTextEquals')]
	#[TestDox('assertTextEquals()')]
	public function testAssertTextEquals(?string $exceptionMessage, string $format, string $content, string $query, string $text): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert) use ($text): void {
			$assert->assertTextEquals($text);
		});
	}

	public static function dataAssertTextEquals(): array {
		return [
			'HTML with empty string and single empty element' => [null, 'html', '<!DOCTYPE html><body><p></p></body>', 'body > p', ''],
			'HTML with empty string and multiple elements and one empty element' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p></p><p>second</p></body>', 'body > p', ''],
			'HTML with empty string and multiple elements and no empty elements' => ['Expected to find at least one element matching the query "body > p" with the text ""', 'html', '<!DOCTYPE html><body><p>first</p><p>second</p></body>', 'body > p', ''],
			'HTML with string and single element' => [null, 'html', '<!DOCTYPE html><body><p>second</p></body>', 'body > p', 'second'],
			'HTML with string and multiple elements and one matching element' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p>second</p><p>third</p></body>', 'body > p', 'second'],
			'HTML with string and multiple elements and no matching elements' => ['Expected to find at least one element matching the query "body > p" with the text "second"', 'html', '<!DOCTYPE html><body><p>first</p><p>third</p></body>', 'body > p', 'second'],
			'HTML with nested string and single element' => [null, 'html', '<!DOCTYPE html><body><p>sec<i>ond</i></p></body>', 'body > p', 'second'],
			'HTML with nested string and multiple elements and one matching element' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p><i><i>sec</i><i>ond</i></i></p><p>third</p></body>', 'body > p', 'second'],
			'HTML without elements' => ['Expected to find at least one element matching the query "body > p"', 'html', '<!DOCTYPE html><body></body>', 'body > p', 'second'],
			'HTML substring match' => ['Expected to find at least one element matching the query "body > p" with the text "second"', 'html', '<!DOCTYPE html><body><p>first second third</p></body>', 'body > p', 'second'],
			'XML with empty string and single empty element' => [null, 'xml', '<body><p></p></body>', '//body/p', ''],
			'XML with empty string and multiple elements and one empty element' => [null, 'xml', '<body><p>first</p><p></p><p>second</p></body>', '//body/p', ''],
			'XML with empty string and multiple elements and no empty elements' => ['Expected to find at least one element matching the query "//body/p" with the text ""', 'xml', '<body><p>first</p><p>second</p></body>', '//body/p', ''],
			'XML with string and single element' => [null, 'xml', '<body><p>second</p></body>', '//body/p', 'second'],
			'XML with string and multiple elements and one matching element' => [null, 'xml', '<body><p>first</p><p>second</p><p>third</p></body>', '//body/p', 'second'],
			'XML with string and multiple elements and no matching elements' => ['Expected to find at least one element matching the query "//body/p" with the text "second"', 'xml', '<body><p>first</p><p>third</p></body>', '//body/p', 'second'],
			'XML with nested string and single element' => [null, 'xml', '<body><p>sec<i>ond</i></p></body>', '//body/p', 'second'],
			'XML with nested string and multiple elements and one matching element' => [null, 'xml', '<body><p>first</p><p><i><i>sec</i><i>ond</i></i></p><p>third</p></body>', '//body/p', 'second'],
			'XML without elements' => ['Expected to find at least one element matching the query "//body/p"', 'xml', '<body></body>', '//body/p', 'second'],
			'XML substring match' => ['Expected to find at least one element matching the query "//body/p" with the text "second"', 'xml', '<body><p>first second third</p></body>', '//body/p', 'second'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertTextNotEquals')]
	#[TestDox('assertTextNotEquals()')]
	public function testAssertTextNotEquals(?string $exceptionMessage, string $format, string $content, string $query, string $text): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert) use ($text): void {
			$assert->assertTextNotEquals($text);
		});
	}

	public static function dataAssertTextNotEquals(): array {
		return [
			'HTML with empty string and single empty element' => ['Expected to find no elements matching the query "body > p" with the text ""', 'html', '<!DOCTYPE html><body><p></p></body>', 'body > p', ''],
			'HTML with empty string and multiple elements and one empty element' => ['Expected to find no elements matching the query "body > p" with the text ""', 'html', '<!DOCTYPE html><body><p>first</p><p></p><p>second</p></body>', 'body > p', ''],
			'HTML with empty string and multiple elements and no empty elements' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p>second</p></body>', 'body > p', ''],
			'HTML with string and single element' => ['Expected to find no elements matching the query "body > p" with the text "second"', 'html', '<!DOCTYPE html><body><p>second</p></body>', 'body > p', 'second'],
			'HTML with string and multiple elements and one matching element' => ['Expected to find no elements matching the query "body > p" with the text "second"', 'html', '<!DOCTYPE html><body><p>first</p><p>second</p><p>third</p></body>', 'body > p', 'second'],
			'HTML with string and multiple elements and no matching elements' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p>third</p></body>', 'body > p', 'second'],
			'HTML with nested string and single element' => ['Expected to find no elements matching the query "body > p" with the text "second"', 'html', '<!DOCTYPE html><body><p>sec<i>ond</i></p></body>', 'body > p', 'second'],
			'HTML with nested string and multiple elements and one matching element' => ['Expected to find no elements matching the query "body > p" with the text "second"', 'html', '<!DOCTYPE html><body><p>first</p><p><i><i>sec</i><i>ond</i></i></p><p>third</p></body>', 'body > p', 'second'],
			'HTML without elements' => ['Expected to find at least one element matching the query "body > p"', 'html', '<!DOCTYPE html><body></body>', 'body > p', 'second'],
			'HTML substring match' => [null, 'html', '<!DOCTYPE html><body><p>first second third</p></body>', 'body > p', 'second'],
			'XML with empty string and single empty element' => ['Expected to find no elements matching the query "//body/p" with the text ""', 'xml', '<body><p></p></body>', '//body/p', ''],
			'XML with empty string and multiple elements and one empty element' => ['Expected to find no elements matching the query "//body/p" with the text ""', 'xml', '<body><p>first</p><p></p><p>second</p></body>', '//body/p', ''],
			'XML with empty string and multiple elements and no empty elements' => [null, 'xml', '<body><p>first</p><p>second</p></body>', '//body/p', ''],
			'XML with string and single element' => ['Expected to find no elements matching the query "//body/p" with the text "second"', 'xml', '<body><p>second</p></body>', '//body/p', 'second'],
			'XML with string and multiple elements and one matching element' => ['Expected to find no elements matching the query "//body/p" with the text "second"', 'xml', '<body><p>first</p><p>second</p><p>third</p></body>', '//body/p', 'second'],
			'XML with string and multiple elements and no matching elements' => [null, 'xml', '<body><p>first</p><p>third</p></body>', '//body/p', 'second'],
			'XML with nested string and single element' => ['Expected to find no elements matching the query "//body/p" with the text "second"', 'xml', '<body><p>sec<i>ond</i></p></body>', '//body/p', 'second'],
			'XML with nested string and multiple elements and one matching element' => ['Expected to find no elements matching the query "//body/p" with the text "second"', 'xml', '<body><p>first</p><p><i><i>sec</i><i>ond</i></i></p><p>third</p></body>', '//body/p', 'second'],
			'XML without elements' => ['Expected to find at least one element matching the query "//body/p"', 'xml', '<body></body>', '//body/p', 'second'],
			'XML substring match' => [null, 'xml', '<body><p>first second third</p></body>', '//body/p', 'second'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertTextContains')]
	#[TestDox('assertTextContains()')]
	public function testAssertTextContains(?string $exceptionMessage, string $format, string $content, string $query, string $text): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert) use ($text): void {
			$assert->assertTextContains($text);
		});
	}

	public static function dataAssertTextContains(): array {
		return [
			'HTML passed with empty string' => [null, 'html', '<!DOCTYPE html><body><p></p></body>', 'body > p', ''],
			'HTML passed with string' => [null, 'html', '<!DOCTYPE html><body><p>string</p></body>', 'body > p', 'str'],
			'HTML passed with string and nested elements' => [null, 'html', '<!DOCTYPE html><body><p><i>str</i><i>ing</i></p></body>', 'body > p', 'trin'],
			'HTML failed when no elements' => ['Expected to find at least one element matching the query "body > p"', 'html', '<!DOCTYPE html><body></body>', 'body > p', ''],
			'HTML failed when no match' => ['Expected to find at least one element matching the query "body > p" containing the text "strings"', 'html', '<!DOCTYPE html><body><p>string</p></body>', 'body > p', 'strings'],
			'XML passed with empty string' => [null, 'xml', '<body><p></p></body>', '//body//p', ''],
			'XML passed with string' => [null, 'xml', '<body><p>string</p></body>', '//body//p', 'str'],
			'XML passed with string and nested elements' => [null, 'xml', '<body><p><i>str</i><i>ing</i></p></body>', '//body//p', 'trin'],
			'XML failed when no elements' => ['Expected to find at least one element matching the query "//body//p"', 'xml', '<body></body>', '//body//p', ''],
			'XML failed when no match' => ['Expected to find at least one element matching the query "//body//p" containing the text "strings"', 'xml', '<body><p>string</p></body>', '//body//p', 'strings'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertTextNotContains')]
	#[TestDox('assertTextNotContains()')]
	public function testAssertTextNotContains(?string $exceptionMessage, string $format, string $content, string $query, string $text): void {
		$this->assert($exceptionMessage, $format, $content, $query, static function (NodeListAssert $assert) use ($text): void {
			$assert->assertTextNotContains($text);
		});
	}

	public static function dataAssertTextNotContains(): array {
		return [
			'HTML passed when no match' => [null, 'html', '<!DOCTYPE html><body><p>string</p></body>', 'body > p', 'STRING'],
			'HTML failed when no elements' => ['Expected to find at least one element matching the query "body > p"', 'html', '<!DOCTYPE html><body></body>', 'body > p', ''],
			'HTML failed when there is match' => ['Expected to find no elements matching the query "body > p" containing the text "string"', 'html', '<!DOCTYPE html><body><p>string</p></body>', 'body > p', 'string'],
			'HTML failed when string is empty' => ['Expected to find no elements matching the query "body > p" containing the text ""', 'html', '<!DOCTYPE html><body><p>string</p></body>', 'body > p', ''],
			'XML passed when no match' => [null, 'xml', '<body><p>string</p></body>', '//body/p', 'STRING'],
			'XML failed when no elements' => ['Expected to find at least one element matching the query "//body/p"', 'xml', '<body></body>', '//body/p', ''],
			'XML failed when there is match' => ['Expected to find no elements matching the query "//body/p" containing the text "string"', 'xml', '<body><p>string</p></body>', '//body/p', 'string'],
			'XML failed when string is empty' => ['Expected to find no elements matching the query "//body/p" containing the text ""', 'xml', '<body><p>string</p></body>', '//body/p', ''],
		];
	}

	#[Test]
	#[DataProvider('dataAssertMatchesRegex')]
	#[TestDox('assertMatchesRegex()')]
	public function testAssertMatchesRegex(?string $exceptionMessage, string $content, string $query, string $regex): void {
		$this->assert($exceptionMessage, 'html', $content, $query, static function (NodeListAssert $assert) use ($regex): void {
			$assert->assertMatchesRegex($regex);
		});
	}

	public static function dataAssertMatchesRegex(): array {
		return [
			'passed when all elements match' => [null, '<!DOCTYPE html><body><p>123</p><p>456</p></body>', 'body > p', '/^\\d+$/'],
			'failed when one element not matches' => ['Expected all elements at the query "body > p" to match the regular expression "/^\\d+$/"', '<!DOCTYPE html><body><p>123</p><p>abc</p></body>', 'body > p', '/^\\d+$/'],
			'failed when no elements' => ['Expected to find at least one element matching the query "body > p"', '<!DOCTYPE html><body></body>', 'body > p', '/^\\d+$/'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotMatchesRegex')]
	#[TestDox('assertNotMatchesRegex()')]
	public function testAssertNotMatchesRegex(?string $exceptionMessage, string $content, string $query, string $regex): void {
		$this->assert($exceptionMessage, 'html', $content, $query, static function (NodeListAssert $assert) use ($regex): void {
			$assert->assertNotMatchesRegex($regex);
		});
	}

	public static function dataAssertNotMatchesRegex(): array {
		return [
			'failed when all elements match' => ['Expected all elements at the query "body > p" not to match the regular expression "/^\\d+$/"', '<!DOCTYPE html><body><p>123</p><p>456</p></body>', 'body > p', '/^\\d+$/'],
			'passed when one element not matches' => [null, '<!DOCTYPE html><body><p>123</p><p>abc</p></body>', 'body > p', '/^\\+$/'],
			'failed when no elements' => ['Expected to find at least one element matching the query "body > p"', '<!DOCTYPE html><body></body>', 'body > p', '/^\\d+$/'],
		];
	}

	private function assert(?string $exceptionMessage, string $format, string $content, string $query, callable $f): void {
		if ($exceptionMessage) {
			$this->expectException(AssertionFailedError::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		switch ($format) {
			case 'html':
				$f($this->html($content)->query($query));
				break;
			case 'xml':
				$f($this->xml($content)->xpath($query));
				break;
		}
	}
}
