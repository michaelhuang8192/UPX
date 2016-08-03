<?php
require_once( dirname(__file__) . '/init.php' );

$g_auth = gen_auth($g_passwd);

$in_auth = null;
if( isset($_POST['code']) )
    $in_auth = gen_auth( trim($_POST['code']) );
else if( isset($_COOKIE['auth']) )
    $in_auth = trim($_COOKIE['auth']);

$ret = 0;
if($in_auth === $g_auth) {
    $ret = 1;
    setcookie('auth', $in_auth, 0);
} else
    setcookie('auth', '', time() - 3600);

die(json_encode( array('ret'=>$ret) ));

?>
