<?php
	class checkbox_obj{
		public  $show_select_all=false;
		public  $box_number="5";
		public  $box_name=array();
		public  $box_id=array();
		public  $box_class=array();
		public  $box_onclick=array();
		public  $box_onchange=array();		
		public  $box_value=array();
		public  $box_text=array();
		public  $box_checked=array();
		public  $plumb=false;
		
		public function create($Name=array(),$CID=array(),$CheckedValue=array()){
			
			if(isset($Name) && count($Name)>0){$this->box_name=$Name;}
			if(isset($CID) && count($CID)>0){$this->box_id=$CID;}
			if(isset($CheckedValue) && count($CheckedValue)>0){$this->box_checked=$CheckedValue;}
			
			$box_data="";
			if(sizeof($this->box_name)!=$this->box_number){$this->box_name=array_pad($this->box_name, $this->box_number, "default_checkbox");}
			if(sizeof($this->box_id)!=$this->box_number){$this->box_id=array_pad($this->box_id, $this->box_number, "default_checkbox");}
			if(sizeof($this->box_value)!=$this->box_number){$this->box_value=array_pad($this->box_value, $this->box_number, "");}			
			
			foreach($this->box_value as $temp_value1 => $checkboxData){
				if(trim($this->box_value[$temp_value1])==''){continue;}
			
					$box_data.="\r<input type=\"checkbox\" ";
					$box_data.=(!empty($this->box_name[$temp_value1]) && trim($this->box_name[$temp_value1])!='')? " name=\"".$this->box_name[$temp_value1]."\" ":"name=\"default_ck_".$temp_value1."\" ";
					$box_data.=(!empty($this->box_id[$temp_value1]) && trim($this->box_id[$temp_value1])!='')? " id=\"".$this->box_id[$temp_value1]."\" ":( (trim($this->box_name[$temp_value1])!='')?" id=\"".$this->box_name[$temp_value1].$temp_value1."\" ":" id=\"default_ck_".$temp_value1."\" " );
					$box_data.=(!empty($this->box_class[$temp_value1]) && trim($this->box_class[$temp_value1])!='')? " class=\"".$this->box_class[$temp_value1]."\" ":"";
					$box_data.=(!empty($this->box_onclick[$temp_value1]) && trim($this->box_onclick[$temp_value1])!='')? " onclick=\"".$this->box_onclick[$temp_value1]."\" ":"";
					$box_data.=(!empty($this->onchange[$temp_value1]) && trim($this->onchange[$temp_value1])!='')? " onchange=\"".$this->onchange[$temp_value1]."\" ":"";
					$box_data.=(!empty($this->box_value[$temp_value1]) && trim($this->box_value[$temp_value1])!='')? " value=\"".$this->box_value[$temp_value1]."\" ":"";
					$box_data.=(@in_array($this->box_value[$temp_value1],$this->box_checked))? " checked ":"";
					$box_data.= " />";
					$box_data.= "<span class='mgR-10'>";
					$box_data.=(!empty($this->box_text[$temp_value1]) && trim($this->box_text[$temp_value1])!='')? trim($this->box_text[$temp_value1])."\n":"checkbox".$temp_value1."\n";
					$box_data.= "</span>";
					if($this->plumb){$box_data.= "<br/>\n";}
			
				
			}
			
			return $box_data;
		}
	
	}
	

?>