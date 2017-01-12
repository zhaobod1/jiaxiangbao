<?php
function huo15_get_client_ip()
{
	if ($_SERVER['REMOTE_ADDR']) {
		$cip = $_SERVER['REMOTE_ADDR'];
	} elseif (getenv("REMOTE_ADDR")) {
		$cip = getenv("REMOTE_ADDR");
	} elseif (getenv("HTTP_CLIENT_IP")) {
		$cip = getenv("HTTP_CLIENT_IP");
	} else {
		$cip = "unknown";
	}
	return $cip;
}






if (huo15_get_client_ip() == '127.0.0.1') {

	// database host
	$db_host   = "211.149.214.242";
	// database name
	$db_name   = "jxb";

// database username
	$db_user   = "root";

// database password
	$db_pass   = "huo15com";



	// database name
	$db_name   = "jxb";

// database username
	$db_user   = "jxb";

// database password
	$db_pass   = "huo15com";


} else {

	// database host
	$db_host   = "localhost";
	// database name
	$db_name   = "jxb";

// database username
	$db_user   = "jxb";

// database password
	$db_pass   = "huo15com";
}


// table prefix
$prefix    = "ecs_";

$timezone    = "Asia/Shanghai";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";

define('EC_CHARSET','utf-8');

if(!defined('ADMIN_PATH'))
{
define('ADMIN_PATH','adminzyz');
}
if(!defined('ADMIN_PATH_M'))
{
define('ADMIN_PATH_M','adminzyz');
}

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '2017-01-12 16:21:15');

define('LICENCE', '');

define('LICENCE_TIME', '2016-12-22 17:56:38');

/**
0 关闭debug
1 显示错误信息
2 关闭缓存
4 显示debug页面
8 记录sql查询

所有的调试模式都开启：
15 = 1 + 2 + 4 + 8
 */

define('DEBUG_MODE', '0');

?>