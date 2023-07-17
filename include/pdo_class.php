<?php
/*
	2014 新版資料庫連線 使用pdo from bruce

	啟動資料庫和讀取資料
	宣告資料庫物件
	Example:
	初始化
	$MDb= new dbase_class(資料庫種類,資料庫位置,預設連線資料庫,使用者名稱,使用者密碼,其他選項);
	查詢
	$MDb->dbquery("SELECT * FROM xxx where x=1 and y=2",1);
	$MDb->dbquery("SELECT * FROM xxx where x=3 and y=4",2);
	新增
	$MDb->dbquery("insert into xxx set x=1,y=2");
	指定查詢


	更新
	$MDb->dbquery("update xxx set x=1,y=2");
	刪除
	$MDb->dbquery("delete from  xxx where x=1 and y=2");
	關閉
	$MDb->dbclose();

	query_exec($SQL,$Parameter)
	$MDb->query_exec('update db set zh_CN= :str where SN=:SN',array(':SN'=>$SN));

*/


class dbase_class extends PDO{
	public static $db_link_obj;//連線物件
	public static $error_mode=1;//是否顯示錯誤訊息 1顯示 0不秀
	public static $db_expire_time;//查詢結束時間

	private static $rows_number;//結果選擇器查詢總筆數
	private static $result_answer=array();//結果選擇器

	private static $last_insert_id=0;
	//--------------------

	public function __get($name){
		return (isset($this->{$name}))? $this->{$name}:"";
	}

	public function __set($name,$value){
		$this->{$name}=$value;
	}

	public static function exception_handler($exception) {
		// Output the exception details
		die('Uncaught exception: '. $exception->getMessage());
	}

	//--------------------

	//選告成立即進行資料庫連線
	public function __construct($dbtype,$host,$dbname, $username='', $password='', $driver_options=array()) {

		//處理所有錯誤訊息
		set_exception_handler(array(__CLASS__, 'exception_handler'));

		//根據資料庫類型指定連線
		$dsn='';
		switch($dbtype){
			case 'mysql' :$dsn='mysql:host='.$host.';port=3306;dbname='.$dbname;break;
			case 'mssql' :$dsn='mysql:host='.$host.';port=1433;dbname='.$dbname;break;
			case 'oracle':$dsn='mysql:host='.$host.';port=1521;dbname='.$dbname;break;
			case 'sqlite':$dsn='sqlite:'.$host;break;
			break;
		}
		self::$db_link_obj=parent::__construct($dsn, $username, $password, $driver_options);
		//echo self::$db_link_obj."+";
		restore_exception_handler();

	}

	//資料庫斷線
	public function dbclose(){
		self::$db_link_obj=NULL;
	}

	//資料庫查詢dbquery(語法,回傳選擇器1~6)
	public function dbquery($SQLM){

		$SQLM=trim($SQLM);
		//	echo $SQLM."pp";
		$fp=fopen(dirname(__FILE__).'/../log/db/'.date('Y-m-d').'.sql','a+');
		if(isset($_SESSION['NYCU_Login_User_Message']['ID'])){
			fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  ".$_SESSION['NYCU_Login_User_Message']['ID']."  ". $SQLM ."\r\n");
		}
		else{
			fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  other  ". $SQLM ."\r\n");
		}
		fclose($fp);

		self::exec("set names 'utf8';");
		self::setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);

		//檢查語法
		$check_sql=substr(strtolower($SQLM),0,6);

		switch($check_sql){
			case 'insert':
			case 'delete':
			case 'update':
			case 'optimi'://OPTIMIZE
				$result=self::exec($SQLM);
				if($result>0){//執行成功
					if($check_sql=='insert'){
						self::$last_insert_id=self::lastInsertId();
						$fp=fopen(dirname(__FILE__).'/../log/db/'.date('Y-m-d').'.sql','a+');
						if(isset($_SESSION['NYCU_Login_User_Message']['ID'])){
							fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  ".$_SESSION['NYCU_Login_User_Message']['ID']."  insert ID:". self::lastInsertId() .self::$last_insert_id."\r\n");
						}else{
							fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  other  insert ID:". self::lastInsertId() ."\r\n");
						}
						fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  result:". $result ."\r\n");
						fclose($fp);
					}
					return true;
				}else{
					return false;
				}
				break;
			default:
				if(!self::$db_link_obj){

				}
				//進行撈取
				$result=self::query($SQLM);

				if (self::errorCode() != 00000){
					echo "error:";
					print_r(self::errorInfo());
				}else{
					try{
						@$result->setFetchMode(PDO::FETCH_OBJ);
						$result_arr=$result->fetchAll();
						//給予陣列
						self::$result_answer=$result_arr;
						//獲取執行總筆數
						self::$rows_number=$result->rowCount();
					} catch (PDOException $e) {
						print_r($e->errorInfo);
					}
				}

				if($result->fetchColumn()){//執行成功
					return true;
				}
				else{
					return false;
				}
				break;
		}
	}

	//資料庫查詢dbquery(語法,欄位值)
	public function query_exec($SQLM,$Parameter){
		$SQLM=trim($SQLM);
		$fp=fopen(dirname(__FILE__).'/../log/db/'.date('Y-m-d').'.sql','a+');
		if(isset($_SESSION['NYCU_Login_User_Message']['ID'])){
			fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  ".$_SESSION['NYCU_Login_User_Message']['ID']."  ". $SQLM ."\r\n");
		}
		else{
			fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  other  ". $SQLM ."\r\n");
		}
		fputs($fp,"\r\n ". serialize($Parameter) ."\r\n");
		fclose($fp);

		self::exec("set names 'utf8';");
		self::setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
		self::setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		//檢查語法
		$check_sql=substr(strtolower($SQLM),0,6);

		switch($check_sql){
			case 'optimi'://OPTIMIZE
				$result=self::exec($SQLM);

				if (self::errorCode() != 00000){
					echo "error:";
					print_r(self::errorInfo());
				}
				$fp=fopen(dirname(__FILE__).'/../log/db/'.date('Y-m-d').'.sql','a+');
				fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  result2:". $result ."\r\n");
				fclose($fp);
				if($result>0){//執行成功
					return true;
				}else{

					return false;
				}
				break;
			default:
				//進行撈取
				/*
				query_exec($SQL,$Parameter)
				$MDb->query_exec('update db set zh_CN= :str where SN=:SN',array(':SN'=>$SN));
				*/
				$result = self::prepare($SQLM);

				$check_field_value=array_keys($Parameter);
				if(strpbrk($check_field_value[0],":")!=''){
					foreach($Parameter as $field_key => $field_value){
						//$result->bindParam($field_key,$field_value);
						try{
							//echo $field_key."--".$field_value."<br>";
							$result->bindValue($field_key,$field_value);
						}
						catch(exception $e){
							echo $e."++";exit;
						}
					}
					$result->execute();
				}else{
					$result->execute($Parameter);
				}

				if($check_sql=='insert'){
					self::$last_insert_id=self::lastInsertId();
					$fp=fopen(dirname(__FILE__).'/../log/db/'.date('Y-m-d').'.sql','a+');
					if(isset($_SESSION['NYCU_Login_User_Message']['ID'])){
						fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  ".$_SESSION['NYCU_Login_User_Message']['ID']."  insert ID:". self::lastInsertId() .self::errorCode()."\r\n");

					}else{
						fputs($fp,"\r\n ".date('Y-m-d H:i:s')."  other  insert ID:". self::lastInsertId() ."\r\n");
					}
					fclose($fp);
				}


				if (self::errorCode() != 00000){
					echo "error:";
					print_r(self::errorInfo());
				}else{
					$result->setFetchMode(PDO::FETCH_OBJ);
					$result_arr=$result->fetchAll();
					//給予陣列
					self::$result_answer=$result_arr;
					//獲取執行總筆數
					self::$rows_number=$result->rowCount();
				}


				if($result->fetchColumn()){//執行成功

					return true;
				}
				else{
					return false;
				}
				break;
		}
	}

	public static function get_result(){
		return self::$result_answer;
	}

	public static function get_rowsNumber(){
		return self::$rows_number;
	}
}

/*
PDO 參數詳解
-----------------------------------------------
PDO::PARAM_BOOL ( integer )
表示布爾數據類型。
PDO::PARAM_NULL ( integer )
表示SQL 中的NULL 數據類型。
PDO::PARAM_INT ( integer )
表示SQL 中的整型。
PDO::PARAM_STR ( integer )
表示SQL 中的CHAR、public static CHAR 或其他字符串類型。
PDO::PARAM_LOB ( integer )
表示SQL 中大對像數據類型。
PDO::PARAM_STMT ( integer )
表示一個記錄集類型。當前尚未被任何驅動支持。
PDO::PARAM_INPUT_OUTPUT ( integer )
指定參數為一個存儲過程的INOUT 參數。必須用一個明確的PDO::PARAM_* 數據類型跟此值進行按位或。
PDO::FETCH_LAZY ( integer )
指定獲取方式，將結果集中的每一行作為一個對象返回，此對象的變量名對應著列名。PDO::FETCH_LAZY創建用來訪問的對像變量名。在PDOStatement::fetchAll()中無效。
PDO::FETCH_ASSOC ( integer )
指定獲取方式，將對應結果集中的每一行作為一個由列名索引的數組返回。如果結果集中包含多個名稱相同的列，則PDO::FETCH_ASSOC每個列名只返回一個值。
PDO::FETCH_NAMED ( integer )
指定獲取方式，將對應結果集中的每一行作為一個由列名索引的數組返回。如果結果集中包含多個名稱相同的列，則PDO::FETCH_ASSOC每個列名返回一個包含值的數組。
PDO::FETCH_NUM ( integer )
指定獲取方式，將對應結果集中的每一行作為一個由列號索引的數組返回，從第0 列開始。
PDO::FETCH_BOTH ( integer )
指定獲取方式，將對應結果集中的每一行作為一個由列號和列名索引的數組返回，從第0 列開始。
PDO::FETCH_OBJ ( integer )
指定獲取方式，將結果集中的每一行作為一個屬性名對應列名的對象返回。
PDO::FETCH_BOUND ( integer )
指定獲取方式，返回TRUE且將結果集中的列值分配給通過PDOStatement::bindParam()或PDOStatement::bindColumn()方法綁定的PHP變量。
PDO::FETCH_COLUMN ( integer )
指定獲取方式，從結果集中的下一行返回所需要的那一列。
PDO::FETCH_CLASS ( integer )
指定獲取方式，返回一個所請求類的新實例，映射列到類中對應的屬性名。
Note : 如果所請求的類中不存在該屬性，則調用__set()魔術方法
PDO::FETCH_INTO ( integer )
指定獲取方式，更新一個請求類的現有實例，映射列到類中對應的屬性名。
PDO::FETCH_FUNC ( integer )
允許在運行中完全用自定義的方式處理數據。（僅在PDOStatement::fetchAll()中有效）。
PDO::FETCH_GROUP ( integer )
根據值分組返回。通常和PDO::FETCH_COLUMN或 PDO::FETCH_KEY_PAIR一起使用。
PDO::FETCH_UNIQUE ( integer )
只取唯一值。
PDO::FETCH_KEY_PAIR ( integer )
獲取一個有兩列的結果集到一個數組，其中第一列為鍵名，第二列為值。自PHP 5.2.3 起可用。
PDO::FETCH_CLASSTYPE ( integer )
根據第一列的值確定類名。
PDO::FETCH_SERIALIZE ( integer )
類似PDO::FETCH_INTO，但是以一個序列化的字符串表示對象。自PHP 5.1.0起可用。從PHP 5.3.0開始，如果設置此標誌，則類的構造函數從不會被調用。
PDO::FETCH_PROPS_LATE ( integer )
設置屬性前調用構造函數。自PHP 5.2.0 起可用。
PDO::ATTR_AUTOCOMMIT ( integer )
如果此值為FALSE，PDO將試圖禁用自動提交以便數據庫連接開始一個事務。
PDO::ATTR_PREFETCH ( integer )
設置預取大小來為​​你的應用平衡速度和內存使用。並非所有的數據庫/驅動組合都支持設置預取大小。較大的預取大小導致性能提高的同時也會佔用更多的內存。
PDO::ATTR_TIMEOUT ( integer )
設置連接數據庫的超時秒數。
PDO::ATTR_ERRMODE ( integer )
關於此屬性的更多信息請參見錯誤及錯誤處理部分。
PDO::ATTR_SERVER_VERSION ( integer )
此為只讀屬性；返回PDO 所連接的數據庫服務的版本信息。
PDO::ATTR_CLIENT_VERSION ( integer )
此為只讀屬性；返回PDO 驅動所用客戶端庫的版本信息。
PDO::ATTR_SERVER_INFO ( integer )
此為只讀屬性。返回一些關於PDO 所連接的數據庫服務的元信息。
PDO::ATTR_CONNECTION_STATUS ( integer )
PDO::ATTR_CASE ( integer )
用類似PDO::CASE_*的常量強制列名為指定的大小寫。
PDO::ATTR_CURSOR_NAME ( integer )
獲取或設置使用游標的名稱。當使用可滾動游標和定位更新時候非常有用。
PDO::ATTR_CURSOR ( integer )
選擇游標類型。PDO當前支持PDO::CURSOR_FWDONLY和 PDO::CURSOR_SCROLL。一般為PDO::CURSOR_FWDONLY，除非確實需要一個可滾動游標。
PDO::ATTR_DRIVER_NAME ( string )
返回驅動名稱。
Example #1使用PDO::ATTR_DRIVER_NAME的例子
<?php
if ( $db -> getAttribute ( PDO :: ATTR_DRIVER_NAME ) ==  'mysql' ) {
  echo  "Running on mysql; doing something mysql specific here\n" ;
}
?>
PDO::ATTR_ORACLE_NULLS ( integer )
在獲取數據時將空字符串轉換成SQL 中的NULL 。
PDO::ATTR_PERSISTENT ( integer )
請求一個持久連接，而非創建一個新連接。關於此屬性的更多信息請參見連接與連接管理。
PDO::ATTR_STATEMENT_CLASS ( integer )
PDO::ATTR_FETCH_CATALOG_NAMES ( integer )
將包含的目錄名添加到結果集中的每個列名前面。目錄名和列名由一個小數點分開（.）。此屬性在驅動層面支持，所以有些驅動可能不支持此屬性。
PDO::ATTR_FETCH_TABLE_NAMES ( integer )
將包含的表名添加到結果集中的每個列名前面。表名和列名由一個小數點分開（.）。此屬性在驅動層面支持，所以有些驅動可能不支持此屬性。
PDO::ATTR_STRINGIFY_FETCHES ( integer )
PDO::ATTR_MAX_COLUMN_LEN ( integer )
PDO::ATTR_DEFAULT_FETCH_MODE ( integer )
自PHP 5.2.0 起可用。
PDO::ATTR_EMULATE_PREPARES ( integer )
自PHP 5.1.3 起可用。
PDO::ERRMODE_SILENT ( integer )
如果發生錯誤，則不顯示錯誤或異常。希望開發人員顯式地檢查錯誤。此為默認模式。關於此屬性的更多信息請參見錯誤與錯誤處理。
PDO::ERRMODE_WARNING ( integer )
如果發生錯誤，則顯示一個PHP E_WARNING消息。關於此屬性的更多信息請參見錯誤與錯誤處理。
PDO::ERRMODE_EXCEPTION ( integer )
如果發生錯誤，則拋出一個PDOException 異常。關於此屬性的更多信息請參見錯誤與錯誤處理。
PDO::CASE_NATURAL ( integer )
保留數據庫驅動返回的列名。
PDO::CASE_LOWER ( integer )
強制列名小寫。
PDO::CASE_UPPER ( integer )
強制列名大寫。
PDO::NULL_NATURAL ( integer )
PDO::NULL_EMPTY_STRING ( integer )
PDO::NULL_TO_STRING ( integer )
PDO::FETCH_ORI_NEXT ( integer )
在結果集中獲取下一行。僅對可滾動游標有效。
PDO::FETCH_ORI_PRIOR ( integer )
在結果集中獲取上一行。僅對可滾動游標有效。
PDO::FETCH_ORI_FIRST ( integer )
在結果集中獲取第一行。僅對可滾動游標有效。
PDO::FETCH_ORI_LAST ( integer )
在結果集中獲取最後一行。僅對可滾動游標有效。
PDO::FETCH_ORI_ABS ( integer )
根據行號從結果集中獲取需要的行。僅對可滾動游標有效。
PDO::FETCH_ORI_REL ( integer )
根據當前游標位置的相對位置從結果集中獲取需要的行。僅對可滾動游標有效。
PDO::CURSOR_FWDONLY ( integer )
創建一個只進游標的PDOStatement對象。此為默認的游標選項，因為此游標最快且是PHP中最常用的數據訪問模式。
PDO::CURSOR_SCROLL ( integer )
創建一個可滾動游標的PDOStatement對象。通過PDO::FETCH_ORI_*常量來控制結果集中獲取的行。
PDO::ERR_NONE ( string )
對應SQLSTATE '00000'，表示SQL語句沒有錯誤或警告地成功發出。當用PDO::errorCode()或 PDOStatement::errorCode()來確定是否有錯誤發生時，此常量非常方便。在檢查上述方法返回的錯誤狀態代碼時，會經常用到。
PDO::PARAM_EVT_ALLOC ( integer )
分配事件
PDO::PARAM_EVT_FREE ( integer )
解除分配事件
PDO::PARAM_EVT_EXEC_PRE ( integer )
執行一條預處理語句之前觸發事件。
PDO::PARAM_EVT_EXEC_POST ( integer )
執行一條預處理語句之後觸發事件。
PDO::PARAM_EVT_FETCH_PRE ( integer )
從一個結果集中取出一條結果之前觸發事件。
PDO::PARAM_EVT_FETCH_POST ( integer )
從一個結果集中取出一條結果之後觸發事件。
PDO::PARAM_EVT_NORMALIZE ( integer )
在綁定參數註冊允許驅動程序正常化變量名時觸發事件。
-----------------------------------------------
errorInfo()
The error codes for SQLite version 3 are unchanged from version 2. They are as follows:
#define SQLITE_OK           0   // Successful result
#define SQLITE_ERROR        1   // SQL error or missing database
#define SQLITE_INTERNAL     2   // An internal logic error in SQLite
#define SQLITE_PERM         3   // Access permission denied
#define SQLITE_ABORT        4   // Callback routine requested an abort
#define SQLITE_BUSY         5   // The database file is locked
#define SQLITE_LOCKED       6   // A table in the database is locked
#define SQLITE_NOMEM        7   // A malloc() failed
#define SQLITE_READONLY     8   // Attempt to write a readonly database
#define SQLITE_INTERRUPT    9   // Operation terminated by sqlite_interrupt()
#define SQLITE_IOERR       10   // Some kind of disk I/O error occurred
#define SQLITE_CORRUPT     11   // The database disk image is malformed
#define SQLITE_NOTFOUND    12   // (Internal Only) Table or record not found
#define SQLITE_FULL        13   // Insertion failed because database is full
#define SQLITE_CANTOPEN    14   // Unable to open the database file
#define SQLITE_PROTOCOL    15   // Database lock protocol error
#define SQLITE_EMPTY       16   // (Internal Only) Database table is empty
#define SQLITE_SCHEMA      17   // The database schema changed
#define SQLITE_TOOBIG      18   // Too much data for one row of a table
#define SQLITE_CONSTRAINT  19   // Abort due to contraint violation
#define SQLITE_MISMATCH    20   // Data type mismatch
#define SQLITE_MISUSE      21   // Library used incorrectly
#define SQLITE_NOLFS       22   // Uses OS features not supported on host
#define SQLITE_AUTH        23   // Authorization denied
#define SQLITE_ROW         100  // sqlite_step() has another row ready
#define SQLITE_DONE        101  // sqlite_step() has finished executing
-----------------------------------------------
*/

// equivalent to MySQL's OLD_PASSWORD() function
function OLD_PASSWORD($input, $hex = true) {
        $nr    = 1345345333;
        $add   = 7;
        $nr2   = 0x12345671;
        $tmp   = null;
        $inlen = strlen($input);
        for ($i = 0; $i < $inlen; $i++) {
            $byte = substr($input, $i, 1);
            if ($byte == ' ' || $byte == "\t") {
                continue;
            }
            $tmp = ord($byte);
            $nr ^= ((($nr & 63) + $add) * $tmp) + (($nr << 8) & 0xFFFFFFFF);
            $nr2 += (($nr2 << 8) & 0xFFFFFFFF) ^ $nr;
            $add += $tmp;
        }
        $out_a  = $nr & ((1 << 31) - 1);
        $out_b  = $nr2 & ((1 << 31) - 1);
        $output = sprintf("%08x%08x", $out_a, $out_b);
        if ($hex) {
            return $output;
        }

        return hexHashToBin($output);
    }

    function hexHashToBin($hex) {
        $bin = "";
        $len = strlen($hex);
        for ($i = 0; $i < $len; $i += 2) {
            $byte_hex  = substr($hex, $i, 2);
            $byte_dec  = hexdec($byte_hex);
            $byte_char = chr($byte_dec);
            $bin .= $byte_char;
        }

        return $bin;
    }
    
  
?>