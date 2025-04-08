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
