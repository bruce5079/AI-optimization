<?php
/*用於傳遞各頁面的變數物件*/
class template_page_var{	
	
	public function __get($name){    		
     		return (isset($this->{$name}))? $this->{$name}:"";
  	 }
  public function __set($name,$value){     		
     		 $this->{$name}=$value;
  }
  
	public function assign($name,$value){
		$value=(!isset($value))? "":$value;
		$this->{$name}=$value;
	}
	public function get($name){	
		if(isset($this->{$name})){
			return $this->{$name};
		}
		else{
			return '';
		}	
	}
}
?>