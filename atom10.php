<?php
include(dirname(__FILE__).'/app/classes/class.Fortunes.sqlite.php');
include(dirname(__FILE__).'/app/lib/http.php');
include(dirname(__FILE__).'/custom/config.php');

$cookie_jar = new CookieJar($fortunes_config);
$cookie_jar->load('all',10);

if (!$cookie_jar->count()>0){
    die();
}

$latest_cookie = current($cookie_jar->cookies);
$last_modified = $latest_cookie->getDate();
$identifier = $latest_cookie->getId();
http_modified($last_modified,$identifier);

header('Content-type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
?><feed xmlns="http://www.w3.org/2005/Atom">
    <title><?php echo FORTUNES_NAME ?></title>
    <subtitle>All your fortunes are belong to us</subtitle>
    <id><?php echo FORTUNES_URL ?></id>
    <link rel="self" type="application/atom+xml" href="<?php echo FORTUNES_URL ?>atom10.php" />
    <link rel="alternate" type="text/html" href="<?php echo FORTUNES_URL ?>" />
    <updated><?php echo date("Y-m-d\TH:i:s\Z") ?></updated>
    <author><name>People</name></author>
<?php
foreach ($cookie_jar->cookies as $cookie){
    echo $cookie->getAtom10Entry();
}
?>
</feed>