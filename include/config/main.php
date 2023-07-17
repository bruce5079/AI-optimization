<?php
return array(
	/*
		網站基本資料
		Website_Name 					網站名稱顯示於title
		Service_Mail 					網站服務信箱
		File_Real_Path 				網站可讀取的真實路徑
		File_Include_Path 		網站系統架構存放的路徑( include php template 等)
		Web_Path 							網站的實際網址
		JS_Directory 					網站可讀取的javascript 目錄名稱
		CSS_Directory					網站可讀取的css 目錄名稱
		Lang_Directory				網站不可讀取的lang 目錄名稱
		Controller_Directory	網站系統架構存放管理程式 目錄名稱
		Template_Directory		網站系統架構存放html樣板 目錄名稱
		PHP_Directory					網站系統架構存放php程式 目錄名稱
		LOG_Directory					網站系統架構存放log紀錄 目錄名稱


  $Level_kind=array("","教職員","系所管理員","","","","","","","生輔組員","系統管理員");
  $show_lev_word=array("","博士","碩士","大學部");
  $url_target=array("_self","_top","_blank");

	*/

	'Website_Name'=>'國立陽明交通大學後端管理系統',
	'Website_Name_Short'=>'國立陽明交通大學',//
	'testsite_symbol'   =>'',//判斷測試機用
	'Service_Mail'=>array(
		'tsenghh@mail.nctu.edu.tw'
	),
	'File_Real_Path'=> dirname(__FILE__).'/../../public_html',
	'File_Include_Path'=> dirname(__FILE__).'/../../',

	'Web_Path'=> ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on' )?"https://":"http://").((isset($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST']:"beclass.xiong.com.tw/"),
	'Layout_Path'=>"/views/layouts",
	'IMG_Directory'=>'images',
	'JS_Directory'=>'js',
	'CSS_Directory'=>'css',
	'Lang_Directory'=>'language',
	'Controller_Directory'=>'controller',
	'Template_Directory'=>'template',
	'PHP_Directory'=>'php',
	'LOG_Directory'=>'log',
	'ADMIN_Directory'=>'system_manager',
	'WebsiteLnaguage'=>'zh-TW',//en zh-TW zh-CN

	/*版本號管理 沒有版本設空*/
	'version'=>array(
		'PHP_Code_Version' => ''
		,'UI_Code_Version' => ''
	),

	/*資料庫連線設定*/

    'db'=>array(
		'host' => 'localhost'
		,'username' => 'root'
		,'password' => 'q1w2e3r4'
		,'charset' => 'utf8'
		,'dbase' => 'nctu'
	),
	/*cache 網頁設定*/
	'cache_config'=>array(
		'cacheDirectory'=>'cache',
		'cacheMode'=>'CONTENT', //可以只有 content 部分做cache 或是整頁做cache  ex:CONTENT | ALL
		'runMode'=>false, //false or true
		'refreshSec'=>1800
	),

	/*可以看到除錯畫面的IP*/
	'DebugIPAddress'=>array(
		'192.168.1.1'
	),

	//允須通過的IP放在website_access_ip.php 由dbindexcreate 產生 放在各controller
	'Permit_IP'=>array(
		'*'
	),

	//不允許通過的IP
	'NO_Permission_IP'=>array(
		''
	),
);
?>