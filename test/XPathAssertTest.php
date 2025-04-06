<?php
namespace Test;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Stein197\PHPUnit\TestCase;
use Stein197\PHPUnit\XPathAssert;

final class XPathAssertTest extends PHPUnitTestCase {

	use TestCase;

	#[Test]
	#[DataProvider('dataAssertCount')]
	#[TestDox('assertCount()')]
	public function testAssertCount(?string $exceptionMessage, string $format, string $content, string $xpath, int $expectedCount): void {
		$this->assert($exceptionMessage, $format, $content, static function (XPathAssert $assert) use ($xpath, $expectedCount): void {
			$assert->assertCount($xpath, $expectedCount);
		});
	}

	public static function dataAssertCount(): array {
		return [
			'HTML passed' => [null, 'html', '<!DOCTYPE html><body><p></p><p></p></body>', '//body/p', 2],
			'HTML failed' => ['Expected to find 2 elements matching the xpath "//body/p", actual: 1', 'html', '<!DOCTYPE html><body><p></p></body>', '//body/p', 2],
			'XML passed' => [null, 'xml', '<body><p></p><p></p></body>', '//body/p', 2],
			'XML failed' => ['Expected to find 2 elements matching the xpath "//body/p", actual: 1', 'xml', '<body><p></p></body>', '//body/p', 2],
		];
	}

	private function assert(?string $exceptionMessage, string $format, string $content, callable $f): void {
		if ($exceptionMessage) {
			$this->expectException(AssertionFailedError::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		switch ($format) {
			case 'html':
				$f($this->xpathHtml($content));
				break;
			case 'xml':
				$f($this->xpathXml($content));
				break;
		}
	}
}
