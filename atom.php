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
?><feed version="0.3" xmlns="http://purl.org/atom/ns#">
    <title><?php echo FORTUNES_NAME ?></title>
    <tagline>All your fortunes are belong to us</tagline>
    <link rel="alternate" type="text/html" href="<?php echo FORTUNES_URL ?>" />
    <modified><?php echo date("Y-m-d\TH:i:s\Z") ?></modified>
    <author><name>People</name></author>
  
<?php
foreach ($cookie_jar->cookies as $cookie){
    echo $cookie->getAtomEntry();
}
?>
</feed>