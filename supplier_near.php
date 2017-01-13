<?php
/**
 * Created by iMac
 * 火一五信息科技有限公司
 * 联系方式:15288986891
 * QQ:3186355915
 * web:http://host.huo15.com
 * 日期：2017/1/12
 */
 
define('IN_ECS', true);

require_once(dirname(__FILE__) . '/includes/init_supplier.php');
require_once(dirname(__FILE__) . '/mobile/includes/Geohash.php');

@$_REQUEST['act'] = $_REQUEST['act'] ? : 'lists';

if ($_REQUEST['act'] == 'lists') {

	if (@$_REQUEST['is_ajax']) {
		$address = get_address($_REQUEST['lat'], $_REQUEST['lng']);
		exit($address);
	}


	/*require_once "wxjs/jssdk.php";
	$ret = $db->getRow("SELECT  *  FROM `ecs_weixin_config`");
	$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
	$signPackage = $jssdk->GetSignPackage();
	$smarty->assign('signPackage',  $signPackage);
	$latitude = @$_SESSION['latitude'];
	$longitude = @$_SESSION['longitude'];
	$address = @$_SESSION['location_address'];
	$smarty->assign("address", $address);

	$is_jssdk = true;

	if($latitude && $longitude) {
		$is_jssdk = false;
	}
	$smarty->assign("is_jssdk", $is_jssdk);

	*/


	/* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/12 */

	//7686746443c75099720a3ea70d458fdd
	//$ip = huo15_get_client_ip();
	//$ip = $_SERVER["REMOTE_ADDR"];

	$ip = real_ip();
	if ($ip=="127.0.0.1") {
		$url = "http://api.map.baidu.com/location/ip?ak=7686746443c75099720a3ea70d458fdd&coor=bd09ll";


	} else {
		$url = "http://api.map.baidu.com/location/ip?ip=".$ip."&ak=7686746443c75099720a3ea70d458fdd&coor=bd09ll";

	}
	$res = file_get_contents($url);
	$cityObj = json_decode($res);

	$content = $cityObj->content;
	$address_detail = $content->address_detail;
	$_SESSION['country'] = "中国";
	$_SESSION['province'] = $address_detail->province;
	$_SESSION['province'] = str_replace("省","",$_SESSION['province']);
	$_SESSION['city'] = $address_detail->city;
	$_SESSION['city'] = str_replace("市","",$_SESSION['city']);
	$point = $content->point;
	$_SESSION['latitude'] = $point->y;
	$_SESSION['longitude'] = $point->x;
	$_SESSION['location_address'] = $content->address;
	$latitude = @$_SESSION['latitude'];
	$longitude = @$_SESSION['longitude'];
	$address = @$_SESSION['location_address'];
	$smarty->assign('latitude', $_SESSION['latitude']);
	$smarty->assign('longitude', $_SESSION['longitude']);
	$smarty->assign("address", $address);
	/* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/12 end */





	// 分页
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('supplier'). " WHERE status=1 AND latitude<>'' AND longitude<>''");
	$pager  = get_pager('supplier_near.php', array(), $record_count, $page);
	// 获取所有店铺


	$sql="SELECT supplier_id,user_id,supplier_name,latitude,longitude FROM ". $ecs->table("supplier") ." WHERE status=1 AND latitude<>'' AND longitude<>''";
	/* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/13 */

	if (isset($_REQUEST['province'])) {
		@$_REQUEST['province'] = intval($_REQUEST['province']) ? : 0;
		@$_REQUEST['city'] = intval($_REQUEST['city']) ? : 0;

		if ($_REQUEST['province']) {
			$sql.= " and province=".$_REQUEST['province'];
		}
		if ($_REQUEST['city']) {
			$sql.= " and city=".$_REQUEST['city'];
		}
		if (isset($_REQUEST['supplier_shop_name']) && $_REQUEST['supplier_shop_name'] !='') {
			$sql.=" and supplier_name like '%".addslashes_deep($_REQUEST['supplier_shop_name'])."%'";
		}
	} else {
		if (isset($_REQUEST['supplier_shop_name']) && $_REQUEST['supplier_shop_name'] !='') {
			$sql.=" and supplier_name like '%".addslashes_deep($_REQUEST['supplier_shop_name'])."%'";
		}
	}
	/* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/13 end */

	$supplier_list = $db->GetAll($sql);

	$geohash = new Geohash();

	foreach ($supplier_list as $key => $supplier) {
		// 获取距离
		$distance = $geohash->getDistance($latitude, $longitude, $supplier['latitude'], $supplier['longitude']);
		$supplier_list[$key]['distance'] = $distance;

		$unit = "m";
		if ($distance > 1000) {
			$unit = 'km';
			$distance = number_format($distance/1000, 1);
		}
		$supplier_list[$key]['distance_'] = $distance.$unit;
	}

	// 按距离排序
	usort($supplier_list, function($a, $b){
		return $a['distance'] > $b['distance'] ? 1 : -1;
	});

	/* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/12 */
	$all_suppliers = $supplier_list;
	/* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/12 end */
	$supplier_list = array_slice($supplier_list, ($page-1)*$pager['size'], $pager['size']);

	foreach ($supplier_list as $key => $supplier) {
		// 获取店铺配置
		$sql = "SELECT code,value FROM ". $ecs->table("supplier_shop_config") ." WHERE supplier_id=".$supplier['supplier_id'];
		$supplier_config_list = $db->GetAll($sql);

		foreach ($supplier_config_list as $cof) {
			$code = $cof['code'];
			if ($code == 'shop_address' || $code == 'shop_name' || $code == 'shop_logo' || $code == "service_phone") {
				$supplier_list[$key][$code] = $cof['value'];
			}
			if ($code == 'shop_country' && $cof['value']){
				$supplier_list[$key]['shop_country'] = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id=".$cof['value']);
			}
			if ($code == 'shop_province' && $cof['value']){
				$supplier_list[$key]['shop_province'] = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id=".$cof['value']);
			}
			if ($code == 'shop_city' && $cof['value']){
				$supplier_list[$key]['shop_city'] = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id=".$cof['value']);
			}
		}

		// 查询店铺下的商品
		$sql2 = "SELECT goods_id,goods_name,shop_price,market_price,goods_img,goods_thumb FROM ". $ecs->table("goods") ." WHERE supplier_id=". $supplier['supplier_id'] ." LIMIT 0,3";
		$goods_list = $db->GetAll($sql2);

		$supplier_list[$key]['goods_list'] = $goods_list;
	}
	$supplier_list_json = json_encode($all_suppliers);
	$smarty->assign("ROOTPATH", "");
	$smarty->assign('pager', $pager);
	$smarty->assign('userSession', $_SESSION);
	$smarty->assign('supplier_list', $supplier_list);
	$smarty->assign('supplier_list_json', $supplier_list_json);

	//省列表
	$province_list = get_regions(1, 1);
	/*$city_list = get_regions(2, $row['province']);
	$district_list = get_regions(3, $row['city']);*/

	$smarty->assign('country_list', get_regions());
	$smarty->assign('province_list', $province_list);
	$smarty->assign('city_list', $city_list);
	$smarty->assign('district_list', $district_list);

	$smarty->display("supplier_city.dwt");




}
else if ($_REQUEST['act'] == 'map') {
	$supplier_id = intval($_REQUEST['supplier_id']);
	$sql="SELECT supplier_id,user_id,supplier_name,latitude,longitude FROM ". $ecs->table("supplier") ." WHERE supplier_id=". $supplier_id;

	$supplier = $db->getRow($sql);

	if ($supplier['latitude'] && $supplier['longitude']){

		$smarty->assign('latitude', $supplier['latitude']);
		$smarty->assign('longitude', $supplier['longitude']);

		$smarty->display("supplier_map.dwt");
	}
}

function get_address($lat, $lng){
	// 火一五信息科技 huo15.com Created by apple on 2017/1/8.
	//36.095040,120.399830
	// 逆地址解析
	$key = "RQNBZ-FOQ32-VVSUW-CHGRE-TP33F-3NFLO"; // 腾讯地图开发密钥

	$location = $lat.",".$lng;

	$url = "http://apis.map.qq.com/ws/geocoder/v1/?location=$location&key=$key&get_poi=1";

	$res = file_get_contents($url);

	$arr = json_decode($res, true);

	$address = $arr['result']['address'];

	$_SESSION['latitude'] = $lat;
	$_SESSION['longitude'] = $lng;
	$_SESSION['location_address'] = $address;

	return $address;
}

?>