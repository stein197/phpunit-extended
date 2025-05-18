<?php
namespace Stein197\PHPUnit;

use function array_key_exists;
use function gettype;
use function is_array;

function array_is_subset(array $superset, array $subset): bool {
	foreach ($subset as $k => $subsetValue) {
		if (!array_key_exists($k, $superset))
			return false;
		$supersetValue = $superset[$k];
		if (gettype($supersetValue) !== gettype($subsetValue) || (is_array($subsetValue) ? !array_is_subset($supersetValue, $subsetValue) : $supersetValue !== $subsetValue))
			return false;
	}
	return true;
}
