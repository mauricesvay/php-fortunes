<?php
function http_modified($last_modified,$identifier){
    $etag = '"'.md5($last_modified.$identifier).'"';
    $client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false;
    $client_last_modified = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
    $client_last_modified_timestamp = strtotime($client_last_modified);
    $last_modified_timestamp = strtotime($last_modified);
    
    if(($client_last_modified && $client_etag) ? (($client_last_modified_timestamp == $last_modified_timestamp) && ($client_etag == $etag)) : (($client_last_modified_timestamp == $last_modified_timestamp) || ($client_etag == $etag))){
        header('Not Modified',true,304);
        exit();
    }else{
        header('Last-Modified:'.$last_modified);
        header('ETag:'.$etag);
    }
}
?>