<?php


//縮圖函式庫
include(html_class::get_class_path()."/picture_class.php");
	
class uploadImage{
    public $limit_type;
    public $limit_size=array();
    public $smaller_size;
    
    public function uploadPIC($source_img,$save_path,$thumbnail_path){
        $error_message='';
        $img_type=getimagesize($source_img["tmp_name"]);//img_type[0]=width img_type[1]=height	
       
        if(($this->limit_size['width']!=0 && $img_type[0]!= $this->limit_size['width']) || ($this->limit_size['height']!=0 && $img_type[1] != $this->limit_size['height']) || !in_array($source_img["type"],$this->limit_type)){
            if((isset($img_type['channels']) && $img_type['channels']==4) || !in_array($source_img["type"],$this->limit_type)){
            	$error_message.="<br/>列表圖檔格式不正確y ( 限 RGB jpg, gif, png 格式 )".($source_img['name'])."++".serialize($source_img['name']) ;
            }
            else if(($this->limit_size['width']!=0 && $img_type[0] != $this->limit_size['width']) || ($this->limit_size['height']!=0 && $img_type[1] != $this->limit_size['height'])){
            	$error_message.="<br/>列表圖檔尺寸不正確x ( 限 ".$this->limit_size['width']."×".$this->limit_size['height']." px )";
            }
        } 
        else{
            $ext = substr(strrchr($source_img['name'], '.'), 1);
            $newFileName='main_'.time().mt_rand().'.'.$ext;
            turn_image($source_img['tmp_name'],$source_img['tmp_name']);										
            copy($source_img['tmp_name'],$save_path.'/'.$newFileName);
            chmod($save_path.'/'.$newFileName,0777);
            reduce($img_type['mime'],$source_img['tmp_name'],$thumbnail_path.'/'.$newFileName,$this->smaller_size['width'],$this->smaller_size['height']);
            chmod($thumbnail_path.'/'.$newFileName,0777);
		}  
		if($error_message==''){
		    return ['status'=>'ok','message'=>'','newFileName'=>$newFileName];
		}
		else{
		    return ['status'=>'fail','message'=>$error_message];
		}   
    }
}
?>