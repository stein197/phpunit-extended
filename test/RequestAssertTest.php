<?php
namespace Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\TestCase;

final class RequestAssertTest extends PHPUnitTestCase {

	use TestCase;

	#[Test]
	#[DataProvider('data_assertStatus')]
	#[TestDox('assertStatus()')]
	public function test_assertStatus(?string $exceptionMessage, ResponseInterface $response, int $expectedStatus): void {
		if ($exceptionMessage) {
			$this->expectException(ExpectationFailedException::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		$this->response($response)->assertStatus($expectedStatus);
	}

	public static function data_assertStatus(): array {
		return [
			'passed' => [null, new Response(200), 200],
			'failed' => ['Expected the response to have the status 200, actual: 500', new Response(500), 200],
		];
	}
}
