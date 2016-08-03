<?php
require_once( dirname(__file__) . '/init.php' );
check_auth() || die();

isset($_REQUEST['img']) or die();
isset($_REQUEST['tid']) or die();

$img = trim($_REQUEST['img']);
$tid = intval(trim($_REQUEST['tid']));

if(!$tid && !$img) die();

$new = false;
if(!file_exists('img.db')) $new = true;

$db = @sqlite_open('img.db', 0600, $db_error) or die($db_error);
if($new) sqlite_query($db, 'CREATE TABLE img(tid INTEGER PRIMARY KEY ASC, access UNSIGNED INTEGER, tts UNSIGNED INTEGER, data TEXT);');

$ret = array('tid'=>$tid);
if($tid) {
    sqlite_query($db, "UPDATE img set data='" . sqlite_escape_string( $img ) . "' WHERE tid=$tid;");
} else {
    $access = substr(md5(microtime(true)), 0, 6);
    sqlite_query($db, "INSERT INTO img VALUES (NULL, " . hexdec($access) . ", " . time() . ", '" . sqlite_escape_string( $img ) . "');");
    $ret['tid'] = sqlite_last_insert_rowid($db);
    $ret['access'] = $access;
}

sqlite_close($db);

print json_encode($ret);

?>
