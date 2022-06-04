<?php

namespace App\Services;

class Censurator
{
	const BAD_WORDS = ["/enculé/", "/diantre/", "/damned/"];

	public function purify($text): string
	{
		return preg_replace(self::BAD_WORDS, "*", $text);
	}
}