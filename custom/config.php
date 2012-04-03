<?php
define('FORTUNES_ADMIN_LOGIN', 'admin');
define('FORTUNES_ADMIN_PASS', 'pass');

define('FORTUNES_URL', 'http://example.com/'); //don't forget the trailing slash
define('FORTUNES_NAME', 'Fortunes');

$fortunes_file = dirname(__FILE__).'/fortunes.sqlite';
$fortunes_users_file = dirname(__FILE__).'/users.txt';
$fortunes_colors_file = dirname(__FILE__).'/colors.txt';

$fortunes_config = new FortunesConfig($fortunes_file, $fortunes_users_file, $fortunes_colors_file);
?>