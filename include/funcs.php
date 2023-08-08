<?php
require_once(__DIR__ . "/../cfg.php");

# https://stackoverflow.com/questions/409496/prevent-direct-access-to-a-php-include-file
# Redirects a user who tries to access include files from the browser to index.php

if (!defined('IncludeAccess')) {
	header("Location: ../" . $script_fn);
	exit;
}

# https://wiki.melonland.net/swatch_time
# Returns the current Swatch time with microbeats

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
