<?php
/*
分頁處理物件 by bruce V1.0
參數說明:
show_select_button 顯示下拉選單的分頁功能 預設不顯示,true 為顯示

*/

class pagination_reception{
	var $show_select_button=false;
	var $form_mode=false;
	var $page_now_number;
	var $page_max_number;
	var $prv_page_check_number;
	var $pagination_show;
	var $vdata=array();
	var $Previous_word="Previous";
	var $Next_word="Next";
	//var $Previous_word="上頁";
	//var $Next_word="下頁";

	//15(300,30,20,)
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
	public function __construct($total_page,$now_page,$limit_page,$page_value=array()){
		$ad_temp_value=intval($total_page/$limit_page);
		$this->page_number_max=($total_page%$limit_page>0)? $ad_temp_value+1:$ad_temp_value; //總頁數
		$this->pagination_show=($total_page < $limit_page)? 0:1;
		$this->page_now_number=intval($now_page/$limit_page)+1;
		$this->vdata=$page_value;
		$this->prv_page_check_number=intval($now_page/$limit_page);
		$this->page_now_number=($this->page_now_number>$this->page_number_max)?$this->page_number_max:$this->page_now_number;
	}
	//-----根據現在頁數呈現
	function show($target_file="?",$ulShow=''){
		if($this->page_number_max<=0)return;
		if($this->form_mode){//表單式的切換頁面

		} else {
			$other_value="";
			$array_temp=1;
			foreach($this->vdata as $fname => $fvalue){
				$other_value.=$fname."=".$fvalue;
				if($array_temp< sizeof($this->vdata)){
					$other_value.="&";
				}
				$array_temp++;
			}
			$prev_number=($this->page_now_number-1<=0)? 1:($this->page_now_number-1);
			$link_first=$target_file.$other_value."&page=1";
			$link_prev=$target_file.$other_value."&page=".$prev_number;
			$link_end=$target_file.$other_value."&page=".$this->page_number_max;
			$show_pagination="";
			

			if($this->page_now_number > 1 && $this->page_number_max > 1 ){
				//$show_pagination.=(trim($ulShow)!='')?"<a href='".$link_prev."'>".$this->Previous_word."</a>&nbsp;\r\n":"<li><a href='".$link_prev."'>".$this->Previous_word."</a></li>&nbsp;\r\n";
				$show_pagination.="<li class='prev disable'><a href='".$link_prev."' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a>&nbsp;\r\n</li>";

			}
			//建立數字分頁
			$list_max=(($this->page_now_number%10==0) )?(intval($this->page_now_number/10))*10:(intval($this->page_now_number/10)+1)*10;

			$for_max=( $list_max > $this->page_number_max)? $this->page_number_max:$list_max;

			$number_list_start=($this->page_now_number%10==0)? $this->page_now_number-9:$this->page_now_number-($this->page_now_number%10)+1;

			for($temp_value1=$number_list_start;$temp_value1<= $for_max ;$temp_value1++){
				$link_number=$target_file.$other_value."&page=".$temp_value1;
				if($this->page_now_number==$temp_value1){
					$show_pagination.= "<li class='is-on'><a href='".$link_number."'>".$temp_value1."</a>&nbsp;</li>\r\n";
				} else {
					$show_pagination.= "<li><a href='".$link_number."'> ".$temp_value1." </a>&nbsp;</li>\r\n";
				}

			}
			$next_number=($this->page_now_number > $this->page_number_max)? $this->page_number_max:$this->page_now_number+1;
			$link_next=$target_file.$other_value."&page=".$next_number;
			if($next_number <= $this->page_number_max){
				$show_pagination.= "<li class='next'><a href='".$link_next."' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a>&nbsp;</li>\r\n";
			}

			//大於一頁才秀
			if($this->page_now_number >= 1 && $this->pagination_show){
				echo "<ul class='pagination'>";
				echo $show_pagination;
				echo "</ul>";
			}

		}

		//顯示下拉選單的分頁
		if($this->show_select_button){
			$fun_name=mktime().mt_rand(1, 1000);
			echo "<select name='change_page_nb".$fun_name."'  id='change_page_nb".$fun_name."' onchange='jump_page_from_select".$fun_name."();'>\r\n";
			for($temp_value1=1;$temp_value1<= $this->page_number_max;$temp_value1++){
				$link_option=$target_file.$other_value."&page=".$temp_value1;
				$show_selected=($temp_value1==$this->page_now_number)?" selected ":"";
				echo "<option value='".$link_option."' $show_selected>".$temp_value1."</option>\r\n";
			}
			echo "</select>\r\n";
			echo "<script>function jump_page_from_select".$fun_name."(){document.location.href=(document.getElementById('change_page_nb".$fun_name."').options[document.getElementById('change_page_nb".$fun_name."').selectedIndex].value);}</script>";
		}
	}

	function getHtmlCode($page_js=''){
		$result_html="";
		if($this->page_number_max<=0){return '';}
		if($this->form_mode){//表單式的切換頁面

		} else {
			$other_value="";
			$array_temp=1;
			foreach($this->vdata as $fname => $fvalue){
				$other_value.=$fname."=".$fvalue;
				if($array_temp< sizeof($this->vdata)){
					$other_value.="&";
				}
				$array_temp++;
			}
			$prev_number=($this->page_now_number-1<=0)? 1:($this->page_now_number-1);
			$link_first=$page_js."(1)";
			$link_prev=$page_js."(".$prev_number.")";
			$link_end=$page_js."(".$this->page_number_max.")";
			$show_pagination="";

			if($this->page_now_number > 1 && $this->page_number_max > 1 ){
				$show_pagination.="<a class='prev' href='".$link_prev."'>".$this->Previous_word."</a>&nbsp;\r\n";
			}

			//建立數字分頁
			$list_max=(($this->page_now_number%10==0) )?(intval($this->page_now_number/10))*10:(intval($this->page_now_number/10)+1)*10;

			$for_max=( $list_max > $this->page_number_max)? $this->page_number_max:$list_max;

			$number_list_start=($this->page_now_number%10==0)? $this->page_now_number-9:$this->page_now_number-($this->page_now_number%10)+1;

			for($temp_value1=$number_list_start;$temp_value1<= $for_max ;$temp_value1++){

				$link_number=$page_js."(".$temp_value1.")";

				if($this->page_now_number==$temp_value1){
					$show_pagination.= "<li class='is-on'><a href='".$link_number."' > ".$temp_value1." </a></li>&nbsp;\r\n";
				} else {
					$show_pagination.= "<li><a href='".$link_number."'> ".$temp_value1." </a></li>&nbsp;\r\n";
				}

			}

			$next_number=($this->page_now_number > $this->page_number_max)? $this->page_number_max:$this->page_now_number+1;
			$link_next=$page_js."(".$next_number.")";

			if($next_number <= $this->page_number_max){
				$show_pagination.= "<a class='next' href='".$link_next."'>".$this->Next_word."</a>&nbsp;\r\n";
			}

			//大於一頁才秀
			if($this->page_now_number > 1 && $this->pagination_show){
				$result_html.= "<ul>";
				$result_html.= $show_pagination;
				$result_html.= "</ul>";
			}
		}
		return $result_html;
	}
}

/*
	for($t2=0;$t2<=500;$t2+=20){
	$dd=new pagination(500,$t2,20,array("dd"=>"123","cc"=>"456"));
	$dd->show();echo "<br/>";
}
*/
?>