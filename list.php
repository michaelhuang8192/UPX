<?php
require_once( dirname(__file__) . '/init.php' );

$access = intval(hexdec(substr(trim($_REQUEST['access']), 0, 6)));
$tid = intval(trim($_REQUEST['tid']));
$tid || die();

$db = sqlite_open('img.db', 0600, $db_error) or die($db_error);
$res = sqlite_query($db, 'select * from img where tid='.$tid.' and access='.$access, SQLITE_ASSOC);
$rows = sqlite_fetch_all($res, SQLITE_ASSOC);
sqlite_close($db);

$rows || die();

?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
div.img_cnt {width:220px;height:220px;float:left;position:relative;margin:3px;border:1px solid #edebe9;}
div.img_cnt > a {display:block;width:100%;height:100%}
div.img_cnt > a > img {display:block;width:100%;height:100%;padding:0;margin:0;}
</style>

</head>
<body>

<div>
<?php
foreach( json_decode($rows[0]['data']) as $r ) {
    $img_url = $g_img_url . '/' . $r;
    printf('<div class="img_cnt"><a target="_blank" href="%s"><img border="0" alt="" src="%s" /></a></div>',
           $img_url,
           $img_url
           );
}
?>
</div>

</body>
</html>
