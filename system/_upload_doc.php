<?php
$errorMeassage = 0;
$uploadSuccess = 0;
$uploadFailed = 0;
// inserting new question .....
if($_REQUEST['upload']){
	$dd = date("Y-m-d H:i:s");
	$stamp = strtotime($dd);
	// upload file
	$allowed_ext = array ('txt' => 'application-x/txt');			
	// Where the file is going to be placed
	if(!is_dir("uploads")) mkdir("uploads",0777);
	if(!is_dir("uploads/".$_SESSION['person'])) mkdir("uploads/".$_SESSION['person'],0777);
	$target_path = "uploads/".$_SESSION['person']."/";
	$reporthand = $target_path.$stamp."_report.vb";
	
	/* Add the original filename to our target path.  
	Result is "uploads/docs/filename.extension" */
	$pos = strrpos(basename($_FILES['uploadedfile']['name']),".")+1;
	$newname = strtolower(substr(basename($_FILES['uploadedfile']['name']),$pos,3));
	$filename = $stamp.".".$newname;
	$target_path = $target_path . $filename;
	
	if(!move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)){
		switch($_FILES['uploadedfile']['error']) {
			case 2:
					$uploadFailed = 1;
					$errorMeassage = "Size of text file exceeds 10 mb";
					break;
			case 3:
					$uploadFailed = 1;
					$errorMeassage = "Uploading text file is not finished";
					break;
			case 4:
					$uploadFailed = 1;
					$errorMeassage = "You didn't choose any text file";
					break;
		}
	}
	else if(!array_key_exists($newname, $allowed_ext)){
		unlink($target_path);
		$uploadFailed = 1;
		$errorMeassage = "File you uploaded is not in (.txt) format";
	}
	else{
// insert into db
		$reportFile = fopen($reporthand,"w+");
		$lines = file($target_path);
		$lastOrder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId"));
		$vidOrder = $lastOrder[0]+1;
		for($i=0 ; $i<count($lines) ; $i+=2){
			
			if(trim($lines[$i]) != "" && trim($lines[$i+1]) != "") {	
				if (!get_magic_quotes_gpc()) $vidTitle = addslashes(trim($lines[$i]));
				else $vidTitle = trim($lines[$i]);
				$vidUrl = trim($lines[$i+1]);
			}
			else {
				if(trim($lines[$i]) == ""){
					while(trim($lines[$i]) == ""){
						$i++;
						if(trim($lines[$i]) != ""){
							if (!get_magic_quotes_gpc()) $vidTitle = addslashes(trim($lines[$i]));
							else $vidTitle = trim($lines[$i]);
							break;
						} 
						else continue;
					}
						
				}
				if(trim($lines[$i+1]) == ""){
					while(trim($lines[$i+1]) == ""){
						$i++;
						if(trim($lines[$i+1]) != ""){
							$vidUrl = trim($lines[$i+1]);
							break;
						} 
						else continue;
					}
				}
			}
			
			//if(strstr($vidUrl,"://",true) == "") continue;
			// filtering url from un-wanted characters
			if(strpos($vidUrl,"&") !== false) $length = strpos($vidUrl,"&");
			else $length = strlen($vidUrl);
			$vidUrl = trim(str_replace("watch?v=","v/",substr($vidUrl,0,$length)));
			$insBefore = mysqli_num_rows(mysqli_query($db,"SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidurl` LIKE '$vidUrl'"));
			if($insBefore == 0){
				$insertNewLine = mysqli_query($db,"INSERT INTO `vb_videos` (`crsid_fk`, `vidtitle`, `vidurl`, `vidorder`, `modify_date`) VALUES ('$crsId', '$vidTitle', '$vidUrl', '$vidOrder', '".date("Y-m-d H:i:s")."');");
				
				if($insertNewLine){
					fwrite($reportFile,"<strong>[Success]</strong>  ".$vidTitle."<br />\r\n");
					$vidOrder++;
				} else fwrite($reportFile,"<strong>[Failed]</strong>  ".$vidTitle."<br />\r\n");
				
			}
			else fwrite($reportFile,"<strong>[Found]</strong>  ".$vidTitle."<br />\r\n");
		}
		fclose($reportFile);
		$uploadSuccess = 1;
		//unlink($target_path);
	}
}
?>