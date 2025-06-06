<?php
namespace Test\Assert;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Stein197\PHPUnit\ExtendedTestCaseInterface;
use Stein197\PHPUnit\ExtendedTestCase;
use function file_get_contents;

final class DocumentAssertTest extends TestCase implements ExtendedTestCaseInterface {

	use ExtendedTestCase;

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
			'passed when only $query exists and matches exactly and order matches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => '1', 'b' => '2', 'c' => '3'], null],
			'passed when only $query exists and matches exactly and order mismatches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['c' => '3', 'b' => '2', 'a' => '1'], null],
			'passed when only $query exists and matches partially and order matches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => '1', 'b' => '2'], null],
			'passed when only $query exists and matches partially and order mismatches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['b' => '2', 'a' => '1'], null],
			'passed when only $query exists and matches deep exactly and order matches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['a' => '1', 'b' => '2', 'c' => '3']], null],
			'passed when only $query exists and matches deep exactly and order mismatches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['c' => '3', 'b' => '2', 'a' => '1']], null],
			'passed when only $query exists and matches deep partially and order matches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['a' => '1', 'b' => '2']], null],
			'passed when only $query exists and matches deep partially and order mismatches' => [null, '<body><a href="?a[a]=1&a[b]=2&a[c]=3"></a></body>', '', ['a' => ['b' => '2', 'a' => '1']], null],
			'passed when only $query exists and matches exactly and order matches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['a' => '?', 'b' => '=', 'c' => '&'], null],
			'passed when only $query exists and matches exactly and order mismatches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['c' => '&', 'b' => '=', 'a' => '?'], null],
			'passed when only $query exists and matches partially and order matches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['a' => '?', 'b' => '='], null],
			'passed when only $query exists and matches partially and order mismatches and urlencoded' => [null, '<body><a href="?a=%3F&b=%3D&c=%26"></a></body>', '', ['b' => '=', 'a' => '?'], null],
			'failed when only $query exists and mismatches' => ['Expected to find at least one <a> with href "?a=10"', '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => '10'], null],
			'passed when only $hash exists and empty' => [null, '<body><a href="#"></a></body>', '', [], ''],
			'passed when only $hash exists and not empty' => [null, '<body><a href="#hash"></a></body>', '', [], 'hash'],
			'failed when only $hash exists and mismatches' => ['Expected to find at least one <a> with href "#hash-1"', '<body><a href="#hash-2"></a></body>', '', [], 'hash-1'],
			'passed when $path and $query exist' => [null, '<body><a href="/url?a=1"></a></body>', '/url', ['a' => '1'], null],
			'passed when $path and $hash exist' => [null, '<body><a href="/url#hash"></a></body>', '/url', [], 'hash'],
			'passed when $query and $hash exist' => [null, '<body><a href="?a=1#hash"></a></body>', '', ['a' => '1'], 'hash'],
			'passed when all arguments match' => [null, '<body><a href="/url?a[b]=2&a[c]=3#hash"></a></body>', '/url', ['a' => ['b' => '2', 'c' => '3']], 'hash'],
			'failed when $path mismatches' => ['Expected to find at least one <a> with href "/url-0?a=1#hash"', '<body><a href="/url?a=1#hash"></a></body>', '/url-0', ['a' => '1'], 'hash'],
			'failed when $query mismatches' => ['Expected to find at least one <a> with href "/url?a=10#hash"', '<body><a href="/url?a=1#hash"></a></body>', '/url', ['a' => '10'], 'hash'],
			'failed when $hash mismatches' => ['Expected to find at least one <a> with href "/url?a=1#hash-0"', '<body><a href="/url?a=1#hash"></a></body>', '/url', ['a' => '1'], 'hash-0'],
		];
	}

	#[Test]
	#[DataProvider('dataAssertAnchorNotExists')]
	#[TestDox('assertAnchorNotExists()')]
	public function testAssertAnchorNotExists(?string $exceptionMessage, string $content, string $expectedUrl, array $expectedQuery, ?string $expectedHash): void {
		if ($exceptionMessage) {
			$this->expectException(AssertionFailedError::class);
			$this->expectExceptionMessage($exceptionMessage);
		}
		$this->html('<!DOCTYPE html>' . $content)->assertAnchorNotExists($expectedUrl, $expectedQuery, $expectedHash);
	}

	public static function dataAssertAnchorNotExists(): array {
		return [
			'failed when all arguments are empty' => ['Expected to find no <a> with href ""', '<body><a href=""></a></body>', '', [], null],
			'passed when all arguments are empty' => [null, '<body><a href="/"></a></body>', '', [], null],
			'failed when only $path exists and matches' => ['Expected to find no <a> with href "/url"', '<body><a href="/url"></a></body>', '/url', [], null],
			'passed when only $path exists and mismatches' => [null, '<body><a href="/url-1"></a></body>', '/url-2', [], null],
			'failed when only $query exists and matches exactly and order matches' => ['Expected to find no <a> with href "?a=1&b=2&c=3"', '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => '1', 'b' => '2', 'c' => '3'], null],
			'failed when only $query exists and matches exactly and order mismatches' => ['Expected to find no <a> with href "?c=3&b=2&a=1"', '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['c' => '3', 'b' => '2', 'a' => '1'], null],
			'failed when only $query exists and matches partially and order matches' => ['Expected to find no <a> with href "?a=1&b=2"', '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => '1', 'b' => '2'], null],
			'failed when only $query exists and matches partially and order mismatches' => ['Expected to find no <a> with href "?b=2&a=1"', '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['b' => '2', 'a' => '1'], null],
			'passed when only $query exists and mismatches' => [null, '<body><a href="?a=1&b=2&c=3"></a></body>', '', ['a' => '10'], null],
			'failed when only $hash exists and empty' => ['Expected to find no <a> with href "#"', '<body><a href="#"></a></body>', '', [], ''],
			'failed when only $hash exists and not empty' => ['Expected to find no <a> with href "#hash"', '<body><a href="#hash"></a></body>', '', [], 'hash'],
			'passed when only $hash exists and mismatches' => [null, '<body><a href="#hash-2"></a></body>', '', [], 'hash-1'],
			'failed when $path and $query exist' => ['Expected to find no <a> with href "/url?a=1"', '<body><a href="/url?a=1"></a></body>', '/url', ['a' => '1'], null],
			'failed when $path and $hash exist' => ['Expected to find no <a> with href "/url#hash"', '<body><a href="/url#hash"></a></body>', '/url', [], 'hash'],
			'failed when $query and $hash exist' => ['Expected to find no <a> with href "?a=1#hash"', '<body><a href="?a=1#hash"></a></body>', '', ['a' => '1'], 'hash'],
			'failed when all arguments match' => ['Expected to find no <a> with href "/url?b=2&a=1#hash"', '<body><a href="/url?a=1&b=2#hash"></a></body>', '/url', ['b' => '2', 'a' => '1'], 'hash'],
			'passed when $path mismatches' => [null, '<body><a href="/url?a=1#hash"></a></body>', '/url-0', ['a' => '1'], 'hash'],
			'passed when $query mismatches' => [null, '<body><a href="/url?a=1#hash"></a></body>', '/url', ['a' => '10'], 'hash'],
			'passed when $hash mismatches' => [null, '<body><a href="/url?a=1#hash"></a></body>', '/url', ['a' => '1'], 'hash-0'],
		];
	}

	#[Test]
	public function namespacedXml(): void {
		$this->xml('<?xml version="1.0" encoding="UTF-8" ?><root xmlns:x="http://example.com"><x:element /></root>')->xpath('//x:element')->assertExists();
	}

	#[Test]
	public function complexHtmlDocument(): void {
		$html = $this->html(file_get_contents(__DIR__ . '/../fixture/data.html'), false);
		$html->assertAnchorExists('/login', ['action' => 'register']);
		$html->query('.assert-children-count')->assertChildrenCount(3);
		$html->query('.assert-count')->assertCount(3);
		$html->query('.assert-empty')->assertEmpty();
		$html->query('.assert-not-empty')->assertNotEmpty();
		$html->query('.assert-exists')->assertExists();
		$html->query('.assert-not-exists')->assertNotExists();
		$html->query('.assert-text-equals')->assertTextEquals('Hello, World!');
		$html->query('.assert-text-equals')->assertTextNotEquals('Text');
		$html->query('.assert-text-contains')->assertTextContains('World');
		$html->query('.assert-text-contains')->assertTextNotContains('Text');
		$html->query('.assert-matches-regex')->assertMatchesRegex('/^\\d+$/');
		$html->query('.assert-matches-regex')->assertNotMatchesRegex('/^[a-z]+$/');
	}

	#[Test]
	public function complexXmlDocument(): void {
		$html = $this->xml(file_get_contents(__DIR__ . '/../fixture/data.xml'), false);
		$html->assertAnchorExists('/login', ['action' => 'register']);
		$html->xpath('//x:body//*[contains(@class, "assert-children-count")]')->assertChildrenCount(3);
		$html->xpath('//x:body//*[contains(@class, "assert-count")]')->assertCount(3);
		$html->xpath('//x:body//*[contains(@class, "assert-empty")]')->assertEmpty();
		$html->xpath('//x:body//*[contains(@class, "assert-not-empty")]')->assertNotEmpty();
		$html->xpath('//x:body//*[contains(@class, "assert-exists")]')->assertExists();
		$html->xpath('//x:body//*[contains(@class, "assert-not-exists")]')->assertNotExists();
		$html->xpath('//x:body//*[contains(@class, "assert-text-equals")]')->assertTextEquals('Hello, World!');
		$html->xpath('//x:body//*[contains(@class, "assert-text-equals")]')->assertTextNotEquals('Text');
		$html->xpath('//x:body//*[contains(@class, "assert-text-contains")]')->assertTextContains('World');
		$html->xpath('//x:body//*[contains(@class, "assert-text-contains")]')->assertTextNotContains('Text');
		$html->xpath('//x:body//*[contains(@class, "assert-matches-regex")]')->assertMatchesRegex('/^\\d+$/');
		$html->xpath('//x:body//*[contains(@class, "assert-matches-regex")]')->assertNotMatchesRegex('/^[a-z]+$/');
	}
}
