<?php

$json = json_decode(file_get_contents(__DIR__ . '/composer.json'));

foreach (@$json->autoload->files as $file) {
	if ($path = realpath(__DIR__ . '/' . $file))
		require_once $path;
}
