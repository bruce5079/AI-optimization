<?php
/*
下拉選單物件 by bruce V1.0

參數說明:
obj_name       select tag 的 name
obj_id         select tag 的 id
default_word   select tag 會出現在第一個預設內容字樣
default_value  select tag 會出現在第一個預設內容value值
start_value    select tag 的 起始值 為陣列時可以多樣性內容,或是單純的數字起始
end_value      select tag 的 結束值 為陣列時可以多樣性內容,或是單純的數字結尾
select_value   select tag 的 預設已選擇變數,一進網頁會停在其變數位置也就是 select=selected 的部分
show_word      select tag 會接在後面的文字
onchange       select tag onchange 事件發生所對應的javascript

使用方式
$order_select=new html_select_obj;
$order_select->obj_name="lan_order";
$order_select->obj_id="lan_order";
$select_source=array("aa"=>"aa","bb"=>"bb","cc"=>"cc","dd"=>"dd","ee"=>"ee","ff"=>"ff","gg"=>"gg");
$order_select->start_value=$select_source;
$order_select->select_value="dd";
echo $order_select->code_show();

要使用群組
把key值設定成
$select_source['optgroup_start:'.mt_rand()]='群組名';
$select_source['optgroup_end:'.mt_rand()]='';即可

範例:
$order_select=new html_select_obj;
$order_select->obj_name="lan_order";
$order_select->obj_id="lan_order";
$select_source=array('optgroup_start:'.mt_rand()=>"群組名1","bb"=>"bb","cc"=>"cc",'optgroup_end:'.mt_rand()=>"","ee"=>"ee","ff"=>"ff","gg"=>"gg");
$order_select->start_value=$select_source;
$order_select->select_value="dd";
echo $order_select->code_show();

純數字選單
$order_select=new html_select_obj;
$order_select->obj_name="lan_order";
$order_select->obj_id="lan_order";
$order_select->start_value="1";
$order_select->end_value="10";
$order_select->select_value="5";
echo $order_select->code_show();

*/
class html_select_obj{
	public $default_word="";
	public $default_value="";
	public $obj_name="";
	public $obj_id="";
	public $start_value="";
	public $start_nokey="";
	public $end_value="";
	public $select_value="";
	public $show_word;
	public $onchange="";
	public $cssSet="";
	public $required_tag="";
	public $required_txt="";
	public $zeronON="";

	public function create($Name='',$SID='',$SelectValue=''){
		if($Name!=''){$this->obj_name=$Name;}
		if($SID!=''){$this->obj_id=$SID;}
		if($SelectValue!=''){$this->select_value=$SelectValue;}
		$code="";
		$code="\r\n<select name='".$this->obj_name."' id='".$this->obj_id."' ";//size='1'
		$code.=(isset($this->onchange) && $this->onchange!="")? " onchange=\"".$this->onchange."\"":"";
		$code.=(isset($this->cssSet) && $this->cssSet!="")? " class=\"".$this->cssSet."\"":"";
		$code.=(isset($this->required_tag) && $this->required_tag==true)? " validate=\"required:true\"":"";
		$code.= ">";
		$code.=(isset($this->default_word) && trim($this->default_word)!="")? "<option value='".$this->default_value."'>".$this->default_word."</option>\n":"";

		if(is_array($this->start_value)){
			foreach ($this->start_value as $key => $text_name) {
				if(substr($key,0,14)=='optgroup_start'){
					$code.= "<optgroup label='".$text_name."'>";
				}
				else if(substr($key,0,12)=='optgroup_end'){
					$code.= "</optgroup>";
				}
				else{
					$select_show_value=($this->start_nokey=='')? $key : $text_name;
					if(isset($this->select_value) && $select_show_value==$this->select_value){
						$code.= "<option value='$select_show_value' selected>".$text_name."</option>";
					}
					else{
						$code.= "<option value='$select_show_value'>".$text_name."</option>";
					}
				}
			}
		}
		else{
			if($this->start_value >  $this->end_value){
				for($temp_value1=$this->start_value;$temp_value1 >= $this->end_value;$temp_value1--){
					$ex=strlen($this->end_value)-strlen($temp_value1);
					$tmp_zero='';
					if($this->zeronON==''){
						for($t3=0,$tmp_zero="";$t3< $ex;$t3++){
							$tmp_zero.="0";
						}
					}
					$temp_value2=$tmp_zero.$temp_value1;
					//-----

					if(isset($this->select_value) && $temp_value2==$this->select_value){
						$code.= "<option value='$temp_value2' selected>$temp_value2</option>\n";
					}
					else{
						$code.= "<option value='$temp_value2'>$temp_value2</option>\n";
					}
				}
			}
			else{
				for($temp_value1=$this->start_value;$temp_value1<= $this->end_value;$temp_value1++){
					$ex=strlen($this->end_value)-strlen($temp_value1);
					$tmp_zero='';
					if($this->zeronON==''){
						for($t3=0,$tmp_zero="";$t3< $ex;$t3++){
							$tmp_zero.="0";
						}
					}
					$temp_value2=$tmp_zero.$temp_value1;

					//-----
					if(isset($this->select_value) && $temp_value2==$this->select_value){
						$code.= "<option value='$temp_value2' selected>$temp_value2</option>\n";
					}
					else{
						$code.= "<option value='$temp_value2'>$temp_value2</option>\n";
					}
				}
			}
		}

		$code.= "</select>\r\n";
		$code.=(isset($this->required_txt) && $this->required_txt!='')? $this->required_txt:"";
		$code.=(isset($this->show_word) && $this->show_word!="")? $this->show_word : "";

		return $code;
	}
}
?>