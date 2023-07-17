<?php
session_start();
date_default_timezone_set("Asia/Taipei");
ini_set( "memory_limit", "512M");

//網站會用到的變數都在這定義
/*
public:權限是最大的，可以內部調用，實例調用等。
protected: 受保護類型，用於本類和繼承類調用。
private: 私有類型，只有在本類中使用。
*/

//--計算進入時間 start--
list($usec, $sec) = explode(' ',microtime());
$GLOBALS["Web_Start_time"] = ((float)$usec + (float)$sec);

class html_basic{
	protected static $header_output=true;// 是否輸出header 預設true
	protected static $global_value;
	protected static $css_code="";
	protected static $js_code="";
	protected static $html_meta=array();//網頁meta自訂部分
	protected static $html_code_w3c="";//網頁w3c宣告部分
	protected static $html_code_header="";
	protected static $html_code_body="";
	protected static $html_code_content="";
	protected static $html_code_footer="";
	protected static $html_code_title="";
	protected static $js_message="";//彈跳視窗訊息文字
	protected static $js_windows_close=0;//跳完js  是否要關閉
	protected static $IP_Address;
	protected static $PHP_Code_Version='';
	protected static $UI_Code_Version='';
	protected static $Service_Mail=array();
	protected static $File_Real_Path;
	protected static $File_Include_Path;
	protected static $Web_URL;
	protected static $DebugIPAddress=array();
	protected static $DB_Data=array('db_host'=>'','user'=>'','password'=>'','charset'=>'','dbase'=>'');
	protected static $code_mode;
	protected static $Controller_Directory;
	protected static $Template_Path;
	protected static $PHP_Path;
	protected static $LOG_Path;
	protected static $IMG_Directory_Name;
	protected static $JS_Directory_Name;
	protected static $CSS_Directory_Name;
	protected static $Lang_Directory_Name;
	protected static $Admin_Directory_Name;
	protected static $Cache_Directory_Name;
	protected static $Permit_IP;
	protected static $NO_Permission_IP;
	protected static $Website_Name;
	protected static $Website_Name_Short;
	protected static $Layout_Path;
	protected static $layout_file;
	protected static $language_set='zh-tw';//設定顯示語言
	protected static $output_over=0;//是否頁面已輸出 0未輸出 ,1已輸出
	protected $internal_value=array();//存放網頁內部使用變數, 可以在本類別各區域通行
	protected static $layout_tag=array();//樣版標籤
	protected static $QueueFile=array();//載入佇列

	protected static $cacheMode=0;//content 部分做cache =1 或是整頁做cache =2 都做=3
	protected static $cacherunMode=true;//是否啟用cache true 啟動,false 不啟動
	protected static $cacheRefreshSec=1800;//cache 自動重新產生的秒數 預設1800秒(30分鐘)

	protected static $testsite_symbol;
	protected static $modeRoute_basic=array('common','admin','reception','api','cron','consume','bridge','practice','practice_api');//內建功能模組
	protected static $modeRoute_custom=array();//自訂功能模組
	protected static $WebsiteLnaguage;



	public function __get($name){
		return (isset($this->{$name}))? $this->{$name}:"";
	}

	public function __set($name,$value){
		$this->{$name}=$value;
	}

	/*取得config建立基本的變數*/
	protected static function set_basic_value($config=null){

		self::$global_value=new template_page_var;
		if(isset($config) && is_array($config)){
			self::$DB_Data['db_host']=(isset($config['db']['host']))? $config['db']['host']:"";
			self::$DB_Data['user']=(isset($config['db']['username']))? $config['db']['username']:"";
			self::$DB_Data['password']=(isset($config['db']['password']))? $config['db']['password']:"";
			self::$DB_Data['charset']=(isset($config['db']['charset']))? $config['db']['charset']:"";
			self::$DB_Data['dbase']=(isset($config['db']['dbase']))? $config['db']['dbase']:"test";

			self::$IP_Address=(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) ? $_SERVER["HTTP_X_FORWARDED_FOR"]:$_SERVER["REMOTE_ADDR"];
			self::$PHP_Code_Version=(isset($config['version']['PHP_Code_Version']))? $config['version']['PHP_Code_Version']:"";
			self::$UI_Code_Version=(isset($config['version']['UI_Code_Version']))? $config['version']['UI_Code_Version']:"";

			self::$Service_Mail=(isset($config['Service_Mail']))? $config['Service_Mail']:array("paulina@tolink.com.tw");
			self::$File_Real_Path=(isset($config['File_Real_Path']))? $config['File_Real_Path']:dirname(__FILE__);
			self::$File_Include_Path=(isset($config['File_Include_Path']))? $config['File_Include_Path']:dirname(__FILE__);
			self::$Web_URL=(isset($config['Web_Path']))? $config['Web_Path']:"http://".$_SERVER['HTTP_HOST'];
			self::$DebugIPAddress=(isset($config['DebugIPAddress']) && is_array($config['DebugIPAddress']))? $config['DebugIPAddress']:array('127.0.0.1');

			self::$Controller_Directory=(isset($config['Controller_Directory']))? self::$File_Include_Path."/".$config['Controller_Directory']:self::$File_Include_Path."/controller";
			self::$Template_Path=(isset($config['Template_Directory']))? self::$File_Include_Path."/".$config['Template_Directory']:self::$File_Include_Path."/template";
			self::$PHP_Path=(isset($config['PHP_Directory']))? self::$File_Include_Path."/".$config['PHP_Directory']:self::$File_Include_Path."/php";
			self::$Admin_Directory_Name=(isset($config['ADMIN_Directory']))? $config['ADMIN_Directory']:"admin_manager";

			self::$Permit_IP=(isset($config['Permit_IP']))? $config['Permit_IP']:array('*');
			self::$NO_Permission_IP=(isset($config['NO_Permission_IP']))? $config['NO_Permission_IP']:array('');
			self::$Website_Name=(isset($config['Website_Name']))? $config['Website_Name']:"網站";
			self::$Website_Name_Short=(isset($config['Website_Name_Short']))? $config['Website_Name_Short']:"網站";
			self::$JS_Directory_Name=(isset($config['JS_Directory']))? $config['JS_Directory']:"js";
			self::$CSS_Directory_Name=(isset($config['CSS_Directory']))? $config['CSS_Directory']:"css";
			self::$Lang_Directory_Name=(isset($config['Lang_Directory']))? $config['Lang_Directory']:"language";
			self::$IMG_Directory_Name=(isset($config['IMG_Directory']))? $config['IMG_Directory']:"images";
			self::$Cache_Directory_Name=(isset($config['cache_config']['cacheDirectory']))? $config['cache_config']['cacheDirectory']:"cache";
			self::$Layout_Path=(isset($config['Layout_Path']))? $config['Layout_Path']: self::$File_Include_Path."/views/layouts";
			self::$modeRoute_custom=(isset($config['modeRoute']['directary']))? $config['modeRoute']['directary']:array();

			self::$WebsiteLnaguage=(isset($config['WebsiteLnaguage']))? $config['WebsiteLnaguage']:'zh-TW';

			if($config['Website_Name']!=''){
				self::set_title($config['Website_Name']);
			}

			self::$LOG_Path=(isset($config['LOG_Directory']))? self::$File_Include_Path."/".$config['LOG_Directory']:self::$File_Include_Path."/log";
			self::$testsite_symbol=(isset($config['testsite_symbol']))? $config['testsite_symbol']:'testsite_symbol';

			//設定cache 變數
			if(isset($config['cache_config'])){
				self::$cacherunMode=$config['cache_config']['runMode'];
				self::$cacheRefreshSec=$config['cache_config']['refreshSec'];
				$temp=explode('|',$config['cache_config']['cacheMode']);
				if(count($temp)> 0){
					$modeNumber=0;
					foreach($temp as $cacheMode){
						if(trim($cacheMode)=='CONTENT'){
							$modeNumber+=1;
						}else if(trim($cacheMode)=='ALL'){
							$modeNumber+=2;
						}
					}
					self::$cacheMode=$modeNumber;
				}
				else{
					$config['cache_config']['cacheMode']=trim($config['cache_config']['cacheMode']);
					self::$cacheMode=($config['cache_config']['cacheMode']=='CONTENT') ? 1:(($config['cache_config']['cacheMode']=='ALL')? 2:3);
				}

			}

			return true;
		}
		return false;
	}

	/*
	public  function yyy(){
		$this->sss="1234";
	}*/

	//mysql 資料庫連線
	public static function dblink_create($db_message=array()){
		//return new Mysql_Db(self::$DB_Data['db_host'],self::$DB_Data['user'],self::$DB_Data['password'],self::$DB_Data['dbase']);//資料庫連線
		 //return new dbase_class('mysql',self::$DB_Data['db_host'],self::$DB_Data['dbase'],self::$DB_Data['user'],self::$DB_Data['password']);
			if(is_array($db_message) && isset($db_message['db_host'])){
				return new dbase_class('mysql',$db_message['db_host'],$db_message['dbase'],$db_message['user'],$db_message['password']);
			}
			else{
			return new dbase_class('mysql',self::$DB_Data['db_host'],self::$DB_Data['dbase'],self::$DB_Data['user'],self::$DB_Data['password']);
		}
	}

	//分析網址參數
	public static function get_command_line_argument(){
		/*$_SERVER["REQUEST_URI"]*/

		//網址有錯
		if(@strpos($_SERVER["REDIRECT_URL"],"$")>0){
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		//2017-03-07 防堵網址xss 攻擊
		if(isset($_SERVER["REDIRECT_URL"])){
			$_SERVER["REDIRECT_URL"]=htmlEntities($_SERVER["REDIRECT_URL"], ENT_QUOTES, "UTF-8");
		}
		$xss_key_array=array("<script","/script","<iframe","/iframe","cookie","show ","union ","select ","<",">","--","##","onmouseover","onkeyup","onkeydown","onblur","onchange","onfocus","onselect","onkeypress");
		foreach($xss_key_array as $rep_word){
			if(isset($_SERVER["REDIRECT_URL"]) && str_replace($rep_word,"",strtolower($_SERVER["REDIRECT_URL"])) !=strtolower($_SERVER["REDIRECT_URL"])){
				header("HTTP/1.0 412 Suspected Aattack");
				exit;
			}
		}

		//第一種狀況
		@$get_this_url=(substr($_SERVER["REDIRECT_URL"],0,1)=='/')? substr($_SERVER["REDIRECT_URL"],1,strlen($_SERVER["REDIRECT_URL"])):$_SERVER["REDIRECT_URL"];
		//return explode("/",$get_this_url);

		//第二種狀況
		//return explode("/",str_replace(str_replace("index.php","",$_SERVER["SCRIPT_NAME"]),"",$_SERVER["REQUEST_URI"]));
		//第三種狀況

		//$reurl=$_SERVER["REQUEST_URI"];
		$reurl=$_SERVER["REDIRECT_URL"];
		$reurl=str_replace("index.php","",$reurl);
		if(isset($_SERVER["argv"])){
		  for($i=0;$i< count($_SERVER["argv"]);$i++){
                $reurl=str_replace($_SERVER["argv"][$i],"",$reurl);
		    }  
		}
		//phpinfo();
		//echo $reurl;exit;
		$reurl=str_replace("?","",$reurl);
		$reurl=str_replace("&","",$reurl);
		return  explode("/",$reurl);

	}

	//設定php 載入版本
	public static function SetVersion_PHP($Version_Code){
		self::$PHP_Code_Version=$Version_Code;
	}

	//設定ui 載入版本
	public static function SetVersion_UI($Version_Code){
		self::$UI_Code_Version=$Version_Code;
	}

	//設定是否啟用cache
	public static function set_cache_mode($cache_mode){
		self::$cacherunMode=($cache_mode==true)? true : false;
	}

	public static function header_output_set($status=true){
		self::$header_output=$status;
	}
	public static function header_output_status(){
		return self::$header_output;
	}

	//設定網站語系設定參數
	public static function set_WebsiteLnaguage($lang){
		return self::$WebsiteLnaguage=($lang=='en')? 'en':'tw' ;
	}
    //返回網站語系設定參數
	public static function get_WebsiteLnaguage(){
		return self::$WebsiteLnaguage ;
	}
	public static function get_debug_ip(){
		return self::$DebugIPAddress;
	}

	public static function get_permit_ip(){
		return self::$Permit_IP;
	}
	public static function get_no_permission_ip(){
		return self::$NO_Permission_IP;
	}

	public static function get_user_ip(){
		return self::$IP_Address;
	}

	public static function get_include_path(){
		return self::$File_Include_Path;
	}
	public static function get_service_mail(){
		return self::$Service_Mail;
	}
	public static function get_lang_path(){
		/*取得暫存檔存放路徑*/
		return self::$File_Include_Path.'/'.self::$Lang_Directory_Name.'/';
	}

	public static function get_cache_path(){
		/*取得cache檔存放路徑*/
		return self::$File_Include_Path.'/'.self::$Cache_Directory_Name;
	}

	public static function get_cache_mode(){
		return self::$cacheMode;
	}

	public static function get_cache_runmode(){
		return self::$cacherunMode;
	}

	public static function get_cache_refreshsec(){
		return self::$cacheRefreshSec;
	}

	public static function get_www_path(){
		/*取得網站絕對路徑*/
		return self::$File_Real_Path;
	}

	public static function get_images_path(){
		/*取得圖片檔存放路徑*/
		return self::$Web_URL."/".self::$IMG_Directory_Name;
	}


	public static function get_website(){
		/*取得網站http路徑*/
		return self::$Web_URL;
	}

	public static function get_css_path(){
		/*取得CSS檔存放路徑*/
		return self::$Web_URL."/".self::$CSS_Directory_Name;
	}

	public static function get_js_path(){
		/*取得JS檔存放路徑*/
		return self::$Web_URL."/".self::$JS_Directory_Name;
	}

	public static function get_config_path(){
		/*取得設定檔存放路徑*/
		return self::$File_Include_Path."/include/config";
	}

	public static function get_class_path(){
		return self::$File_Include_Path."/include";
	}


	public static function get_modules_path(){
		return self::$File_Include_Path."/include/modules";
	}

	public static function get_website_name(){
		return self::$Website_Name;
	}

	public static function get_website_name_short(){
		return self::$Website_Name_Short;
	}

	public static function get_admin_directory_name(){
		return self::$Admin_Directory_Name;
	}

	public static function get_log_path(){
		return self::$LOG_Path."/";
	}

	public static function get_path($_pathName){
		$func_name='get_'.$_pathName.'_path';
		return self::$func_name();
	}

	public static function get_day_time(){
		return date("Y-m-d H:i:s");
	}

	//輸出設定中的controller 路徑
	public static function get_controller(){
		return self::$Controller_Directory;
	}

	//取得設定中的程式&樣板路徑
	public static function get_directory($code_mode,$d_type=''){
		//reception admin mobile
		//$view_directory=self::$code_mode;
		//echo "code_mode=".$code_mode."<br/>";
		//echo "d_type=".$d_type."<br/>";
		//讀取程式路徑

		if($code_mode=='php'){
			//內建功能模組路徑
			//print_r(self::$modeRoute_basic );
			foreach(self::$modeRoute_basic as $mode_name){
				if($d_type==$mode_name){
					if(self::$PHP_Code_Version!=''){
						return self::$PHP_Path."/".$d_type."/".self::$PHP_Code_Version;
					}
					else{
						return self::$PHP_Path."/".$d_type;
					}
				}
			}
			//自訂功能模組路徑
			foreach(self::$modeRoute_custom as $mode_name){
				if($d_type==$mode_name){
					if(self::$PHP_Code_Version!=''){
						return  self::$PHP_Path."/".$d_type."/".self::$PHP_Code_Version;
					}
					else{
						return  self::$PHP_Path."/".$d_type;
					}
				}
			}
		}
		else{
			//內建功能模組路徑
			foreach(self::$modeRoute_basic as $mode_name){
				if($d_type==$mode_name){
					if(self::$UI_Code_Version!=''){
						return self::$Template_Path."/".$d_type."/".self::$UI_Code_Version;
					}
					else{
						return self::$Template_Path."/".$d_type;
					}
				}
			}
			//自訂功能模組路徑
			foreach(self::$modeRoute_custom as $mode_name){
				if($d_type==$mode_name){
					if(self::$PHP_Code_Version!=''){
						return self::$Template_Path."/".$d_type."/".self::$UI_Code_Version;
					}
					else{
						return self::$Template_Path."/".$d_type;
					}
				}
			}
		}
	}


	/*==================
	共用函式
	==================*/

	//檢查是否為測試機
	public static function check_testsite(){
		if(preg_match('@' . self::$testsite_symbol . '@', $_SERVER['HTTP_HOST'])){
			return true;
		} else {
			return false;
		}
	}

	//判斷不允許瀏覽的IP
	public static function check_limit_ip($IP_Address,$sourceFrom=array()){
		$check_ip="";
		if(is_array($sourceFrom) && count($sourceFrom) > 0){
			if( is_array($sourceFrom)){
				foreach($sourceFrom as $check_ip){
							$tip2=strpos($check_ip,"/");
 							$check_ips=trim(substr($check_ip,0,$tip2-1));
							//-- 表示有區段
							if(strpos($check_ips,"*") > 0){
								$tip2=strpos($check_ips,"*");
								$check_ips=trim(substr($check_ips,0,$tip2-1));
								if(substr($IP_Address,0,strlen($check_ips))==$check_ips){
									return 1;
								}
							}
							else if($IP_Address==trim($check_ips)){
								return 1;
							}
				}
			}
		}
		else{
				if( is_array(self::get_no_permission_ip())){
					foreach(self::get_no_permission_ip() as $check_ip){
					//-- 表示有區段
						if(strpos($check_ip,"*") > 0){
							$tip2=strpos($check_ip,"*");
							$check_ip=trim(substr($check_ip,0,$tip2-1));
							if(substr($IP_Address,0,strlen($check_ip))==$check_ip){
								return 1;
							}
						}
						else if($IP_Address==$check_ip){
							return 1;
						}
					}
				}
		}

	}

	//判斷允許通過的IP
	public static function check_permit_ip($IP_Address,$sourceIpData=array()){
		$check_ip="";
		$sourceIP=(!isset($sourceIpData) || count($sourceIpData) <=0) ? self::get_permit_ip():$sourceIpData;
		foreach($sourceIP as $check_ip){
			//-- 表示有區段
			if(substr($check_ip,0,1)=='*'){//全開
				return 1;
			}
			else if(strpos($check_ip,"*")>0){//區段開
				$tip2=strpos($check_ip,"*");
				$check_ip=trim(substr($check_ip,0,$tip2-1));
				if(substr($IP_Address,0,strlen($check_ip))==$check_ip){
					return 1;
				}
			}
			else if(trim($IP_Address)==trim($check_ip)){
				return 1;
			}
		}

	}

	public static function check_debug_ip($IP_Address){
		$check_ip="";
		foreach(self::get_debug_ip() as $check_ip){
			//-- 表示有區段
			if(trim($IP_Address)==trim($check_ip)){
				return 1;
			}
		}
	}

	//除錯訊息
	public static function debug($message){
		if(in_array(self::$IP_Address,self::$DebugIPAddress)){
			echo "\r\n<br>=======DEBUG STAART=====\r\n<br>";
			if(is_array($message)){
				print_r($message);
			}
			else if(is_object($message)){
				var_dump($message);
			}
			else{
				echo $message;
			}
			echo "\r\n<br>=======DEBUG END========\r\n<br>";
		}
	}

	//javascript 跳轉畫面
	public static function js_go_next_page($url="",$message=""){
		header('Content-type:text/html; charset=utf-8');
		if(ob_get_length() > 0){
			ob_end_clean();//先清空畫面
		}
		if($message!=''){
			echo "<script type='text/javascript'>alert('".$message."');</script>";
		}
		if($url!=''){
			echo "<script type='text/javascript'>document.location.href=('".$url."');</script>";
			echo "\r\n<noscript><a href='".$url."'>未自動轉跳,請按這裏</a></noscript>";
		}
		exit;
	}

	public static function js_go_next_pageNOHistory($url="",$message=""){
		header('Content-type:text/html; charset=utf-8');
		if(ob_get_length() > 0){
			ob_end_clean();//先清空畫面
		}
		if($message!=''){
			echo "<script type='text/javascript'>alert('".$message."');</script>";
		}
		if($url!=''){
			echo "<script type='text/javascript'>document.location.replace('".$url."');</script>";
			echo "\r\n<noscript><a href='".$url."'>未自動轉跳,請按這裏</a></noscript>";
		}
		exit;
	}


	//php 跳轉畫面
	public static  function header_go_next_page($url=""){
		if($url!=''){
			header('Location:'.$url);
		}
		else{
			echo "no url ...";
		}
		exit;
	}

	//跳訊息
	public static  function message_box($word=""){/*適用地方為php 處理完資料要秀訊息時*/
		self::$js_message.=$word;
	}

	//內部區域變數
	public static  function set_global($name,$value){
		self::$global_value->assign($name,$value);
	}

	public static function get_global(){
		return self::$global_value;
	}

	/*在畫面上方顯示的javascript 訊息區塊設定*/
	public static function set_js_message_box(){
		$html_code="<div class='message' style='width:100%;height:50px;line-height:50px;font-size:15px;display:none;color:#E8D69F;background-color:#FF0000;text-align:center;' id='red_message'><!-- 取代alert 的訊息 --></div><div class='message' style='width:100%;height:50px;line-height:50px;font-size:15px;display:none;color:#FF0000;background-color:#FFFF00;text-align:center;' id='yellow_message'><!-- 取代alert 的訊息 --></div><SCRIPT LANGUAGE='JavaScript'>var message_tag_id='';function set_yellow_message(mword,tag_id){\$('html, body').scrollTop(0);if(tag_id==undefined || tag_id==null || tag_id==''){tag_id='red_message';}message_tag_id=tag_id;\$('#'+message_tag_id).html(mword);\$('#'+message_tag_id).show();setTimeout('call_disappear();', 15000);}\r\nfunction call_disappear(){\$('#'+message_tag_id).html('');\$('#'+message_tag_id).hide();}</script>\r\n";
		return $html_code;
	}

	//=====================================
	//設定網頁使用版型
	public static  function layout($output_html){
	//	echo $output_html."+layout";
		//echo "<br/>".self::$File_Include_Path.'/'.self::$Layout_Path.'/'.$output_html;
		if(file_exists(self::$File_Include_Path.'/'.self::$Layout_Path.'/'.$output_html)){
			self::$layout_file=self::$File_Include_Path.'/'.self::$Layout_Path.'/'.$output_html;
			//echo self::$layout_file;exit;
		}else{
			echo " lose layout file!";
			exit;
		}
	}

	//設定header 其他補強的html 補強語法
	public static  function set_header_other($code){
		self::$html_code_header.=$code;
	}

	//=====================================

	//設定文本內容
	public static  function set_content($content){
		self::$html_code_content=$content;
	}
	//設定表尾內容
	public static  function set_footer($content){
		self::$html_code_footer=($content);
	}

	//=====================================
	public static function get_value($vtype,$from_value,$value_name=""){
		//global $_POST,$_GET,$_FILES,$_REQUEST;

		$vtype=strtolower($vtype);
		${$value_name}="";
		$from_value=strtolower($from_value);//轉小寫
		if($from_value=='post'){
			$from_value=$_POST;
		}else if($from_value=='get'){
			$from_value=$_GET;
		}else{
			$from_value=$_REQUEST;
		}

		if(!isset($from_value[$value_name])&& $vtype!='file'){
			switch($vtype){
				case "int":return 0;break;
				default:return "";break;
			}
		}

		//$xss_key_array=array("<script","/script","<iframe","/iframe","alert","cookie","<",">","=","--","##");
		$xss_key_array=array("<script","/script","<iframe","/iframe","alert","cookie","show ","union ","select ","<",">","=","--","##","onmouseover","onkeyup","onkeydown","onblur","onchange","onfocus","onselect","onkeypress");

		switch($vtype){
			case "int":
				if(is_array($from_value[$value_name])){
					${$value_name}=array();
					foreach($from_value[$value_name] as $key => $value){
						/*防堵xss 攻擊*/
						if(stristr($key,'"') || stristr($key,"'") || (strip_tags($key)!= $key)){echo "f1";exit;}
							${$value_name}[$key]=abs(intval($value));
					}
				}
				else{
					if(isset($value) && is_array($value)){ // ex: aa[][]
					}
					else{
						${$value_name}=abs(intval($from_value[$value_name]));
					}

				}
			break;
			case "char":
				if(is_array($from_value[$value_name])){
					${$value_name}=array();
					foreach($from_value[$value_name] as $key => $value){
						/*防堵xss 攻擊*/
						if(stristr($key,'"') || stristr($key,"'") || (strip_tags($key)!= $key)){exit;}
						if(get_magic_quotes_gpc()){
							if(is_array($value)){
								${$value_name}[$key]=(  ($value) );
							}
							else{
								${$value_name}[$key]=trim( stripslashes ($value) );
							}
						}
						else{
							//${$value_name}[$key]=trim(mysql_real_escape_string ($value));
							if(is_array($value)){ // ex: aa[][]
								foreach($value as $key2 => $value2){

								}
							}
							else{
								${$value_name}[$key]=trim( ($value));
							}

						}
						//${$value_name}[$key]=trim(($value));
						if(${$value_name}!=''){
							foreach($xss_key_array as $rep_word){
								if(str_replace($rep_word,"",strtolower(${$value_name}[$key])) !=strtolower(${$value_name}[$key])){
									${$value_name}[$key]='';
									echo "error Code #5430";exit;
								}
								${$value_name}[$key]=str_ireplace($rep_word,"",${$value_name}[$key]);
							}
							${$value_name}[$key]=str_replace('$','&#036;', ${$value_name}[$key]);
							${$value_name}[$key]=htmlspecialchars(${$value_name}[$key]);
						}
					}
				}
				else{
					if(get_magic_quotes_gpc()){
						${$value_name}=trim(stripslashes($from_value[$value_name]));
					}
					else{
						//${$value_name}=trim(mysql_real_escape_string ($from_value[$value_name]));
						//$from_value[$value_name]=str_replace('$','\$',$from_value[$value_name]);
						${$value_name}=trim( ($from_value[$value_name]));
					}

					if(${$value_name}!=''){
						foreach($xss_key_array as $rep_word){
							if(isset(${$value_name}) && str_replace($rep_word,"",strtolower(${$value_name})) !=strtolower(${$value_name})){
								//${$value_name}='';
								//echo "error Code #5431";exit;
							}
							${$value_name}=str_ireplace($rep_word,"",${$value_name});
						}
						${$value_name}=str_replace('$','&#036;', ${$value_name});
						${$value_name}=htmlentities(${$value_name},ENT_QUOTES,"UTF-8");
						//${$value_name}=htmlspecialchars(${$value_name});
					}
				}
			break;
			case "file":
				${$value_name}=array();
				if(isset($_FILES[$value_name])){
					foreach($_FILES[$value_name] as $key => $value){
						if(stristr($key,'"') || stristr($key,"'") || (strip_tags($key)!= $key)){exit;}
						${$value_name}[$key]=$value;
					}
				}else{
					${$value_name}="";
				}
			break;
		}
		return ${$value_name};
	}

	//=====================================
	/*給後端使用,不過濾xss*/
	public static function get_value_no_filter($vtype,$from_value,$value_name=""){
		//global $_POST,$_GET,$_FILES;
		$vtype=strtolower($vtype);
		${$value_name}="";
		$from_value=strtolower($from_value);//轉小寫
		if($from_value=='post'){
			$from_value=$_POST;
		}else if($from_value=='get'){
			$from_value=$_GET;
		}else{
			$from_value=$_REQUEST;
		}
		if(!isset($from_value[$value_name])&& $vtype!='file'){return "";}
		//$xss_key_array=array("<script","/script","<iframe","/iframe","alert","cookie","<",">");
		$xss_key_array=array();

		switch($vtype){
			case "int":
				if(is_array($from_value[$value_name])){
					${$value_name}=array();
					foreach($from_value[$value_name] as $key => $value){
						/*防堵xss 攻擊*/
						if(stristr($key,'"') || stristr($key,"'") || (strip_tags($key)!= $key)){echo "f1";exit;}
						${$value_name}[$key]=(int)$value;
					}
				}
				else{
					${$value_name}=(int)$from_value[$value_name];
				}
			break;
			case "char":
				if(is_array($from_value[$value_name])){
					${$value_name}=array();
					foreach($from_value[$value_name] as $key => $value){
						/*防堵xss 攻擊*/
						if(stristr($key,'"') || stristr($key,"'") || (strip_tags($key)!= $key)){exit;}

						if(get_magic_quotes_gpc()){
							${$value_name}[$key]=trim(stripslashes($value));
						}
						else{
							//${$value_name}[$key]=trim(mysql_real_escape_string ($value));
							${$value_name}[$key]=trim( addslashes($value));
							//${$value_name}[$key]=trim( ($value));
						}

						if(${$value_name}!=''){
							foreach($xss_key_array as $rep_word){
								${$value_name}[$key]=str_replace($rep_word,"",${$value_name}[$key]);
							}
						}
					}
				}
				else{
					if(get_magic_quotes_gpc()){
						${$value_name}=trim(stripslashes($from_value[$value_name]));
					}
					else{
						//${$value_name}=trim(mysql_real_escape_string ($from_value[$value_name]));
						//${$value_name}=trim( htmlspecialchars($from_value[$value_name], ENT_QUOTES) );
						//$from_value[$value_name]=str_replace('$','&#036;',$from_value[$value_name]);
						${$value_name}=trim( addslashes($from_value[$value_name]));

						//${$value_name}=trim( ($from_value[$value_name]));
					}
					if(${$value_name}!=''){
						foreach($xss_key_array as $rep_word){
							${$value_name}=str_replace($rep_word,"",${$value_name});
						}
					}
				}
			break;
			case "file":
				${$value_name}=array();
				foreach($_FILES[$value_name] as $key => $value){
					if(stristr($key,'"') || stristr($key,"'") || (strip_tags($key)!= $key)){exit;}
					${$value_name}[$key]=$value;
				}
			break;
		}
		return ${$value_name};
	}

	//=====================================
	public static function set_meta($meta_key='',$code=""){
		if($code!=''){
			self::$html_meta[$meta_key]=$code;
		}
	}

	//=====================================
	public static function set_title($code=""){
		if($code!=''){
			//self::$html_code_title="<title>".$code."</title>";
			self::$html_code_title=$code;
		}
	}

	//=====================================
	public static  function basic_modeRoute(){
		return self::$modeRoute_basic;
	}

	//=====================================
	public static  function custom_modeRoute(){
		return self::$modeRoute_custom;
	}

	//=====================================
	public static  function set_css($type, $code=""){
		$type=strtolower($type);
		if($type=='file'){
			self::$css_code.="<link href=\"".$code."\"  media=\"screen\" rel=\"stylesheet\" type=\"text/css\" />\r\n";
		}
		else if($type=='custom'){
			self::$css_code.="".$code."\r\n";
		}
		else{
			self::$css_code.="<style type='text/css'>\r\n".$code."</style>\r\n";
		}
	}

	//=====================================
	public static  function set_js($type, $code=""){
		$type=strtolower($type);
		if($type=='file'){
			self::$js_code.="<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"".$code."\" type=\"text/javascript\" charset=\"utf-8\"></script>\r\n";
		}
		else{
			self::$js_code.="<SCRIPT LANGUAGE=\"JavaScript\" type=\"text/javascript\">\r\n".$code."</script>\r\n";
		}
	}

	//=====================================
	public static function set_body($code=""){
		if($code!=''){
			self::$html_code_body=$code;
			//畫面訊息
			self::$html_code_body.=self::set_js_message_box();
		}
	}

	//=====================================
	public static function set_language($lang=""){
		if($lang!=''){
			self::$language_set=$lang;
		}
	}

	//=====================================
	public static function changeLanguage($mess,$lan='zh-tw'){
		if($lan!='zh-tw'){
			define("MEDIAWIKI_PATH",  self::get_class_path()."/mediawiki-1.15.4/");//定義MediaWiki路徑為MEDIAWIKI_PATH常數
			require_once  self::get_class_path()."/mediawiki-zhconverter.inc.php";
			return MediaWikiZhConverter::convert($mess, "zh-cn");//轉大陸簡體
		}else{
			if(self::$IP_Address=='220.133.80.92'){
				echo $mess;exit;
			}
			return $mess;
		}
	}

	//=====================================
	public static function output(){
		/*
		根據html載入相關模板
		*/
		$layout_code='';

		//print_r(self::$QueueFile);exit;
		if(self::$output_over >0){return;}
		//header('Content-type:text/html; charset=utf-8');
		$content_mode="";
		$footer_mode="";

		//-----------------
		$content_html="";
		foreach(self::$QueueFile as $includefile){
			ob_start();
			require_once($includefile);
			$info=ob_get_contents();
			ob_end_clean();
			$content_html.=$info;
		}
		self::$html_code_content=$content_html;
		//echo self::$html_code_content."+";

		//----------------
		if(self::$header_output==true){
			$header_mode="";
			$title_mode="";
			$body_mode="";

			$header_mode.=(self::$html_code_header!='')?self::$html_code_header:"";
			if(self::$html_meta!=''){
				foreach(self::$html_meta as $meta_value){
					$header_mode.=$meta_value."\r\n";//未來要加上meta格式判斷
				}
			}
			$header_mode.=(self::$css_code!='')?self::$css_code:"";
			$header_mode.=(self::$js_code!='')?self::$js_code:"";

			$title_mode.=(self::$html_code_title!='')?self::$html_code_title:"";
			$body_mode.=(self::$html_code_body!='')?self::$html_code_body:"<body>";



			if(file_exists(self::$layout_file)){
				$layout_code=file_get_contents(self::$layout_file);
				if($header_mode!=''){
					$layout_code=preg_replace("/<(layout_mode:header_meta.*?)>(.*?)<(\/layout_mode:header_meta.*?)>/si",$header_mode,$layout_code);
				}
				if($title_mode!=''){
					$layout_code=preg_replace("/<(layout_mode:title.*?)>(.*?)<(\/layout_mode:title.*?)>/si",$title_mode,$layout_code);
				}
				if($title_mode!=''){
					$layout_code=preg_replace("/<(layout_mode:header_title.*?)>(.*?)<(\/layout_mode:header_title.*?)>/si",$title_mode,$layout_code);
				}
				if($body_mode!=''){
					$layout_code=preg_replace("/<(layout_mode:body.*?)>(.*?)<(\/layout_mode:body.*?)>/si",$body_mode,$layout_code);
				}


				//echo $layout_code."+";
				//print_r(self::$layout_tag );
				foreach(self::$layout_tag as $index => $layout_data){
					//$temp_code=self::layout_injection($layout_data[0],$layout_data[1]);
					//--$layout_tag_name,$php_file_name
					$temp_code='';
					ob_start();
					//echo html_class::get_global()->class."==".html_class::get_admin_directory_name();exit;
					$directary_type=(html_class::get_global()->class==html_class::get_admin_directory_name())? 'admin':html_class::get_global()->class;
					$php_file_name=$layout_data[1];
					if($php_file_name!=''){
						if(in_array($directary_type,html_class::basic_modeRoute()) || in_array($directary_type,html_class::custom_modeRoute()) ){
							$php_file_path=html_class::get_directory('php',$directary_type).'/_'.$php_file_name;
						}else{
							$php_file_path=html_class::get_directory('php','reception').'/_'.$php_file_name;
						}
						if(file_exists($php_file_path)){
							require_once($php_file_path);
						}
						//-----------
						if(in_array($directary_type,html_class::basic_modeRoute()) || in_array($directary_type,html_class::custom_modeRoute()) ){
							$ui_file_path=html_class::get_directory('ui',$directary_type).'/'.$php_file_name;
						}else{
							$ui_file_path=html_class::get_directory('ui','reception').'/'.$php_file_name;
						}

						if(file_exists($ui_file_path)){
							require_once($ui_file_path);
						}
					}
					$temp_code=ob_get_contents();
					ob_end_clean();
					//--
					if($temp_code!=''){
						//2017-06-05 新增轉換$號,因為經過 preg_replace $就會變成變數 所以這邊先轉換
						//$temp_code=str_replace('$','&#036;',$temp_code);
						$layout_code=preg_replace("/<(layout_mode:".$layout_data[0].".*?)>(.*?)<(\/layout_mode:".$layout_data[0].".*?)>/si",$temp_code,$layout_code);
					}
				}
			}
			else{
				//$layout_code="lose template view html";
			}
		}else{
			echo self::$html_code_content;
		}


		$content_mode.=(self::$html_code_content!='')?self::$html_code_content:"";

		//2017-06-05 新增轉換$號,因為經過 preg_replace $就會變成變數 所以這邊先轉換
		//$content_mode=str_replace('$','&#036;',$content_mode);

		if(self::$header_output=='true'){
			$footer_mode.=(self::$html_code_footer!='')?self::$html_code_footer:"";
			if(self::$js_message!=""){
				$layout_code.= "<script type=\"text/javascript\">";
				$layout_code.= "set_yellow_message(\"".self::$js_message."\");";
				$layout_code.= "";
				$layout_code.= "</script>";
			}
		}

		if($content_mode!=''){
			$layout_code=preg_replace("/<(layout_mode:content.*?)>(.*?)<(\/layout_mode:content.*?)>/si",$content_mode,$layout_code);
			$layout_code=preg_replace("/<(layout_mode:content_home.*?)>(.*?)<(\/layout_mode:content_home.*?)>/si",$content_mode,$layout_code);
		}


		if(self::$language_set!="zh-tw"){
			echo self::changeLanguage($layout_code,self::$language_set);
		}else{
			echo $layout_code;
		}

		/*
		條件 不屬於後端程式 &&
		cache mode 有啟動 &&
		樣板檔有設定
		則進行cache 儲存判斷*/

		if(html_class::get_global()->class=='consume'){
			$dir_name="/home/2017nova/log/page_html/".date("Y-m-d");
			if(!file_exists($dir_name)){
				@mkdir($dir_name);
			}
			$fine_name=str_replace("/","_",((isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]!="/")? $_SERVER["REQUEST_URI"]:"index"));
			file_put_contents($dir_name."/".$fine_name.date("mdHis").".html",$layout_code, FILE_APPEND | LOCK_EX);
			@chmod($cache_file,0755);
		}
		if(html_class::get_global()->class!=html_class::get_admin_directory_name() && html_class::get_cache_runmode() && (html_class::get_cache_mode()==2 || html_class::get_cache_mode()==3)){
			//cahce 檔案存在並且重新生成時間到了 或是檔案不存在 都進行建立動作
			if (!file_exists($cache_file) || (file_exists($cache_file) && time()-fileatime($cache_file)> html_class::get_cache_refreshsec()) ){
				file_put_contents($cache_file,$layout_code, FILE_APPEND | LOCK_EX);
				@chmod($cache_file,0755);
			}
		}
		self::$output_over=1;
	}

	//=====================================
	//標簽注入
	public static function layout_injection_join($layout_tag_name,$php_file_name){
		array_push(self::$layout_tag,array($layout_tag_name,$php_file_name));
	}


	public static function layout_injection($layout_tag_name,$php_file_name){
		ob_start();
		$directary_type=(html_class::get_global()->class==html_class::get_admin_directory_name())? 'admin':html_class::get_global()->class;

		if($php_file_name!=''){
			if(in_array($directary_type,html_class::basic_modeRoute()) || in_array($directary_type,html_class::custom_modeRoute()) ){
				$php_file_path=html_class::get_directory('php',$directary_type).'/_'.$php_file_name;
			}else{
				$php_file_path=html_class::get_directory('php','reception').'/_'.$php_file_name;
			}
			if(file_exists($php_file_path)){
				require_once($php_file_path);
			}
			//-----------
			if(in_array($directary_type,html_class::basic_modeRoute()) || in_array($directary_type,html_class::custom_modeRoute()) ){
				$ui_file_path=html_class::get_directory('ui',$directary_type).'/'.$php_file_name;
			}else{
				$ui_file_path=html_class::get_directory('ui','reception').'/'.$php_file_name;
			}

			if(file_exists($ui_file_path)){
				require_once($ui_file_path);
			}
		}

		$info=ob_get_contents();

		ob_end_clean();
		return $info;
	}
}

class html_class extends html_basic{
	protected $str;
	private static $_app;

	public function __construct(){
		/*物件生成時的執行*/
	}

	public function __destruct(){
		/*物件消滅時的執行*/
	}

	public function __get($name){
		//echo "get function para: $name  ";
		return (isset($this->{$name}))?$this->{$name}:"";
	}

	public	function __set($name,$value){
		//echo "set function para: name=$name,value=$value  ";
		$this->{$name}=$value;
	}

	//載入佇列 在pageout 才會一次載入
	static function codefile_inject($fname=""){
		if(file_exists($fname)){
			array_push(self::$QueueFile,$fname);
		}
	}


	public static function createWebApplication($config=''){

		$config=require($config);
		parent::set_basic_value($config);

		//如果再不允許瀏覽群組裡,則離開
		if(parent::check_limit_ip(parent::get_user_ip())){
			header("HTTP/1.0 404 Not Found");exit;
		}
		if(!parent::check_permit_ip(parent::get_user_ip())){
			header("HTTP/1.0 404 Not Found");exit;
		}
		//ddos 黑名單
		$blackIPFile=parent::get_include_path()."include/variable/black_ip.php";
		if(file_exists($blackIPFile)){
			 $blackIPData=file($blackIPFile);
			if(parent::check_limit_ip(parent::get_user_ip(),$blackIPData)){
				header("HTTP/1.0 404 Not Found");exit;
			}
		}



		//建立頁面模組

		return self::createApplication('tolink_WebApplication',$config);
	}

	public static function set_mode($mode_type=null){
		parent::$code_mode=$mode_type;
	}
	public static function createApplication($class,$config=null){
		return new $class($config);
	}

	/*
	判斷來自於電腦或是手機 返回值1=手機
	*/
	public static function check_mobile_from(){
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$useragent=$_SERVER['HTTP_USER_AGENT'];
			if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile|o2|opera m(ob|in)i|palm( os)?|p(ixi|re)\/|plucker|pocket|psp|smartphone|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce; (iemobile|ppc)|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
				$mobile_browser=1;
			}
			else{
				$mobile_browser=0;
			}
			$return_status=array();
			$return_status['status']=$mobile_browser;
			if(stristr($_SERVER['HTTP_USER_AGENT'],'Android') ){
				$return_status['type']='Android';
			}
			else if(stristr($_SERVER['HTTP_USER_AGENT'],'iPhone') ){
				$return_status['type']='iphone';
			}
			else{
				$return_status['type']='pc';
			}
		}else{
			$return_status['type']='unknow';
		}

		return $return_status;
	}

	/*遇有連結的內容自動+上網址的函數*/
	public function parseURL($strURL = null){
		$regex = "{ ((https?|telnet|gopher|file|wais|ftp):[\\w/\\#~:.?+=&%@!\\-]+?)(?=[.:?\\-]*(?:[^\\w/\\#~:.?+=&%@!\\-]|$)) }x";
		return preg_replace($regex,"<a href=\"$1\" target=\"_blank\" alt=\"$1\" title=\"$1\" class='blue' >$1</a>",$strURL);
	}

	/*
	判斷來是否來自於搜尋引擎
	*/
	public static function robot_check(){
		if(preg_match("/(Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla)/i", $_SERVER['HTTP_USER_AGENT'])) {
			$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
			if (strpos($useragent, 'googlebot') !== false){
				return 'Googlebot';
			}
			if (strpos($useragent, 'msnbot') !== false){
				return 'MSNbot';
			}
			if (strpos($useragent, 'slurp') !== false){
				return 'Yahoobot';
			}
			if (strpos($useragent, 'baiduspider') !== false){
				return 'Baiduspider';
			}
			if (strpos($useragent, 'sohu-search') !== false){
				return 'Sohubot';
			}
			if (strpos($useragent, 'lycos') !== false){
				return 'Lycos';
			}
			if (strpos($useragent, 'robozilla') !== false){
				return 'Robozilla';
			}
		}
		return 'Unknown';
	}

	/*
	計算讀取花費時間
	*/
	public static function web_query_log($log_type='time',$message_word='',$directory=''){

		$log_DIR=html_class::get_log_path()."/";

		if($directory!='' && !file_exists($log_DIR.$directory )){
			@mkdir(html_class::get_log_path()."/".$directory);
			@chmod(html_class::get_log_path()."/".$directory,0777);
			$log_DIR=html_class::get_log_path()."/".$directory."/";
		}else if($directory!=''){
			$log_DIR=html_class::get_log_path()."/".$directory."/";
		}

		if(!file_exists($log_DIR.date('Y_m_d') )){
			@mkdir($log_DIR.date('Y_m_d'));
			@chmod($log_DIR.date('Y_m_d'),0777);
			$log_DIR=$log_DIR.date('Y_m_d')."/";
		}else{
			$log_DIR=$log_DIR.date('Y_m_d')."/";
		}

		switch($log_type){
			case 'time':
				$fp=fopen($log_DIR.date('Y_m_d')."_web_query_time.txt","a+");
				list($usec, $sec) = explode(' ',microtime());	$querytime_after = ((float)$usec + (float)$sec);
				$get_sec= sprintf("%01.3f",$querytime_after - $GLOBALS["Web_Start_time"]);
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word." ".$get_sec."\r\n");
			break;
			case 'message':
				$fp=fopen($log_DIR.date('Y_m_d')."_message.txt","a+");
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word."\r\n");
			break;
			case 'searchbot':
				$fp=fopen($log_DIR.date('Y_m_d')."_search_bot.txt","a+");
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word."\r\n");
			break;
			case 'verify':
				$fp=fopen($log_DIR.date('Y_m_d')."_verify_key.txt","a+");
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word."\r\n");
			break;
			case 'dbase':
				$fp=fopen($log_DIR.date('Y_m_d')."_dbase.txt","a+");
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word."\r\n");
			break;
			case 'other':
				$fp=fopen($log_DIR.date('Y_m_d')."_other.txt","a+");
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word."\r\n");
			break;
			default:
				$fp=fopen($log_DIR.date('Y_m_d')."_".$log_type.".txt","a+");
				fputs($fp,html_class::get_day_time()." | ".html_class::get_user_ip()." | ".$message_word."\r\n");
			break;
		}
		fclose($fp);
	}

	public static function pageOutput($php_file_name,$template_file_name){
		$php_file_path='';
		$ui_file_path='';
		$directary_type=(html_class::get_global()->class==html_class::get_admin_directory_name())? 'admin':html_class::get_global()->class;

		if($php_file_name!=''){
			if(in_array($directary_type,html_class::basic_modeRoute()) || in_array($directary_type,html_class::custom_modeRoute()) ){
				$php_file_path=html_class::get_directory('php',$directary_type).'/_'.$php_file_name;
			}else{
				$php_file_path=html_class::get_directory('php','reception').'/_'.$php_file_name;
			}
			if(file_exists($php_file_path)){
				array_push(self::$QueueFile,$php_file_path);
			}
		}

		if($template_file_name!=''){
			//print_r(html_class::basic_modeRoute());
			//print_r(html_class::custom_modeRoute());
			if(in_array($directary_type,html_class::basic_modeRoute()) || in_array($directary_type,html_class::custom_modeRoute()) ){
				$ui_file_path=html_class::get_directory('ui',$directary_type).'/'.$template_file_name;
			}
			else{
				$ui_file_path=html_class::get_directory('ui','reception').'/'.$template_file_name;
			}

			if(file_exists($ui_file_path)){
				array_push(self::$QueueFile,$ui_file_path);
			}
		}
		//html_class::output();
	}

	public static function sendMail($mailTo, $mailTitle, $mailContent){

		mb_internal_encoding('UTF-8');

		$title = '=?utf-8?B?' . base64_encode($mailTitle).'?=';
		$content = '';

		$layout_file = self::$File_Include_Path.self::$Layout_Path.'/mail.html';
		$layout_file = str_replace('//', '/', $layout_file);
		if(file_exists($layout_file)){
			$content = file_get_contents($layout_file);
			$content = preg_replace("/<(layout_mode:mail_content.*?)>(.*?)<(\/layout_mode:mail_content.*?)>/si",$mailContent,$content);
		} else {
			$content = nl2br($mailContent);
		}

		$service_email = html_class::get_service_mail()[0];
		$from  = "From: =?utf-8?B?".base64_encode(html_class::get_website_name_short())."?= <".$service_emiail.">";
		$from .= "\nReply-To: ".$service_email;
		$from .= "\nContent-Type: text/html; charset=utf-8";

		mail($mailTo, $title, $content, $from);
	}

    public static function _t($word){
        $lang_word=$word;
        if(file_exists(self::get_lang_path().'lang_txt.php')){
           $lang=include(self::get_lang_path().'lang_txt.php');
           $webuselang=self::get_WebsiteLnaguage();
            if(isset($lang[$webuselang][$word]) && trim($lang[$webuselang][$word])!=''){
                $lang_word=$lang[$webuselang][$word];
            }
        }
        return $lang_word;
    }

}

//
class tolink_WebApplication{
	public function run(){
		//切割網址參數到指定變數
		$args=html_class::get_command_line_argument();
		$arg_start_index=1;//指定從哪一個參數開始切
		foreach($args as $args_index => $args_v){
			$ex_name='.'.substr(strrchr($args[$args_index],"." ),1);
			$args[$args_index]=str_replace($ex_name,'',$args[$args_index]);
		}

		html_class::set_global('class','');
		html_class::set_global('func','');
		html_class::set_global('work','');
		html_class::set_global('subwork','');

		if(isset($args[ ($arg_start_index) ])){
			html_class::set_global('class',$args[($arg_start_index)]);
		}
		if(isset($args[($arg_start_index+1)])){
			html_class::set_global('func',$args[($arg_start_index+1)]);
		}
		if(isset($args[($arg_start_index+2)])){
			html_class::set_global('work',$args[($arg_start_index+2)]);
		}
		if(isset($args[($arg_start_index+3)])){
			html_class::set_global('subwork',$args[($arg_start_index+3)]);
		}

		$mobile_check=html_class::check_mobile_from();

		switch(html_class::get_global()->class){
			case html_class::get_admin_directory_name():
				$controller_file=html_class::get_admin_directory_name();
			break;
			case 'api':
				$controller_file="API";
			break;
			case 'cron':
				$controller_file="Cron";
			break;
			case 'bridge':
				$controller_file="Bridge";
			break;
			case 'consume':
				$controller_file="Consume";
			break;
			case 'practice_api':
				$controller_file="practiceAPI";
			break;

			default:
				if(in_array(html_class::get_global()->class,html_class::basic_modeRoute()) || in_array(html_class::get_global()->class,html_class::custom_modeRoute()) ){
					$controller_file=html_class::get_global()->class;
				}else{
					$controller_file="default";
				}
		}

		$controller_file_path=html_class::get_controller().'/'.$controller_file.'Controller.php';
		if(file_exists($controller_file_path)){
			require_once($controller_file_path);
		}
	}
}


//判斷頁面權限
function checkPermission($perm_id,$perm_array){
	if($perm_id!='' && is_array($perm_array)){
		if(!in_array('ALL',$perm_array) && !in_array($perm_id,$perm_array)){
			if(ob_get_length() > 0){
				ob_end_clean();
			}
			html_class::js_go_next_page(html_class::get_website().'/'.html_class::get_admin_directory_name(),'您沒有權限#1');
			exit;
		}
	}
	else{
		if(ob_get_length() > 0){
			ob_end_clean();
		}
		html_class::js_go_next_page(html_class::get_website().'/'.html_class::get_admin_directory_name(),'您沒有權限#2');
		exit;
	}
}
?>