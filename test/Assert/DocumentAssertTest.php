<?php
namespace Test\Assert;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Stein197\PHPUnit\ExtendedTestCase;
use Stein197\PHPUnit\TestCase;

// TODO: Test namespaced XML
final class DocumentAssertTest extends PHPUnitTestCase implements ExtendedTestCase {

	use TestCase;

	#[Test]
	#[DataProvider('dataAssertAnchorExists')]
	#[TestDox('assertAnchorExists()')]
	public function testAssertAnchorExists(?string $exceptionMessage, string $content, string $expectedUrl, array $expectedQuery, ?string $expectedHash): void {
		if ($exceptionMessage) {
			$this->expectException(AssertionFailedError::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		$this->html('<!DOCTYPE html>' . $content)->assertAnchorExists($expectedUrl, $expectedQuery, $expectedHash);
	}

	public static function dataAssertAnchorExists(): array {
		return [
			'passed when all arguments are empty' => [null, '<body><a href=""></a></body>', '', [], null],
			'failed when all arguments are empty' => ['Expected to find at least one <a> with href ""', '<body><a href="/"></a></body>', '', [], null],
			'passed when only $path exists and matches' => [null, '<body><a href="/url"></a></body>', '/url', [], null],
			'failed when only $path exists and mismatches' => ['Expected to find at least one <a> with href "/url-2"', '<body><a href="/url-1"></a></body>', '/url-2', [], null],
			'passed when only $query exists and matches exactly and order matches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => 1, 'b' => 2, 'c' => 3], null],
			'passed when only $query exists and matches exactly and order mismatches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['c' => 3, 'b' => 2, 'a' => 1], null],
			'passed when only $query exists and matches partially and order matches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => 1, 'b' => 2], null],
			'passed when only $query exists and matches partially and order mismatches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['b' => 2, 'a' => 1], null],
			'passed when only $query exists and matches deep exactly and order matches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['a' => 1, 'b' => 2, 'c' => 3]], null],
			'passed when only $query exists and matches deep exactly and order mismatches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['c' => 3, 'b' => 2, 'a' => 1]], null],
			'passed when only $query exists and matches deep partially and order matches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['a' => 1, 'b' => 2]], null],
			'passed when only $query exists and matches deep partially and order mismatches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['b' => 2, 'a' => 1]], null],
			'passed when only $query exists and matches exactly and order matches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['a' => '?', 'b' => '=', 'c' => '&'], null],
			'passed when only $query exists and matches exactly and order mismatches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['c' => '&', 'b' => '=', 'a' => '?'], null],
			'passed when only $query exists and matches partially and order matches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['a' => '?', 'b' => '='], null],
			'passed when only $query exists and matches partially and order mismatches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['b' => '=', 'a' => '?'], null],
			'failed when only $query exists and mismatches' => ['Expected to find at least one <a> with href "?a=10"', '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => 10], null],
			'passed when only $hash exists and empty' => [null, '<body><a href="#"></a></body>', '', [], ''],
			'passed when only $hash exists and not empty' => [null, '<body><a href="#hash"></a></body>', '', [], 'hash'],
			'failed when only $hash exists and mismatches' => ['Expected to find at least one <a> with href "#hash-1"', '<body><a href="#hash-2"></a></body>', '', [], 'hash-1'],
			'passed when $path and $query exist' => [null, '<body><a href="/url?a=1"></a></body>', '/url', ['a' => 1], null],
			'passed when $path and $hash exist' => [null, '<body><a href="/url#hash"></a></body>', '/url', [], 'hash'],
			'passed when $query and $hash exist' => [null, '<body><a href="?a=1#hash"></a></body>', '', ['a' => 1], 'hash'],
			'passed when all arguments match' => [null, '<body><a href="/url?a[b]=2&a[c]=3#hash"></a></body>', '/url', ['a' => ['b' => 2, 'c' => 3]], 'hash'],
		];
	}
}
