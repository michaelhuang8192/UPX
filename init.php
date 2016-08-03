<?php
require_once( dirname(__file__) . '/config.php' );

function gen_auth($passwd)
{
    global $g_secret;
    
    $p = md5($passwd . $g_secret, true);
    $i = md5($_SERVER["REMOTE_ADDR"] . $g_secret, true);
    
    $k1 = md5($p . $g_secret . $i, true);
    $k2 = md5($k1 . $g_secret . $i . $p, true);
    
    return base64_encode($k1 . $k2);
}

function gen_img_auth($tid, $ts)
{
    global $g_secret, $g_passwd;
    
    return substr(md5($g_passwd . $g_secret . $tid . $ts, true), 0, 4);
}

function check_auth()
{
    global $g_passwd;
    return isset($_COOKIE['auth']) && trim($_COOKIE['auth']) === gen_auth($g_passwd);
}

?>