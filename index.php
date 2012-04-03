<?php
include(dirname(__FILE__).'/app/classes/class.Fortunes.sqlite.php');
include(dirname(__FILE__).'/app/lib/http.php');
include(dirname(__FILE__).'/custom/config.php');

$cookie_jar = new CookieJar($fortunes_config);

if (isset($_GET['view'])) {
    switch($_GET['view']){
        case 'all':
            $cookie_jar->load('all');
            //HTTP Cache
            $latest_cookie = current($cookie_jar->cookies);
            http_modified($latest_cookie->getDate(),$latest_cookie->getId());
            break;
        case 'top':
            $cookie_jar->load('all', 30, 'vote DESC');
            break;
        case 'bottom':
            $cookie_jar->load('all', 30, 'vote ASC');
            break;
        case 'one':
            if (isset($_GET['id'])){
                $cookie_jar->load($_GET['id'], 1);
            }
            break;
        case 'featuring':
            $cookie_jar->load('all', 30, 'id DESC', $_GET['nick']);
            break;
        default:
            $cookie_jar->load('all', 30);
            //HTTP Cache
            $latest_cookie = current($cookie_jar->cookies);
            http_modified($latest_cookie->getDate(),$latest_cookie->getId());
            break;
    }
} else {
    $cookie_jar->load('all', 30);
    //HTTP Cache
    $latest_cookie = current($cookie_jar->cookies);
    http_modified($latest_cookie->getDate(),$latest_cookie->getId());
}

header('Content-type: text/html;charset=UTF-8');
include_once(dirname(__FILE__).'/custom/tpl/index.tpl.php');
die();
?>