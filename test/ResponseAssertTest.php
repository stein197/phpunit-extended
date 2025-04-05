<?php
namespace Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\ResponseAssert;
use Stein197\PHPUnit\TestCase;

final class ResponseAssertTest extends PHPUnitTestCase {

	use TestCase;

	#[Test]
	#[DataProvider('dataAssertHeaderExists')]
	#[TestDox('assertHeaderExists()')]
	public function testAssertHeaderExists(?string $exceptionMessage, ResponseInterface $response, string $expectedHeader): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedHeader): void {
			$response->assertHeaderExists($expectedHeader);
		});
	}

	public static function dataAssertHeaderExists(): array {
		return [
			'case-sensetive' => [null, new Response(200, ['Content-Type' => 'text/html']), 'Content-Type'],
			'case-insensetive' => [null, new Response(200, ['Content-Type' => 'text/html']), 'content-type'],
			'failed' => ['Expected the response to have the header "Content-Type"', new Response(200, []), 'Content-Type'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertHeaderNotExists')]
	#[TestDox('assertHeaderNotExists()')]
	public function testAssertHeaderNotExists(?string $exceptionMessage, ResponseInterface $response, string $expectedHeader): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedHeader): void {
			$response->assertHeaderNotExists($expectedHeader);
		});
	}

	public static function dataAssertHeaderNotExists(): array {
		return [
			'passed' => [null, new Response(200), 'Content-Type'],
			'failed case-sensetive' => ['Expected the response not to have the header "Content-Type"', new Response(200, ['Content-Type' => 'text/html']), 'Content-Type'],
			'failed case-insensetive' => ['Expected the response not to have the header "content-type"', new Response(200, ['Content-Type' => 'text/html']), 'content-type'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertNotFound')]
	#[TestDox('assertNotFound()')]
	public function testAssertNotFound(?string $exceptionMessage, ResponseInterface $response): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response): void {
			$response->assertNotFound();
		});
	}

	public static function dataAssertNotFound(): array {
		return [
			'passed' => [null, new Response(404)],
			'failed' => ['Expected the response to have the status 404, actual: 500', new Response(500)],
		];
	}

	#[Test]
	#[DataProvider('dataAssertOk')]
	#[TestDox('assertOk()')]
	public function testAssertOk(?string $exceptionMessage, ResponseInterface $response): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response): void {
			$response->assertOk();
		});
	}

	public static function dataAssertOk(): array {
		return [
			'passed' => [null, new Response(200)],
			'failed' => ['Expected the response to have the status 200, actual: 500', new Response(500)],
		];
	}

	#[Test]
	#[DataProvider('dataAssertStatus')]
	#[TestDox('assertStatus()')]
	public function testAssertStatus(?string $exceptionMessage, ResponseInterface $response, int $expectedStatus): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedStatus): void {
			$response->assertStatus($expectedStatus);
		});
	}

	public static function dataAssertStatus(): array {
		return [
			'passed' => [null, new Response(200), 200],
			'failed' => ['Expected the response to have the status 200, actual: 500', new Response(500), 200],
		];
	}

	private function assert(?string $exceptionMessage, ResponseInterface $response, callable $f): void {
		if ($exceptionMessage) {
			$this->expectException(ExpectationFailedException::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		$f($this->response($response));
	}
}
