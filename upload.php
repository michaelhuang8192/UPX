<?php
require_once( dirname(__file__) . '/init.php' );

$ret = array('nz'=>'');
$exs = array('jpg', 'gif', 'png');
$tar = dirname(__FILE__).'/../img/';

$fn = 'fn_' . gen_auth($g_passwd);

mt_srand();
if(isset($_FILES[$fn]) && isset($_FILES[$fn]['error']) && $_FILES[$fn]['error'] === UPLOAD_ERR_OK) {

    $nzp = explode('.', $_FILES[$fn]['name']);
    $ext = count($nzp) < 2 ? '' : trim(strtolower(end($nzp)));
    if($ext && in_array($ext, $exs, true)) {
        $src = $_FILES[$fn]['tmp_name'];
        $fnz = strftime("%d_%H%M%S").'_'.md5(mt_rand() . microtime(true) . getmygid() . $_FILES[$fn]['name']).'.'.$ext;
        $dst_inner_dir = date('y').'/'.date('m');
        $dst_dir = $tar.$dst_inner_dir;
        $dst = $dst_dir.'/'.$fnz;
        if( $dst && (file_exists($dst_dir) || mkdir($dst_dir, 0711, true)) && move_uploaded_file($src, $dst) )
            $ret['nz'] = $dst_inner_dir.'/'.$fnz;
    }
}

die(json_encode($ret));

?>