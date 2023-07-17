<?php

	function jsondb_insert($file_name,$insert_data=array()){
		if(file_exists($file_name)){
			$old_data=file_get_contents($file_name);
			$temp1=json_decode($old_data, true);
			if(is_array($temp1) && count($temp1)>0 ){
				array_push($temp1,$insert_data);
				$insert_json= json_encode($temp1);
			}
			else{
					$temp1=array();
					array_push($temp1,$insert_data);
					$insert_json=json_encode($temp1);
			}
		}
		else{
				$temp1=array();
				array_push($temp1,$insert_data);
				$insert_json=json_encode($temp1);
		}
		$fp=fopen($file_name,'w');
		fputs($fp,$insert_json);
		fclose($fp);
	}
	
	function jsondb_del($file_name,$field_name='',$index_id=0){
		if(file_exists($file_name)){
			$old_data=file_get_contents($file_name);
			$news_data=json_decode($old_data,true);
			foreach($news_data as $index => $value){
				if($value[$field_name]==$index_id){
					unset($news_data[$index]);
				}
			}
			$insert_json=json_encode($news_data);
			$fp=fopen($file_name,'w');
			fputs($fp,$insert_json);
			fclose($fp);
		}
	}
	
	function jsondb_update($file_name,$update_data=array(),$field_name='',$index_id=0){
		if(file_exists($file_name)){
			$old_data=file_get_contents($file_name);
			$news_data=json_decode($old_data,true);
			if($index_id !='' && $index_id !='0'){
				$find_field=0;
				foreach($news_data as $index => $value){
					if($value[$field_name]==$index_id){
						$find_field=1;
						foreach($update_data as $array_key => $array_data){
							$news_data[$index][$array_key]=$update_data[$array_key];
						}
						break;
					}
				}
				//更新找不到欄位則進行新增一筆
				if($find_field<=0){						
						array_push($news_data,$update_data);
				}
			}
			else{
					foreach($news_data as $index => $value){
							foreach($update_data as $array_key => $array_data){
								$news_data[$index][$array_key]=$update_data[$array_key];
							}
					}
			}	
			$insert_json=json_encode($news_data);
			$fp=fopen($file_name,'w');
			fputs($fp,$insert_json);
			fclose($fp);
		}
	}
	
	function jsondb_select($file_name,$return_array=false){
		$news_data=array();
		if(file_exists($file_name)){
			$old_data=file_get_contents($file_name);			
			$news_data=json_decode($old_data,($return_array)? true:false  );
			return $news_data;	
		}
	}
	
	function jsondb_search($file_name,$select_field=array(),$field_name='',$index_id=0){
		$news_data=array();
		$result_data=array();
		if(file_exists($file_name)){
			$old_data=file_get_contents($file_name);			
			$news_data=json_decode($old_data,true);
			foreach($news_data as $index => $value){
				if(($field_name!='' && $value[$field_name]==$index_id) || $field_name==''){
						$select_value=array();
						foreach($select_field as $array_key ){
										$select_value[$array_key]=$news_data[$index][$array_key];								
						}
						array_push($result_data,$select_value);
				}		
			}
			return $result_data;
		}
	}
	
?>