<?php
require_once( dirname(__file__) . '/init.php' );

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>uploader</title>

<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.17.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>

<style type="text/css">
body {margin:0;padding:0;}
div#ctrl_cnt {height:30px;padding:5px 0 5px 200px;position:relative;}
.uploader_ctn {position:absolute;top:5px;bottom:5px;left:0;width:180px;}
div#ctrl_cnt > input {font-size:20px;font-family:"Times New Roman",Georgia,Serif;margin:0;}
div#ctrl_cnt > input[type="button"] {margin-right:20px;}
#btn_load, #btn_list {padding:0 10px;height:30px;}
#txt_tid {width:100px;height:26px;outline:none;padding:1px;border:1px solid #ccc;}

div#view_cnt {border-top:1px solid #b8b8b8;}

div.img_cnt {width:220px;height:220px;float:left;position:relative;margin:5px 0 0 5px;border:1px solid #edebe9;}
div.img_cnt img.img_elem {display:block;width:220px;height:220px;padding:0;margin:0;}
div.img_cnt span {display:block;position:absolute;top:100px;left:0px;text-align:center;width:220px;height:20px;}
div.img_cnt span.img_name {top:130px;white-space:nowrap;overflow:hidden;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#036}
div.img_cnt span.img_btn_rm {top:4px;left:200px;width:16px;height:16px;background-image:url('remove.gif');cursor:pointer}
div.img_cnt span.img_percent {width:200px;margin:0 10px;}

.ui-progressbar-value {background-image: url('pbar-ani.gif');}

#passwd_dlg {text-align:center;}
#passwd_dlg input {width:200px;}

</style>

<script type="text/javascript">

var g_up = null;
var g_nr = -2;
var g_img_cnt = null;
var g_tid = 0;
var g_access = null;
var g_sp_db_seq = 0;

function setcode(code)
{
	g_dlg_passwd.find('#passwd_val').val('');

	var data = {};
	if(code !== undefined) data['code'] = code;
	$.post('setcode.php', data, function(res) {
		if(!res.ret) return;
		g_up.up_srv_set('fieldname', 'fn_' + $.cookie('auth'));
		g_dlg_passwd.dialog("close");
	}, 'json');
}

function up_cli_log(s)
{
	console.log(s);
}

function up_cli_ready()
{
	g_nr++; if(!g_nr) uploader_init();
}

function uploader_init()
{
	up_cli_log("uploader start...");
	
	g_up.up_srv_set('url', '<?php print $g_req_url;?>/upload.php');
	g_up.up_srv_set('cli_ready', 1);
	
	g_dlg_passwd.dialog("open");
	setcode();
}

function cancel_fd()
{
	var im = $(this).closest('.img_cnt');
	if( !im.is(':visible') ) return false;
	im.hide();
	
	var fid = im.attr('id').substr(4);
	var pct = im.children('span.img_percent');
	if( pct.length ) {
		pct.remove();
		up_cli_log('+cancel ' + fid);
		g_up.up_srv_cancel(fid);
		
	}
	
	if( !$('span.img_percent', g_img_cnt).length ) update_db();
	
	return false;
}

function open_fd()
{
	var src = $(this).children('img.img_elem').attr('src');
	if(src) window.open(src, '_blank');
	return false;
}

function update_db()
{
	var ims = $('img.img_elem:visible', g_img_cnt);
	var arr = [];
	for(var i = ims.length - 1; i >= 0; i--)
		arr.push( $(ims[i]).attr('src').replace('<?php print $g_img_url;?>/', '') );

	var seq = ++g_sp_db_seq;
	$.post('db.php', {'img': JSON.stringify(arr), 'tid': g_tid, 'seq': seq}, function(data) {
		if(seq != g_sp_db_seq || !data) return;
		g_tid = data.tid;
		if(data.access !== undefined) g_access = data.access;
		g_v_txt_tid.val(g_tid);
		
	}, 'json');
}

function load_db()
{
	g_tid = 0;
	g_access = 0;
	g_img_cnt.empty();
	
	tid = parseInt(g_v_txt_tid.val());
	g_v_txt_tid.val('');
	if( isNaN(tid) ) return;
	
	var seq = ++g_sp_db_seq;
	$.post('ldb.php', {'tid': tid, 'seq': seq}, function(data) {
		if(seq != g_sp_db_seq || !data) return;
		g_tid = data.tid;
		if(data.access !== undefined) g_access = data.access;
		g_v_txt_tid.val(g_tid);
		
		var d = data.data
		for(var i = 0; i < d.length; i++) {
			var fid = -i;
			var im = $('<div id="img_' + fid + '" class="img_cnt"></div>')
			.click(open_fd)
			.attr('title', fid)
			.append( $('<img class="img_elem" border="0" alt="" />').attr('src', '<?php print $g_img_url;?>/' + d[i]) )
			.append( $('<span class="img_name"></span>').text(fid) )
			.append( $('<span class="img_btn_rm"></span>').click(cancel_fd) );
		
			g_img_cnt.prepend(im);
		}
		
	}, 'json');
}

function up_cli_add(fds)
{
	for(var i = 0; i < fds.length; i++) {
		var fd = fds[i];
		var fid = fd[0];
		
		var im = $('<div id="img_' + fid + '" class="img_cnt"></div>')
			.click(open_fd)
			.attr('title', fid + ' - ' + fd[1])
			.append( $('<img class="img_elem" border="0" alt="" />') )
			.append( $('<span class="img_percent"></span>').progressbar({value: 0}) )
			.append( $('<span class="img_name"></span>').text(fid + ' - ' + fd[1]) )
			.append( $('<span class="img_btn_rm"></span>').click(cancel_fd) );
		
		g_img_cnt.prepend(im);
	}
	
}

function up_cli_update(fds)
{
	for(var i = 0; i < fds.length; i++) {
		var fd = fds[i];
		var im = $('div#img_' + fd[0], g_img_cnt);
		
		if(fd[2]) {
			var res = fd[2] > 0 && fd[3] && $.parseJSON(fd[3]) || {nz:''}
			$('> span.img_percent', im).remove();
			if(res.nz)
				$('img.img_elem', im).attr('src', '<?php print $g_img_url;?>/' + res.nz);
			else
				$('img.img_elem', im).attr('src', 'noimg.jpg');
			
			if( !$('span.img_percent', g_img_cnt).length ) update_db();
			
		} else {
			$('span.img_percent', im).progressbar("option", "value", Math.round(fd[1]));
		}
	}
}

$(function() {
	g_up = $('#uploader').get(0);
	g_img_cnt = $('div#view_cnt');
	
	g_dlg_passwd = $("#passwd_dlg").dialog({modal:true, width:400, height:120, resizable:false, autoOpen:false});
	$('#passwd_btn').button().click(function() {
		var pv = $('#passwd_val');
		setcode(pv.val());
		pv.val('');
	});
	
	g_v_btn_load = $('#btn_load').button().click(load_db);
	$('#btn_list').button().click(function() { if(g_tid && g_access !== null) window.open('list.php?tid=' + g_tid + '&access=' + g_access, '_blank'); });
	g_v_txt_tid = $('#txt_tid').keyup(function(e) { if(e.which == 13) g_v_btn_load.click(); });
	
	up_cli_ready();
});

</script>

</head>
<body>

<div id="ctrl_cnt">
	<div class="uploader_ctn">
	<object style="z-index:-9999" id="uploader" type="application/x-shockwave-flash" data="up.swf" width="180" height="30">
		<param name="play" value="true" />
		<param name="loop" value="true" />
	</object>
	</div>
	
	<input id="txt_tid" type="text" />
	<input id="btn_load" type="button" value="Load" />
	<input id="btn_list" type="button" value="List" />
	
</div>

<div id="view_cnt"></div>

<div id="passwd_dlg" title="passcode">
	<input id="passwd_val" type="password" />
	<span id="passwd_btn">OK</span>
</div>

</body>
</html>
