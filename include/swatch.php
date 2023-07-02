<?php
# https://wiki.melonland.net/swatch_time
# Returns the current Swatch time with microbeats

require_once(__DIR__ . "/redir_include.php");

function GetSwatchTime($showDecimals = true, $dateTime) {
	// Get time in UTC+1 (Do not Change!)
	$now = new DateTime($dateTime, new DateTimeZone("UTC"));
	$now->add(new DateInterval("PT1H"));

	// Calculate the seconds since midnight e.g. time of day in seconds
	$midnight = clone $now;
	$midnight->setTime(0, 0);
	$seconds = $now->getTimestamp() - $midnight->getTimestamp();

	// Swatch beats in seconds - DO NOT CHANGE
	$swatchBeatInSeconds = 86.4;

	// Calculate beats to two decimal places
	if ($showDecimals) {
		return number_format(round(abs($seconds / $swatchBeatInSeconds), 2), 2);
	} else {
        	return floor(abs($seconds / $swatchBeatInSeconds));
	}
}