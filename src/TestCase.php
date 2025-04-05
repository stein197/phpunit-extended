<?php
namespace Stein197\PHPUnit;

use Psr\Http\Message\ResponseInterface;

/**
 * Extended PHPUnit assertions.
 */
trait TestCase {

	/**
	 * Return an assertion object to test response objects.
	 * @param ResponseInterface $response PSR-7 response object.
	 * @return RequestAssert Assertion object.
	 * ```php
	 * $this->request(new Response(...))->assertStatus(200);
	 * ```
	 */
	public function response(ResponseInterface $response): RequestAssert {
		return new RequestAssert($this, $response);
	}
}
