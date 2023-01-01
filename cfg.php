<?php

/* minigb included variables file */

// Default username (replace this to your username)
$default_user = "example";

// Default entries TXT database file
$database = "entries_$default_user";

// Default CSS style file
$style = "style_$default_user";

// Max length for posts
$max_length_name = 40;
$max_length_email = 250;
$max_length_url = 250;
$max_length_comment = 520;

// Error messages
$messages = array(
	'database_missing' => 'ERROR: Database file not found for this user.',
	'input_empty' => 'ERROR: Name and Comment cannot be empty!',
	'url_invalid' => 'ERROR: Invalid URL format (use required: <b>http://example.org/</b>).',
	'email_invalid' => 'ERROR: Invalid Email format (use required: <b>example@example.org</b>).',
	'captcha_invalid' => 'ERROR: Invalid Math CAPTCHA.',
	'disabled_entries' => 'ERROR: The webmaster of this guestbook disabled new entries.',
	'max_length_name' => 'ERROR: Maximum character length for guest name is ' . $max_length_name  . '.',
	'max_length_email' => 'ERROR: Maximum character length for guest email is ' . $max_length_url  . '.',
	'max_length_url' => 'ERROR: Maximum character length for guest URL is ' . $max_length_url  . '.',
	'max_length_message' => 'ERROR: Maximum character length for guest comments is ' . $max_length_comment . '.',
);

// Smileys list
// (if you are going to add a new one, do it on lowercase and remember also to add its image name in the array below and respect the order, otherwise the smiley replacement will work incorrectly)
$smileys = array(
	":)",
	":(",
	":d",
	":p",
	";)",
	"b)",
	":o",
	":eek:",
	":crazy:",
	":love:"
);

// Smileys image names
// (if you are going to add one here, make sure they are in GIF format (or in any other in case if you edited the smileys image format in the minigb script))
$smileys_img = array(
	"smile",
	"sad",
	"grin",
	"stick",
	"wink",
	"cool",
	"gasp",
	"eek",
	"crazy",
	"love"
);

// Smileys directory
$smileys_dir = "img/smileys";

// Show minigb 88x31 button and version info
$powered_by = 1;

// Enable or disable new entries (this will be applied to all users)
$disable_entries = 0;

// Version information
$mgb_ver = "0.01a";
$mgb_verdate = "27 Dec 2022";

?>
