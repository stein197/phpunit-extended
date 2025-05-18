![](https://img.shields.io/packagist/v/stein197/phpunit-extended)
![](https://img.shields.io/github/actions/workflow/status/stein197/phpunit-extended/test.yml)

# PHPUnit Extended
This package is a small extension to PHPUnit that extends its assertions. The library provides assertions for PSR-7 HTTP responses, XML/HTML documents and JSON structures. The extension uses the following libraries to make it work:
- [DOM](https://www.php.net/manual/en/book.dom.php): the new native PHP 8.4 DOM library to make assertions against XML/HTML DOM structures
- [PSR-7](https://www.php-fig.org/psr/psr-7/): HTTP message interfaces to make assertions against HTTP server response objects
- [JSONPath](https://github.com/Galbar/JsonPath-PHP): JSONPath library to make assertions against JSON structures

## Installation
```bash
$ composer require --dev stein197/phpunit-extended
```

## Usage
Let's say you test a an HTTP response:
```php
namespace Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stein197\PHPUnit\ExtendedTestCaseInterface;
use Stein197\PHPUnit\TestCase as TestCaseTrait; // Trait that extends the basic PHPUnit assertion functionality and implements the ExtendedTestCaseInterface interface

// Your test class
final class ResponseTest extends TestCase implements ExtendedTestCaseInterface {

	use TestCaseTrait; // Include the extended assertions

	#[Test]
	public function testResponse(): void {
		// HTTP response assertions
		$response = $this->request('/home'); // Anything that returns a PSR-7 response object
		$response = $this->response($response); // Wrap the PSR-7 response in an assertion object
		$response->assertOk(); // Assert the status code is 200
		$response->assertHeaderEquals('Content-Type', 'text/html'); // Assert that there is a header 'Content-Type' with the value 'text/html'
		// ...

		// XML/HTML DOM assertions
		$document = $response->document(); // Return a DocumentAssert assertion object containing the response body
		$document->query('#main h1')->assertTextEquals('Hello, World!'); // Query elements by query selector an assert
		$document->xpath('//*[@id = "main"]//h1')->assertTextEquals('Hello, World!'); // The the same but using XPath
		// ...

		// JSON assertions
		$json = $response->json(); // Return a JsonAssert containing the response body
		$json->assertExists('$.user'); // Assert that there is a given JSONPath
		// ...

		$this->json('{...}'); // Wrap a JSON string
		$this->html('<!DOCTYPE html>...'); // Wrap an HTML string
		$this->xml('<?xml version="1.0" ?>...'); // Wrap an XML string
	}
}
```

> **IMPORTANT!**
>
> The extension uses instances of the `PHPUnit\Framework\TestCase` class. The methods cannot be called statically like the native PHPUnit's methods:
> ```php
> self::assertOk(); // Won't work
> ```

## Testing
Run `make test`.

## Documentation
The main methods of the trait are:
- `response(ResponseInterface $response): ResponseAssert`: Wraps an HTTP response object in an assertion one
- `json(string $json): JsonAssert`: Wraps a JSON string in an assertion object
- `html(string $html): DocumentAssert`: Wraps an HTML string in an assertion object
- `xml(string $xml): DocumentAssert`: Wraps an XML string in an assertion object
- `pass(): void`: Marks test as passed

The whole documentation to every assertion method can be found in the phpdoc comments in the source code.
