<?php

namespace Toustobot\Utils;

use Nette\Utils\Strings;


class Matcher
{
	public static function matchesDate(\DateTimeInterface $date, string $string): bool
	{
		$day = (int) $date->format('j');
		$month = (int) $date->format('n');
		$datePattern = sprintf('/\b0?%s\b.+\b0?%s\b/u', $day, $month);
		return (bool) Strings::match($string, $datePattern);
	}
}
