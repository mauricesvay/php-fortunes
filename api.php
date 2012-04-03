<?php
include(dirname(__FILE__).'/app/classes/class.Fortunes.sqlite.php');
include(dirname(__FILE__).'/custom/config.php');

$cookie_jar = new CookieJar($fortunes_config);
$response = '';

/**
 * Basic HTTP auth
 */
function fortunes_auth(){
    return (
        isset($_SERVER['PHP_AUTH_USER']) && 
        isset($_SERVER['PHP_AUTH_PW']) && 
        ($_SERVER['PHP_AUTH_USER'] == FORTUNES_ADMIN_LOGIN) && 
        ($_SERVER['PHP_AUTH_PW'] == FORTUNES_ADMIN_PASS)
    );
}

//Give a positive vote
if (isset($_POST['vote'])){
    if (isset($_POST['id'])){
        $response = '{"id":'.$_POST['id'].', "result":'.$cookie_jar->vote($_POST['id'],1).'}';
    } else {
        $response = '{"id":null, "result":"no id"}';
    }
//Give a negative vote
} elseif (isset($_POST['bury'])){
    if (isset($_POST['id'])){
        $response = '{"id":'.$_POST['id'].', "result":'.$cookie_jar->vote($_POST['id'],-1).'}';
    } else {
        $response = '{"id":null, "result":"no id"}';
    }
//Set a fortune cookie offline
} elseif (isset($_POST['offline'])){
    if (!fortunes_auth()) {
        header('WWW-Authenticate: Basic realm="Auth"');
        header('HTTP/1.0 401 Unauthorized');
        $response = '{"id":null, "result":"auth required"}';
    } else {
        if (isset($_POST['id'])){
            $response = '{"id":'.$_POST['id'].', "result":'.$cookie_jar->offline($_POST['id']).'}';
        } else {
            $response = '{"id":null, "result":"no id"}';
        }
    }
//Set a fortune cookie online
} elseif (isset($_POST['online'])){
    if (!fortunes_auth()) {
        header('WWW-Authenticate: Basic realm="Auth"');
        header('HTTP/1.0 401 Unauthorized');
        $response = '{"id":null, "result":"auth required"}';
    } else {
        if (isset($_POST['id'])){
            $response = '{"id":'.$_POST['id'].', "result":'.$cookie_jar->online($_POST['id']).'}';
        } else {
            $response = '{"id":null, "result":"no id"}';
        }
    }
}

//Return json or go back ?
if (isset($_GET['ajax'])) {
    header('Content-type: application/json');
    echo $response;
} else {
    if (isset($_POST['id'])){
        $back = 'index.php?view=one&id='.$_POST['id'];
    } elseif (isset($_SERVER['HTTP_REFERER'])) {
        $back = $_SERVER['HTTP_REFERER'];
    } else {
        $back = 'index.php';
    }
    header('Location: '.$back);
}
die();
?>