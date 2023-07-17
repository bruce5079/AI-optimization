<?php
//----
function imagecreatefrombmp($file){
	global  $CurrentBit, $echoMode;
	$f=fopen($file,"r");
	$Header=fread($f,2);

	if($Header=="BM")
	{
		$Size=freaddword($f);
		$Reserved1=freadword($f);
		$Reserved2=freadword($f);
		$FirstByteOfImage=freaddword($f);

		$SizeBITMAPINFOHEADER=freaddword($f);
		$Width=freaddword($f);
		$Height=freaddword($f);
		$biPlanes=freadword($f);
		$biBitCount=freadword($f);
		$RLECompression=freaddword($f);
		$WidthxHeight=freaddword($f);
		$biXPelsPerMeter=freaddword($f);
		$biYPelsPerMeter=freaddword($f);
		$NumberOfPalettesUsed=freaddword($f);
		$NumberOfImportantColors=freaddword($f);

		if($biBitCount<24)
		{
			$img=imagecreate($Width,$Height);
			$Colors=pow(2,$biBitCount);
			for($p=0;$p<$Colors;$p++)
			{
				$B=freadbyte($f);
				$G=freadbyte($f);
				$R=freadbyte($f);
				$Reserved=freadbyte($f);
				$Palette[]=imagecolorallocate($img,$R,$G,$B);
			}




			if($RLECompression==0)
			{
				$Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;

				for($y=$Height-1;$y>=0;$y--)
				{
					$CurrentBit=0;
					for($x=0;$x<$Width;$x++)
					{
						$C=freadbits($f,$biBitCount);
						imagesetpixel($img,$x,$y,$Palette[$C]);
					}
					if($CurrentBit!=0) {freadbyte($f);}
					for($g=0;$g<$Zbytek;$g++)
					freadbyte($f);
				}

			}
		}


		if($RLECompression==1) //$BI_RLE8
		{
			$y=$Height;

			$pocetb=0;

			while(true)
			{
				$y--;
				$prefix=freadbyte($f);
				$suffix=freadbyte($f);
				$pocetb+=2;

				$echoit=false;

				if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if(($prefix==0)and($suffix==1)) break;
				if(feof($f)) break;

				while(!(($prefix==0)and($suffix==0)))
				{
					if($prefix==0)
					{
						$pocet=$suffix;
						$Data.=fread($f,$pocet);
						$pocetb+=$pocet;
						if($pocetb%2==1) {freadbyte($f); $pocetb++;}
					}
					if($prefix>0)
					{
						$pocet=$prefix;
						for($r=0;$r<$pocet;$r++)
						$Data.=chr($suffix);
					}
					$prefix=freadbyte($f);
					$suffix=freadbyte($f);
					$pocetb+=2;
					if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
				}

				for($x=0;$x<strlen($Data);$x++)
				{
					imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
				}
				$Data="";

			}

		}


		if($RLECompression==2) //$BI_RLE4
		{
			$y=$Height;
			$pocetb=0;

			/*while(!feof($f))
			echo freadbyte($f)."_".freadbyte($f)."<BR>";*/
			while(true)
			{
				//break;
				$y--;
				$prefix=freadbyte($f);
				$suffix=freadbyte($f);
				$pocetb+=2;

				$echoit=false;

				if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if(($prefix==0)and($suffix==1)) break;
				if(feof($f)) break;

				while(!(($prefix==0)and($suffix==0)))
				{
					if($prefix==0)
					{
						$pocet=$suffix;

						$CurrentBit=0;
						for($h=0;$h<$pocet;$h++)
						$Data.=chr(freadbits($f,4));
						if($CurrentBit!=0) freadbits($f,4);
						$pocetb+=ceil(($pocet/2));
						if($pocetb%2==1) {freadbyte($f); $pocetb++;}
					}
					if($prefix>0)
					{
						$pocet=$prefix;
						$i=0;
						for($r=0;$r<$pocet;$r++)
						{
							if($i%2==0)
							{
								$Data.=chr($suffix%16);
							}
							else
							{
								$Data.=chr(floor($suffix/16));
							}
							$i++;
						}
					}
					$prefix=freadbyte($f);
					$suffix=freadbyte($f);
					$pocetb+=2;
					if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
				}

				for($x=0;$x<strlen($Data);$x++)
				{
					imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
				}
				$Data="";

			}

		}


		if($biBitCount==24)
		{
			$img=imagecreatetruecolor($Width,$Height);
			$Zbytek=$Width%4;

			for($y=$Height-1;$y>=0;$y--)
			{
				for($x=0;$x<$Width;$x++)
				{
					$B=freadbyte($f);
					$G=freadbyte($f);
					$R=freadbyte($f);
					$color=imagecolorexact($img,$R,$G,$B);
					if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
					imagesetpixel($img,$x,$y,$color);
				}
				for($z=0;$z<$Zbytek;$z++)
				freadbyte($f);
			}
		}
		return $img;

	}


	fclose($f);


}

function freadbyte($f)
{
	return ord(fread($f,1));
}

function freadword($f)
{
	$b1=freadbyte($f);
	$b2=freadbyte($f);
	return $b2*256+$b1;
}

function freaddword($f)
{
	$b1=freadword($f);
	$b2=freadword($f);
	return $b2*65536+$b1;
}

//旋轉校正圖片
function turn_image($source_pic,$purpose_pic){
	$img_type=getimagesize($source_pic);
	
	if(is_array($img_type)){
		$img_type=$img_type['mime'];
	}
	switch($img_type){
		case "image/gif":
			$src = ImageCreateFromGIF($source_pic);
			break;
		case "image/jpeg":
		case "image/pjpeg":
			$src = imagecreatefromjpeg($source_pic);
			break;
		case "image/gif":
			$src = imagecreatefromgif($source_pic);
			break;
		case "image/png":
			$src = imagecreatefromPNG($source_pic);
			break;
		case "image/bmp":
			$src = imagecreatefrombmp($source_pic);
			break;
		default:
			$src = imagecreatefromjpeg($source_pic);
	}
	//如果圖片是歪的進行矯正
	@$exif = exif_read_data($source_pic, 0, true); //讀取檔案exif資訊(檔案 , 是否以逗號區隔數組資訊結果 , 是否將每個Section都變成陣列)
	$orient = 0;
	if(isset($exif['IFD0']['Orientation'])){ //偵測exif的旋轉資訊
		$orient = $exif['IFD0']['Orientation'];
	}

	switch($orient) {
		case 3:     //圖片原向左旋轉180度
			$src = imagerotate($src, 180, 0);//圖片向左旋轉18度
			break;
		case 6:     //圖片原向左旋轉90度
			$src = imagerotate($src, -90, 0);//圖片向右旋轉90度
			break;
		case 8:     //圖片原向右旋轉90度
			$src = imagerotate($src, 90, 0); //圖片向左旋轉90度
			break;
		default:    //圖片不需要旋轉
//			$src = imagerotate($src, 0, 0);  //圖片不旋轉
			return true;
			break;
	}

	//--截圖結束
	switch($img_type){
		case "image/jpeg":
		case "image/pjpeg":
			imagejpeg($src,$purpose_pic,100);
		break;
		case "image/gif":
		case "image/bmp":
		case "image/png":
			//imagepng($src,$purpose_pic);
			imagejpeg($src,$purpose_pic,100);
		break;
		default:
			//imagepng($src,$purpose_pic);
			imagejpeg($src,$purpose_pic,100);
	}

}

//縮圖-------------------------------------------
function reduce($type,$source_pic,$purpose_pic,$new_x,$new_y){
	$new_x=intval($new_x);
	$new_y=intval($new_y);
	switch($type){
		case "image/gif":
			$src = ImageCreateFromGIF($source_pic);
			break;
		case "image/jpeg":
		case "image/pjpeg":
			$src = imagecreatefromjpeg($source_pic);
			break;
		case "image/gif":
			$src = imagecreatefromgif($source_pic);
			break;
		case "image/png":
			$src = imagecreatefromPNG($source_pic);
			break;
		case "image/bmp":
			$src = imagecreatefrombmp($source_pic);
			break;
	}

	//如果圖片是歪的進行矯正

	@$exif = exif_read_data($source_pic, 0, true); //讀取檔案exif資訊(檔案 , 是否以逗號區隔數組資訊結果 , 是否將每個Section都變成陣列)
	$orient = 0;
	if(isset($exif['IFD0']['Orientation'])){ //偵測exif的旋轉資訊
		$orient = $exif['IFD0']['Orientation'];
	}

	switch($orient) {
		case 3:     //圖片原向左旋轉180度
			$src = imagerotate($src, 180, 0);//圖片向左旋轉18度
			break;
		case 6:     //圖片原向左旋轉90度
			$src = imagerotate($src, -90, 0);//圖片向右旋轉90度
			break;
		case 8:     //圖片原向右旋轉90度
			$src = imagerotate($src, 90, 0); //圖片向左旋轉90度
			break;
		default:    //圖片不需要旋轉
			$src = imagerotate($src, 0, 0);  //圖片不旋轉
			break;
	}


	// 原始圖片的來源
	$src_w = imagesx($src);
	$src_h = imagesy($src);


	//如果原圖小於指定縮圖則不做動作
	if($src_w <= $new_x){
		$new_x=$src_w;
		$new_y=$src_h;
	}
	else if($src_h <= $new_y){
		$new_x=$src_w;
		$new_y=$src_h;
	}

	//如果只有一個參數有值,則另一個參數按照比例縮放
	if($new_x>0 && $new_y<=0){
		$thumb_w = $new_x;
		$thumb_h = intval(($src_h*$new_x)/$src_w);
		$new_y=$thumb_h;
	}
	else if($new_x<=0 && $new_y > 0){
		$thumb_w = intval(($src_w*$new_y)/$src_h);
		$thumb_h = $new_y;
		$new_x=$thumb_w;
	}
	else{
		// 取得原始圖片的長寬
		if($src_w > $src_h){
			$thumb_w = $new_x;
			$thumb_h = intval($src_h / $src_w * $new_x);
		//設定縮圖之後的寬度（改掉那個640為你要的數字）
		}else{
			$thumb_h = $new_y;
			$thumb_w = intval($src_w / $src_h * $new_y);
		//設定縮圖之後的高度（改掉那個640為你要的數字）
		}
	}
	// 如果使用的是GD1.6,請使用 imagecreate()
	$thumb = imagecreatetruecolor($thumb_w, $thumb_h);
	imagealphablending($thumb,false);//這裏很重要,意思是不合並顏色,直接用$img圖像顏色替換,包括透明色;
	imagesavealpha($thumb,true);//這裏很重要,意思是不要丟了$thumb圖像的透明色;

	// start resize
	imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);

	// save thumbnail
	imagejpeg($thumb,$purpose_pic,100);
	//上面有三個參數，最後一個是品質，上限一百。第二個參數是存檔的位置
}

//先縮再截圖
function reduce_screenshot($type,$source_pic,$purpose_pic,$new_x,$new_y){
	$new_x=intval($new_x);
	$new_y=intval($new_y);
	switch($type){
		case "image/gif":
			$src = ImageCreateFromGIF($source_pic);
			break;
		case "image/jpeg":
		case "image/pjpeg":
			$src = imagecreatefromjpeg($source_pic);
			break;
		case "image/gif":
			$src = imagecreatefromgif($source_pic);
			break;
		case "image/png":
			$src = imagecreatefromPNG($source_pic);
			imagesavealpha($src, true);
			break;
		case "image/bmp":
			$src = imagecreatefrombmp($source_pic);
			break;
	}

	//如果圖片是歪的進行矯正
	$exif = exif_read_data($source_pic, 0, true); //讀取檔案exif資訊(檔案 , 是否以逗號區隔數組資訊結果 , 是否將每個Section都變成陣列)
	$orient = 0;
	if(isset($exif['IFD0']['Orientation'])){ //偵測exif的旋轉資訊
		$orient = $exif['IFD0']['Orientation'];
	}
	switch($orient) {
		case 3:     //圖片原向左旋轉180度
			$src = imagerotate($src, 180, 0);//圖片向左旋轉18度
			break;
		case 6:     //圖片原向左旋轉90度
			$src = imagerotate($src, -90, 0);//圖片向右旋轉90度
			break;
		case 8:     //圖片原向右旋轉90度
			$src = imagerotate($src, 90, 0); //圖片向左旋轉90度
			break;
		default:    //圖片不需要旋轉
			$src = imagerotate($src, 0, 0);  //圖片不旋轉
			break;
	}

	// 原始圖片的來源
	$src_w = imagesx($src);
	$src_h = imagesy($src);


	$dont_do_anyting=0;
	//縮圖比原圖大 就不要動了
	if($src_w < $new_x || $src_h< $new_y){
		$new_x=$src_w;
		$new_y=$src_h;
		$dont_do_anyting=1;
	}
	//html_class::web_query_log('other','width='.$new_x.'&he='.$new_y);

	//如果只有一個參數有值,則另一個參數按照比例縮放
	if($new_x>0 && $new_y<=0){
		$thumb_w = $new_x;
		$thumb_h = intval(($src_h*$new_x)/$src_w);
		$new_y=$thumb_h;
	}
	else if($new_x<=0 && $new_y > 0){
		$thumb_w = intval(($src_w*$new_y)/$src_h);
		$thumb_h = $new_y;
		$new_x=$thumb_w;
	}
	else{
		if($src_w > $src_h){ //設定縮圖之後的寬度（改掉那個640為你要的數字）
			$thumb_w = $new_x;
			$thumb_h = intval($src_h / $src_w * $new_x);
			if($thumb_h > $new_y){
				$thumb_h = $new_y;
				$thumb_w = intval($src_w / $src_h * $new_y);
			}
			else if($thumb_w > $new_x){
				$thumb_w = $new_x;
				$thumb_h = intval($src_h / $src_w * $new_x);
			}

			//html_class::web_query_log('other','1 thumb width='.$thumb_w.'&he='.$thumb_h.',new_x='.$new_x.',new_y'.$new_y);
		}
		else{//設定縮圖之後的高度
			$thumb_h = $new_y;
			$thumb_w = intval($src_w / $src_h * $new_y);
			if($thumb_w > $new_x && $new_x > 0){
				$thumb_w = $new_x;
				$thumb_h = intval($src_h / $src_w * $new_x);
			}
			// html_class::web_query_log('other','2 thumb width='.$thumb_w.'&he='.$thumb_h.',new_x='.$new_x.',new_y'.$new_y);
		}
		//如果縮小的圖片長寬小於要被截取的圖長寬 則進行放寬
		if($thumb_w< $new_x && $new_x > 0){
			$thumb_h = intval($new_x*$thumb_h  / $thumb_w);
			$thumb_w = $new_x;
			// html_class::web_query_log('other','3 thumb width='.$thumb_w.'&he='.$thumb_h);
		}
		else if( $thumb_h < $new_y && $new_y > 0){
			$thumb_w = intval($new_y*$thumb_w  / $thumb_h);
			$thumb_h = $new_y;
			//html_class::web_query_log('other','4 thumb width='.$thumb_w.'&he='.$thumb_h);
		}
	}

	if(!$dont_do_anyting){
		$c_thumb_w=$thumb_w+20;
		$c_thumb_h=$thumb_h+20;
	}
	else{
		$c_thumb_w=$thumb_w;
		$c_thumb_h=$thumb_h;
	}


	// 如果使用的是GD1.6,請使用 imagecreate()
	$thumb = imagecreatetruecolor(intval($c_thumb_w), intval($c_thumb_h));

	imagealphablending($thumb,false);//這裏很重要,意思是不合並顏色,直接用$img圖像顏色替換,包括透明色;
	imagesavealpha($thumb,true);//這裏很重要,意思是不要丟了$thumb圖像的透明色;

	//html_class::web_query_log('other','width='.$thumb_w.'&he='.$thumb_h);
	//縮圖
	imagecopyresampled($thumb, $src, 0, 0, 0, 0, intval($c_thumb_w), intval($c_thumb_h), $src_w, $src_h);

	//計算差數 大於10px 才可以取位移
	$difference_number=($thumb_w-$new_x > 10)? 10:0;

	$cut_start_x=($thumb_w > $new_x) ? intval(($thumb_w - $new_x)/2)+$difference_number:0;
	$cut_start_y=($thumb_h > $new_y) ? intval(($thumb_h - $new_y)/2)+$difference_number:0;

	//html_class::web_query_log('other','imagecopyresampled='.$cut_start_x.",". $cut_start_y.",". $new_x.",". $new_y.",". $new_x.",". $new_y);
	//----截圖開始
	/* 先建立一個 新的空白圖檔 */
	$newim = imagecreatetruecolor($new_x, $new_y);
	imagealphablending($newim,false);//這裏很重要,意思是不合並顏色,直接用$img圖像顏色替換,包括透明色;
	imagesavealpha($newim,true);//這裏很重要,意思是不要丟了$thumb圖像的透明色;
	imagecopyresampled($newim, $thumb, 0, 0, $cut_start_x, $cut_start_y, $new_x, $new_y, $new_x, $new_y);
	//--截圖結束
	imagepng($newim,$purpose_pic);
	//imagepng($newim,"/home/nic/public_html/images/product/".mktime().".png");
	//上面有三個參數，最後一個是品質，上限一百。第二個參數是存檔的位置
}


function set_copyright($source_file,$copyright_file,$paste_x,$paste_y){
	//讀取來源圖
	$RSize = getImageSize($source_file);
	$RInfo = pathInfo($source_file);

	//根據副檔名讀取版權圖檔
	switch (strtolower($RInfo['extension'])) {
		//case "gif": $RSrc = imageCreateFromGif($source_file); break;
		case "gif":
			return;
		break;
		case "jpg":
			@$RSrc = imageCreateFromJpeg($source_file);
			if(!$RSrc){
				$RSrc = imageCreateFromPng($source_file);
			}
			break;
		case "png": $RSrc = imageCreateFromPng($source_file); break;
		default:$RSrc = imageCreateFromPng($source_file); break;
	}
	if($copyright_file!=''){
		//讀取版權圖
		$CPSize = getImageSize($copyright_file);
		$CPInfo = pathInfo($copyright_file);

		//根據副檔名讀取版權圖檔
		switch (strtolower($CPInfo['extension'])) {
			case "gif": $CPSrc = imageCreateFromGif($copyright_file); break;
			case "jpg": $CPSrc = imageCreateFromJpeg($copyright_file); break;
			case "png": $CPSrc = imageCreateFromPng($copyright_file); break;
			default:$CPSrc = imageCreateFromPng($copyright_file); break;
		}

		$CP_src_w = imagesx($CPSrc);
		$CP_src_h = imagesy($CPSrc);
		//寫入版權圖
		ImageCopyResized($RSrc, $CPSrc, $paste_x,$paste_y , 0, 0 ,$CP_src_w, $CP_src_h,$CP_src_w,$CP_src_h);
		//--截圖結束
		imagejpeg($RSrc,$source_file,100);
		imagedestroy($CPSrc);
		imagedestroy($RSrc);
	}
}

/*
imagecopyresampled
$dst_image ：新建的圖片
$src_image ：需要載入的圖片
$dst_x ：設定需要載入的圖片在新圖中的x坐標
$dst_y ：設定需要載入的圖片在新圖中的y坐標
$src_x ：設定載入圖片要載入的區域x坐標
$src_y ：設定載入圖片要載入的區域y ​​坐標
$dst_w ：設定載入的原圖的寬度（在此設置縮放）
$dst_h ：設定載入的原圖的高度（在此設置縮放）
$src_w ：原圖要載入的寬度
$src_h ：原圖要載入的高度
*/
?>