<?php
class radio_obj{
	public $show_select_all=false;
	public $box_number="5";
	public $box_name='';
	public $box_id=array();
	public $box_class=array();
	public $box_onclick=array();
	public $box_onchange=array();
	public $box_value=array();
	public $box_text=array();
	public $box_checked='';
	public $plumb=false;

	public function create($Name='',$CID=array(),$CheckedValue=array()){
		if(isset($Name) && $Name!=''){$this->box_name=$Name;}
		if(isset($CID) && count($CID)>0){$this->box_id=$CID;}
		if(isset($CheckedValue) && $CheckedValue!=''){$this->box_checked=$CheckedValue;}

		$box_data="";
		if(sizeof($this->box_id)!=$this->box_number){$this->box_id=array_pad($this->box_id, $this->box_number, "default_radio");}
		if(sizeof($this->box_value)!=$this->box_number){$this->box_value=array_pad($this->box_value, $this->box_number, "");}
		for($temp_value1=0;$temp_value1< $this->box_number;$temp_value1++){
			$box_data.="\r<input type=\"radio\" ";
			$box_data.=(!empty($this->box_name) && trim($this->box_name)!='')? " name=\"".$this->box_name."\" ":"name=\"default_ck_".$temp_value1."\" ";
			$box_data.=(!empty($this->box_id[$temp_value1]) && trim($this->box_id[$temp_value1])!='')? " id=\"".$this->box_id[$temp_value1]."\" ":( (trim($this->box_name[$temp_value1])!='')?" id=\"".$this->box_name[$temp_value1].$temp_value1."\" ":" id=\"default_ck_".$temp_value1."\" " );
			$box_data.=(!empty($this->box_class[$temp_value1]) && trim($this->box_class[$temp_value1])!='')? " class=\"".$this->box_class[$temp_value1]."\" ":"";
			$box_data.=(!empty($this->box_onclick[$temp_value1]) && trim($this->box_onclick[$temp_value1])!='')? " onclick=\"".$this->box_onclick[$temp_value1]."\" ":"";
			$box_data.=(!empty($this->onchange[$temp_value1]) && trim($this->onchange[$temp_value1])!='')? " onchange=\"".$this->onchange[$temp_value1]."\" ":"";
			$box_data.=(isset($this->box_value[$temp_value1]) )? " value=\"".$this->box_value[$temp_value1]."\" ":"";
			$box_data.=($this->box_value[$temp_value1]==$this->box_checked)? " checked ":"";
			$box_data.= " />";
			$box_data.=(!empty($this->box_text[$temp_value1]) && trim($this->box_text[$temp_value1])!='')? trim($this->box_text[$temp_value1])."\n":"radio".$temp_value1."\n";

			if($this->plumb){$box_data.= "<br/>\n";}
			if($this->show_select_all){
				$box_data.="<input type='checkbox' name=\"\" id=\"\" onclick=\"\" >全選";
			}
		}

		return $box_data;
	}

}

?>