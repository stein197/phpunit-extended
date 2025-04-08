<?php
namespace Test\Assert;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Stein197\PHPUnit\Assert\NodeListAssert;
use Stein197\PHPUnit\TestCase;

// TODO
final class NodeListAssertTest extends PHPUnitTestCase {

	use TestCase;

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
			'HTML with empty string and multiple elements and no empty elements' => ['Expected to find at least one element matching the query "body > p" and containing the text ""', 'html', '<!DOCTYPE html><body><p>first</p><p>second</p></body>', 'body > p', ''],
			'HTML with string and single element' => [null, 'html', '<!DOCTYPE html><body><p>second</p></body>', 'body > p', 'second'],
			'HTML with string and multiple elements and one matching element' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p>second</p><p>third</p></body>', 'body > p', 'second'],
			'HTML with string and multiple elements and no matching elements' => ['Expected to find at least one element matching the query "body > p" and containing the text "second"', 'html', '<!DOCTYPE html><body><p>first</p><p>third</p></body>', 'body > p', 'second'],
			'HTML with nested string and single element' => [null, 'html', '<!DOCTYPE html><body><p>sec<i>ond</i></p></body>', 'body > p', 'second'],
			'HTML with nested string and multiple elements and one matching element' => [null, 'html', '<!DOCTYPE html><body><p>first</p><p><i><i>sec</i><i>ond</i></i></p><p>third</p></body>', 'body > p', 'second'],
			'HTML without elements' => ['Expected to find at least one element matching the query "body > p" and containing the text "second"', 'html', '<!DOCTYPE html><body></body>', 'body > p', 'second'],
			'HTML substring match' => ['Expected to find at least one element matching the query "body > p" and containing the text "second"', 'html', '<!DOCTYPE html><body><p>first second third</p></body>', 'body > p', 'second'],
			'XML with empty string and single empty element' => [null, 'xml', '<body><p></p></body>', '//body/p', ''],
			'XML with empty string and multiple elements and one empty element' => [null, 'xml', '<body><p>first</p><p></p><p>second</p></body>', '//body/p', ''],
			'XML with empty string and multiple elements and no empty elements' => ['Expected to find at least one element matching the query "//body/p" and containing the text ""', 'xml', '<body><p>first</p><p>second</p></body>', '//body/p', ''],
			'XML with string and single element' => [null, 'xml', '<body><p>second</p></body>', '//body/p', 'second'],
			'XML with string and multiple elements and one matching element' => [null, 'xml', '<body><p>first</p><p>second</p><p>third</p></body>', '//body/p', 'second'],
			'XML with string and multiple elements and no matching elements' => ['Expected to find at least one element matching the query "//body/p" and containing the text "second"', 'xml', '<body><p>first</p><p>third</p></body>', '//body/p', 'second'],
			'XML with nested string and single element' => [null, 'xml', '<body><p>sec<i>ond</i></p></body>', '//body/p', 'second'],
			'XML with nested string and multiple elements and one matching element' => [null, 'xml', '<body><p>first</p><p><i><i>sec</i><i>ond</i></i></p><p>third</p></body>', '//body/p', 'second'],
			'XML without elements' => ['Expected to find at least one element matching the query "//body/p" and containing the text "second"', 'xml', '<body></body>', '//body/p', 'second'],
			'XML substring match' => ['Expected to find at least one element matching the query "//body/p" and containing the text "second"', 'xml', '<body><p>first second third</p></body>', '//body/p', 'second'],
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
