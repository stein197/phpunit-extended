<?php
namespace Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Stein197\PHPUnit\ResponseAssert;
use Stein197\PHPUnit\TestCase;

final class ResponseAssertTest extends PHPUnitTestCase {

	use TestCase;

	#[Test]
	#[DataProvider('dataAssertContentContains')]
	#[TestDox('assertContentContains()')]
	public function testAssertContentContains(?string $exceptionMessage, ResponseInterface $response, string $expectedContent): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedContent): void {
			$response->assertContentContains($expectedContent);
		});
	}

	public static function dataAssertContentContains(): array {
		return [
			'passed' => [null, new Response(200, [], 'Hello, World!'), 'Hello'],
			'failed' => ['Expected the response to contain the content "Hello", actual: ""', new Response(200), 'Hello'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertContentNotContains')]
	#[TestDox('assertContentNotContains()')]
	public function testAssertContentNotContains(?string $exceptionMessage, ResponseInterface $response, string $expectedContent): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedContent): void {
			$response->assertContentNotContains($expectedContent);
		});
	}

	public static function dataAssertContentNotContains(): array {
		return [
			'passed' => [null, new Response(200, [], ''), 'Hello'],
			'failed' => ['Expected the response not to contain the content "Hello", actual: "Hello, World!"', new Response(200, [], 'Hello, World!'), 'Hello'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertContentEquals')]
	#[TestDox('assertContentEquals()')]
	public function testAssertContentEquals(?string $exceptionMessage, ResponseInterface $response, string $expectedContent): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedContent): void {
			$response->assertContentEquals($expectedContent);
		});
	}

	public static function dataAssertContentEquals(): array {
		return [
			'passed' => [null, new Response(200, [], 'Hello, World!'), 'Hello, World!'],
			'failed' => ['Expected the response to have the content "Hello, World!", actual: ""', new Response(200), 'Hello, World!'],
		];
	}


	#[Test]
	#[DataProvider('dataAssertContentNotEquals')]
	#[TestDox('assertContentNotEquals()')]
	public function testAssertContentNotEquals(?string $exceptionMessage, ResponseInterface $response, string $expectedContent): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedContent): void {
			$response->assertContentNotEquals($expectedContent);
		});
	}

	public static function dataAssertContentNotEquals(): array {
		return [
			'passed' => [null, new Response(200, []), 'Hello, World!'],
			'failed' => ['Expected the response not to have the content "Hello, World!"', new Response(200, [], 'Hello, World!'), 'Hello, World!'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertContentRegex')]
	#[TestDox('assertContentRegex()')]
	public function testAssertContentRegex(?string $exceptionMessage, ResponseInterface $response, string $regex): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($regex): void {
			$response->assertContentRegex($regex);
		});
	}

	public static function dataAssertContentRegex(): array {
		return [
			'passed' => [null, new Response(200, [], 'Hello, World!'), '/hello/i'],
			'failed' => ['Expected the response to match the regular expression "/hello/i", actual: ""', new Response(200), '/hello/i'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertContentNotRegex')]
	#[TestDox('assertContentNotRegex()')]
	public function testAssertContentNotRegex(?string $exceptionMessage, ResponseInterface $response, string $regex): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($regex): void {
			$response->assertContentNotRegex($regex);
		});
	}

	public static function dataAssertContentNotRegex(): array {
		return [
			'passed' => [null, new Response(200), '/hello/i'],
			'failed' => ['Expected the response not to match the regular expression "/hello/i", actual: "Hello, World!"', new Response(200, [], 'Hello, World!'), '/hello/i'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertContentType')]
	#[TestDox('assertContentType()')]
	public function testAssertContentType(?string $exceptionMessage, ResponseInterface $response, string $expectedContentType): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedContentType): void {
			$response->assertContentType($expectedContentType);
		});
	}

	public static function dataAssertContentType(): array {
		return [
			'passed' => [null, new Response(200, ['Content-Type' => 'text/html']), 'text/html'],
			'failed' => ['Expected the response to have the header "Content-Type" with value "text/html"', new Response(200), 'text/html'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertCookieEquals')]
	#[TestDox('assertCookieEquals()')]
	public function testAssertCookieEquals(?string $exceptionMessage, ResponseInterface $response, string $expectedName, string $expectedValue): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedName, $expectedValue): void {
			$response->assertCookieEquals($expectedName, $expectedValue);
		});
	}

	public static function dataAssertCookieEquals(): array {
		return [
			'passed' => [null, new Response(200, ['Set-Cookie' => ['key=value']]), 'key', 'value'],
			'cookie not exists' => ['Expected the response to have the cookie "key" with the value "value"', new Response(200), 'key', 'value'],
			'value not matches' => ['Expected the response to have the cookie "key" with the value "value"', new Response(200, ['Set-Cookie' => ['key=value2']]), 'key', 'value'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertCookieNotEquals')]
	#[TestDox('assertCookieNotEquals()')]
	public function testAssertCookieNotEquals(?string $exceptionMessage, ResponseInterface $response, string $expectedName, string $expectedValue): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedName, $expectedValue): void {
			$response->assertCookieNotEquals($expectedName, $expectedValue);
		});
	}

	public static function dataAssertCookieNotEquals(): array {
		return [
			'cookie not exists' => [null, new Response(200), 'key', 'value'],
			'value not matches' => [null, new Response(200, ['Set-Cookie' => ['key=value2']]), 'key', 'value'],
			'failed' => ['Expected the response not to have the cookie "key" with the value "value"', new Response(200, ['Set-Cookie' => ['key=value']]), 'key', 'value'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertCookieExists')]
	#[TestDox('assertCookieExists()')]
	public function testAssertCookieExists(?string $exceptionMessage, ResponseInterface $response, string $expectedName): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedName): void {
			$response->assertCookieExists($expectedName);
		});
	}

	public static function dataAssertCookieExists(): array {
		return [
			'passed' => [null, new Response(200, ['Set-Cookie' => ['key=value']]), 'key'],
			'failed' => ['Expected the response to have the cookie "key"', new Response(200), 'key'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertCookieNotExists')]
	#[TestDox('assertCookieNotExists()')]
	public function testAssertCookieNotExists(?string $exceptionMessage, ResponseInterface $response, string $expectedName): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedName): void {
			$response->assertCookieNotExists($expectedName);
		});
	}

	public static function dataAssertCookieNotExists(): array {
		return [
			'passed' => [null, new Response(200), 'key'],
			'failed' => ['Expected the response not to have the cookie "key"', new Response(200, ['Set-Cookie' => ['key=value']]), 'key'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertDownload')]
	#[TestDox('assertDownload()')]
	public function testAssertDownload(?string $exceptionMessage, ResponseInterface $response, ?string $expectedName): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedName): void {
			$response->assertDownload($expectedName);
		});
	}

	public static function dataAssertDownload(): array {
		return [
			'passed without name' => [null, new Response(200, ['Content-Disposition' => 'attachment']), null],
			'passed with name' => [null, new Response(200, ['Content-Disposition' => 'attachment; name="file"; filename="file.txt"']), 'file.txt'],
			'failed without header' => ['Expected the response to have the header "Content-Disposition"', new Response(200), null],
			'failed with incorrect type' => ['Expected the response to have the Content-Disposition header to be attachment, actual: inline', new Response(200, ['Content-Disposition' => 'inline']), null],
			'failed with incorrect name' => ['Expected the response to download a file "file", actual: "file.txt"', new Response(200, ['Content-Disposition' => 'attachment; filename="file.txt"']), 'file'],
			'failed without name' => ['Expected the response to download a file "file"', new Response(200, ['Content-Disposition' => 'attachment; ']), 'file'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertHeaderEquals')]
	#[TestDox('assertHeaderEquals()')]
	public function testAssertHeaderEquals(?string $exceptionMessage, ResponseInterface $response, string $expectedHeader, string $expectedValue): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedHeader, $expectedValue): void {
			$response->assertHeaderEquals($expectedHeader, $expectedValue);
		});
	}

	public static function dataAssertHeaderEquals(): array {
		return [
			'case-sensetive' => [null, new Response(200, ['Content-Type' => ['text/html']]), 'Content-Type', 'text/html'],
			'case-insensetive' => [null, new Response(200, ['Content-Type' => 'text/html']), 'content-type', 'text/html'],
			'failed' => ['Expected the response to have the header "Content-Type"', new Response(200, ['Content-Type' => 'text/html']), 'Content-Type', 'text/plain'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertHeaderNotEquals')]
	#[TestDox('assertHeaderNotEquals()')]
	public function testAssertHeaderNotEquals(?string $exceptionMessage, ResponseInterface $response, string $expectedHeader, string $expectedValue): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedHeader, $expectedValue): void {
			$response->assertHeaderNotEquals($expectedHeader, $expectedValue);
		});
	}

	public static function dataAssertHeaderNotEquals(): array {
		return [
			'passed' => [null, new Response(200, ['Content-Type' => ['text/html']]), 'Content-Type', 'text/plain'],
			'failed case-sensetive' => ['Expected the response not to have the header "Content-Type" with value "text/html"', new Response(200, ['Content-Type' => 'text/html']), 'Content-Type', 'text/html'],
			'failed case-insensetive' => ['Expected the response not to have the header "content-type" with value "text/html"', new Response(200, ['Content-Type' => ['text/html']]), 'content-type', 'text/html'],
		];
	}


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
	#[DataProvider('dataAssertRedirect')]
	#[TestDox('assertRedirect()')]
	public function testAssertRedirect(?string $exceptionMessage, ResponseInterface $response, string $expectedUrl): void {
		$this->assert($exceptionMessage, $response, static function (ResponseAssert $response) use ($expectedUrl): void {
			$response->assertRedirect($expectedUrl);
		});
	}

	public static function dataAssertRedirect(): array {
		return [
			'status is 201' => [null, new Response(201, ['Location' => '/url']), '/url'],
			'status is 3xx' => [null, new Response(302, ['Location' => '/url']), '/url'],
			'no location' => ['Expected the response to have the header "Location" with value "/url"', new Response(302), '/url'],
			'status is not 201 or 3xx' => ['Expected the response to have the status 201 or 3xx, actual: 200', new Response(200, ['Location' => '/url']), '/url'],
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
			$this->expectException(AssertionFailedError::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		$f($this->response($response));
	}
}
