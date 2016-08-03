<?php
require_once( dirname(__file__) . '/init.php' );
check_auth() || die();

isset($_REQUEST['tid']) or die();
$tid = intval(trim($_REQUEST['tid']));
$tid || die();

$db = sqlite_open('img.db', 0600, $db_error) or die($db_error);

$res = sqlite_query($db, 'select access,data from img where tid='.$tid, SQLITE_ASSOC);
$rows = sqlite_fetch_all($res, SQLITE_ASSOC);
sqlite_close($db);


$rows || die();

$row = $rows[0];
$res = array(
             'tid' => $tid,
             'access' => dechex($row['access']),
             'data' => json_decode($row['data'])
);
die(json_encode($res));

?>