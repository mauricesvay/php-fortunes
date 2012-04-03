<?php
include(dirname(__FILE__).'/app/classes/class.Fortunes.sqlite.php');
include(dirname(__FILE__).'/app/lib/http.php');
include(dirname(__FILE__).'/custom/config.php');

$cookie_jar = new CookieJar($fortunes_config);
$cookie_jar->load('all');
$raw_cookies = Array();

if (!$cookie_jar->count()>0){
    die();
}

foreach ($cookie_jar->cookies as $cookie){
    $raw_cookies[] =  $cookie->getRawContent();
}

header('Content-type: text/plain');
echo implode("\n%\n", $raw_cookies);
?>