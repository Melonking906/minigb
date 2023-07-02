<?php
# https://stackoverflow.com/questions/409496/prevent-direct-access-to-a-php-include-file
# Redirects a user who tries to access include files from the browser to index.php

require_once(__DIR__ . "/../cfg.php");

if (!defined('IncludeAccess')) {
	header("Location: ../" . $script_fn);
}
?>