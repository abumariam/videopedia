<?php
$vidTitle = $_REQUEST['vidTitle'];
$vidUrl = $_REQUEST['vidUrl'];
$crsId = $_REQUEST['crsid'];
$lastOrder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId"));
$vidOrder = $lastOrder[0]+1;
// filtering url from un-wanted characters
	if(strpos($vidUrl,"&") !== false) $length = strpos($vidUrl,"&");
	else $length = strlen($vidUrl);
	$vidUrl = trim(str_replace("watch?v=","v/",substr($vidUrl,0,$length)));
	$insBefore = mysqli_num_rows(mysqli_query($db,"SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidurl` LIKE '$vidUrl'"));
	if($insBefore == 0){
		$insertNewLine = mysqli_query($db,"INSERT INTO `vb_videos` (`crsid_fk`, `vidtitle`, `vidurl`, `vidorder`, `modify_date`) VALUES ('$crsId', '$vidTitle', '$vidUrl', '$vidOrder', '".date("Y-m-d H:i:s")."');");
		
		if($insertNewLine) echo"<script>alert('Adding \"".$vidTitle."\" video is succeed!'); 
			history.back();
			</script>";
		else echo"<script>alert('Adding \"".$vidTitle."\" video is failed!'); 
		history.back();
		</script>";
		
	}
?>