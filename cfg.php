<?php
/* minigb global included variables file */

/* Booleans */

# Show minigb 88x31 button and version info
$powered_by = 1;

# Enable or disable new entries for all users
$disable_entries_all = 0;

# Enable or disable cookie to allow one post comment per client (this will be applied for all users)
$unique_cookie = 1;

# Enable or disable user listing
$user_listing = 1;

# Show variable dumps of entries array (for debugging)
$dump_entries = 0;

# Show debug information (execution time and mem usage)
$debug_info = 0;

/* Directories */

# Smileys directory
$smileys_dir = "img/smileys";

# Icons directory
$icons_dir = "img/icons";

# Users directory
$users_dir = "users";

/* Filenames */

# gecko-minigb filename
$script_fn = "index.php";

# Default CSS filename
$default_css_fn = "default.css";

# Default GB CSS filename
$default_gb_css_fn = "default_gb.css";

# Version info INI filename
$verinfo_fn = "verinfo.ini";

/* Misc */

# Max length for posts
$max_length_name = 40;
$max_length_email = 250;
$max_length_url = 250;
$max_length_comment = 520;

/* Arrays */

# Error/success messages
$messages = array(
	'db_missing' => 'ERROR: Database file not found.',
	'perms_invalid' => 'ERROR: This user&apos;s database file doesn&apos;t have set read/write permissions for all users (<b>0666</b>).',
	'input_empty' => 'ERROR: Name and Comment cannot be empty!',
	'url_invalid' => 'ERROR: Invalid URL format (use required: <b>http://example.org/</b>).',
	'email_invalid' => 'ERROR: Invalid Email format (use required: <b>example@example.org</b>).',
	'captcha_invalid' => 'ERROR: Invalid CAPTCHA.',
	'disabled_entries' => 'ERROR: This user or the administrator of this guestbook has disabled new entries.',
	'max_length_name' => 'ERROR: Maximum character length for guest name is ' . $max_length_name  . '.',
	'max_length_email' => 'ERROR: Maximum character length for guest email is ' . $max_length_email  . '.',
	'max_length_url' => 'ERROR: Maximum character length for guest URL is ' . $max_length_url  . '.',
	'max_length_comment' => 'ERROR: Maximum character length for guest comments is ' . $max_length_comment . '.',
	'cookie_set' => 'ERROR: You have already sent a message in this browser, please wait 1 hour to send another one.',
	'posted_message' => 'Message posted successfully!'
);

# Smileys list
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

# Smileys image names
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
