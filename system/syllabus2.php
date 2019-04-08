<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$bar_menu = "home";

$outcomeArr = array("Understand","Use","Design","Implement","Derive","Evaluate","Synthesize");
$profCompArr = array("Mathematics","Sciences","General Ed.","Eng. Science","Eng. Design");

$crsId = $_REQUEST['crsid'];
$catidq = mysqli_fetch_row(mysqli_query($db,"SELECT `fk_catid` FROM `vb_courses` WHERE `crsid` = $crsId"));
// add new video
if($_REQUEST['save'] || $_REQUEST['finish']){

	if (!get_magic_quotes_gpc()) $crsCoordinator = addslashes(trim($_REQUEST['crsCoordinator']));
	else $crsCoordinator = trim($_REQUEST['crsCoordinator']);
	$reviewdate = $_REQUEST['reviewdate'];
	//$crsId = $_REQUEST['crsid'];
	
	// update course data
	$updatCourse = mysqli_query($db,"UPDATE `vb_courses` SET `crscoordinator` = '$crsCoordinator', `lastreview` = '$reviewdate' WHERE `crsid` = $crsId;");
	
	$preTopics = trim($_REQUEST['preTopics'],"#");
	if(!empty($preTopics)){
		mysqli_query($db,"DELETE FROM `vb_prerequists` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$preTopics = explode("#",$preTopics);
		foreach($preTopics as $preTopic){
			$insTopics = mysqli_query($db,"INSERT INTO `vb_prerequists` (`record`, `crsid_fk`, `topictitle`) VALUES ($count, $crsId, '$preTopic');");
			$count++;
		}
	}
	
	$compTools = trim($_REQUEST['compTools'],"#");
	if(!empty($preTopics)){
		mysqli_query($db,"DELETE FROM `vb_comptools` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$compTools = explode("#",$compTools);
		foreach($compTools as $compTool){
			$insTools = mysqli_query($db,"INSERT INTO `vb_comptools` (`record`, `crsid_fk`, `toolname`) VALUES ($count, $crsId, '$compTool');");
			$count++;
		}
	}
	
	$topics = trim($_REQUEST['topics'],"#");
	if(!empty($topics)){
		mysqli_query($db,"DELETE FROM `vb_crstopics` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$topics = explode("#",$topics);
		foreach($topics as $topic){
			$topicTitle = strtok($topic,"{");
			$tok2 = strtok("{");
			$classTot = strtok($tok2," ");
			$insCrsTopics = mysqli_query($db,"INSERT INTO `vb_crstopics` (`record`, `crsid_fk`, `topictitle`, `classes`) VALUES ($count, $crsId, '$topicTitle', '$classTot');");
			$count++;
		}
	}
	
	$labs = trim($_REQUEST['labs'],"#");
	if(!empty($labs)){
		mysqli_query($db,"DELETE FROM `vb_labtopics` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$labs = explode("#",$labs);
		foreach($labs as $lab){
			$topicTitle = strtok($lab,"{");
			$tok2 = strtok("{");
			$hourTot = strtok($tok2," ");
			$insLabTopics = mysqli_query($db,"INSERT INTO `vb_labtopics` (`record`, `crsid_fk`, `topictitle`, `hours`) VALUES ($count, $crsId, '$topicTitle', '$hourTot');");
			$count++;
		}
	}
	
	$projects = trim($_REQUEST['projects'],"#");
	if(!empty($projects)){
		mysqli_query($db,"DELETE FROM `vb_projects` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$projects = explode("#",$projects);
		foreach($projects as $project){
			$projTitle = strtok($project,"{");
			$tok2 = strtok("{");
			$hourTot = strtok($tok2," ");
			$insProjects = mysqli_query($db,"INSERT INTO `vb_projects` (`record`, `crsid_fk`, `projtitle`, `hours`) VALUES ($count, $crsId, '$projTitle', '$hourTot');");
			$count++;
		}
	}
	
	mysqli_query($db,"DELETE FROM `vb_profcomp` WHERE `crsid_fk` = $crsId");
	for($i = 0 ; $i < count($profCompArr) ; $i++){
		$comp = $_REQUEST['component'.$i];
		$insProfComp = mysqli_query($db,"INSERT INTO `vb_profcomp` (`crsid_fk`, `component`, `hours`) VALUES ($crsId, '$profCompArr[$i]', '$comp');");	
	}
	
	$assId = explode("#",trim($_REQUEST['specAssessed'],"#"));
	$counti = 1; 
	mysqli_query($db,"DELETE FROM `vb_outcomes` WHERE `crsid_fk` = $crsId");
	foreach($outcomeArr as $item){
		$itemText = $_REQUEST['ableTo'.$counti];
		if(in_array($counti,$assId)) $assVal = 1; else $assVal = 0;
		$insProfComp = mysqli_query($db,"INSERT INTO `vb_outcomes` (`crsid_fk`, `item`, `itemtext`, `assessed`) VALUES ($crsId, '$item', '$itemText', '$assVal');");
		$counti++;
	}

	if($_REQUEST['finish']) echo"<script>self.location='index.php?selctCat=$catidq[0]';</script>";
		
}

// select inserted data
	$crsdata = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname`, `fk_catid`, `crscredits`, `crscoordinator`, `lastreview` FROM `vb_courses` WHERE `crsid` = $crsId"));
	$assText = "";
	for($i = 0 ; $i < count($outcomeArr) ; $i++){
		$assItems = mysqli_fetch_row(mysqli_query($db,"SELECT `assessed` FROM `vb_outcomes` WHERE `crsid_fk` = $crsId AND `item` LIKE '$outcomeArr[$i]'"));
		$ii = $i+1;
		if($assItems[0] == 1) $assText .= "#".$ii;
	}
		
	$prerequists_q = mysqli_query($db,"SELECT `record`, `topictitle` FROM `vb_prerequists` WHERE `crsid_fk` = $crsId");
	$crstopics_q = mysqli_query($db,"SELECT `record`, `topictitle`, `classes` FROM `vb_crstopics` WHERE `crsid_fk` = $crsId");
	$labtopics_q = mysqli_query($db,"SELECT `record`, `topictitle`, `hours` FROM `vb_labtopics` WHERE `crsid_fk` = $crsId");
	$projects_q = mysqli_query($db,"SELECT `record`, `projtitle`, `hours` FROM `vb_projects` WHERE `crsid_fk` = $crsId");
	$comptools_q = mysqli_query($db,"SELECT `record`, `toolname` FROM `vb_comptools` WHERE `crsid_fk` = $crsId");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php require(INC_DIR."header_include.php"); ?>
<script type="text/javascript" src="<?php echo BASEURL; ?>inc/date_input.js"></script>
</head>

<body>
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><?php require(INC_DIR."banner.php");?></td>
  </tr>
  <tr>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="middle"><table width="1000" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="13" height="36" class="bodyTopLeft">&nbsp;</td>
            <td align="left" valign="middle" class="bodyTopBG"><div id="bodyHeader"><a href="<?php echo BASEURL;?>">Home </a> / <a href="<?php echo BASEURL;?>system/index.php?selctCat=<?php echo $crsdata[1]; ?>">Courses</a> / <?php echo $crsdata[0]; ?></div></td>
            <td width="13" class="bodyTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="bodyMidLeft">&nbsp;</td>
            <td align="left" valign="top" class="bodyMidBG">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="left" valign="top"><h3>Course Syllabus - Last Step</h3>
              <hr /><br />
<?php if($updatCourse){ ?>
<div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Updating course information is done successfully!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script>
<?php } ?>
<br />
<form id="form1" name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="7" cellpadding="0">
    <tr class="record0">
      <td width="16%" class="fieldLabel">Learning Outcomes / Expected Performance Criteria
      <input type="hidden" name="specAssessed" id="specAssessed" value="<?php echo $assText; ?>" /></td>
      <td width="84%" align="left" valign="top"><strong>Upon completion of the course, the students should be able to:<br />
        </strong>
        <table width="100%" border="0" align="left" cellpadding="0" cellspacing="3">
          <?php $itemCount = 1; foreach($outcomeArr as $item){
			  $outcomes_q = mysqli_fetch_row(mysqli_query($db,"SELECT `itemtext`, `assessed` FROM `vb_outcomes` WHERE `crsid_fk` = $crsId AND `item` LIKE '".$item."'"));
			  ?>
          <tr>
            <td width="15%"><?php echo $itemCount."- ".$item; ?></td>
            <td width="40%"><input name="<?php echo "ableTo".$itemCount; ?>" type="text" id="<?php echo "ableTo".$itemCount; ?>" size="40" class="inpuText" value="<?php if($outcomes_q[0]) echo $outcomes_q[0]; ?>" /></td>
            <td width="45%"><input name="<?php echo "assOut".$itemCount; ?>" type="checkbox" id="<?php echo "assOut".$itemCount; ?>" onclick="chckbox(this.id,'specAssessed')" value="<?php echo $itemCount; ?>" <?php if($outcomes_q[1] == 1) echo"checked"; ?> />
            <label for="<?php echo "assOut".$itemCount; ?>" style="font-style:italic">Assessed</label></td>
          </tr>
          <?php $itemCount++; } ?>
      </table></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Prerequistes by Topic</td>
      <td align="left" valign="top"><input name="prequisTopic" type="text" id="prequisTopic" size="50" class="inpuText" />
      <input type="button" name="addPrTopicBut" id="addPrTopicBut" value="Add" onclick="addItem('prequisTopic','_','preTopics','preTopicUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="preTopicUl">
      <?php 
	  $conText = "";
	  while($prerequists_r = mysqli_fetch_row($prerequists_q)){ 
	  	echo "<li id=\"li_".$crsId."-quist".$prerequists_r[0]."\"><pre>".$prerequists_r[1]."&nbsp;&nbsp;<a href=\"javascript:remItem('preTopics','preTopicUl','".$crsId."-quist".$prerequists_r[0]."','".$prerequists_r[1]."')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$prerequists_r[1];
	  } 
	  ?>
      </ol>
        <textarea name="preTopics" id="preTopics" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
      </div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Professional Component {Credit Hours}
      <input name="totHrs" type="hidden" id="totHrs" value="<?php echo $crsdata[2]; ?>" /></td>
      <td align="left" valign="top"><table width="100%" border="0" cellspacing="3" cellpadding="0">
        <?php for($c = 0 ; $c < count($profCompArr) ; $c+=2){
			$rest = "";
			for($j=0;$j<count($profCompArr);$j++){if($j != $c) $rest .="#component".$j; else continue;}
			$outcomes_q = mysqli_fetch_row(mysqli_query($db,"SELECT `hours` FROM `vb_profcomp` WHERE `crsid_fk` = $crsId AND `component` LIKE '".$profCompArr[$c]."'"));
			?>
        <tr>
          <td width="15%" align="left" valign="top"><?php echo $profCompArr[$c]; ?>:</td>
          <td width="35%" align="left" valign="top"><input name="<?php echo "component".$c; ?>" type="text" id="<?php echo "component".$c; ?>" size="3" onkeyup="chckTotal('<?php echo "component".$c; ?>','<?php echo trim($rest,"#"); ?>','totHrsExc')" class="inpuText" value="<?php if($outcomes_q[0]) echo $outcomes_q[0]; else echo "0"; ?>" /> 
            Hrs</td>
            <?php 
			if($profCompArr[$c+1]){
			$rest = "";
			for($j=0;$j<count($profCompArr);$j++){if($j != $c+1) $rest .="#component".$j; else continue;} 
			$idd = $c+1;
			$outcomes2_q = mysqli_fetch_row(mysqli_query($db,"SELECT `hours` FROM `vb_profcomp` WHERE `crsid_fk` = $crsId AND `component` LIKE '".$profCompArr[$idd]."'"));
			?>
          <td width="15%" align="left" valign="top"><?php echo $profCompArr[$idd]; ?>:</td>
          <td width="35%" align="left" valign="top"><input name="<?php echo "component".$idd; ?>" type="text" id="<?php echo "component".$idd; ?>" size="3" onkeyup="chckTotal('<?php echo "component".$idd; ?>','<?php echo trim($rest,"#"); ?>','totHrsExc')" class="inpuText"  value="<?php if($outcomes2_q[0]) echo $outcomes2_q[0]; else echo "0"; ?>" /> 
            Hrs</td><?php } ?>
          </tr>
          <?php } ?>
      </table><div id="totHrsExc"></div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Course Topics</td>
      <td align="left" valign="top"><label for="topicTxt">Title</label>
      <input name="topicTxt" type="text" id="topicTxt" size="50" class="inpuText" />
      <label for="crsTopicClasses">Classes</label>
      <input name="crsTopicClasses" type="text" id="crsTopicClasses" value="0" size="3" class="inpuText" />
      <input type="button" name="addTopicBut" id="addTopicBut" value="Add" onclick="addItem('topicTxt','crsTopicClasses#classes','topics','topicUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="topicUl">
      <?php 
	  $conText = "";
	  while($crstopics_r = mysqli_fetch_row($crstopics_q)){ 
	  	echo "<li id=\"li_".$crsId."-topic".$crstopics_r[0]."\"><pre>".$crstopics_r[1]." {".$crstopics_r[2]." classes}&nbsp;&nbsp;<a href=\"javascript:remItem('topics','topicUl','".$crsId."-topic".$crstopics_r[0]."','".$crstopics_r[1]." {".$crstopics_r[2]." classes}')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$crstopics_r[1]." {".$crstopics_r[2]." classes}";
	  } 
	  ?>
      </ol>
        <textarea name="topics" id="topics" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
      </div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Laboratory Topics</td>
      <td align="left" valign="top"><label for="labTopicTxt">Title</label>
      <input name="labTopicTxt" type="text" id="labTopicTxt" size="50" class="inpuText" />
      <label for="labHours">Hours</label>
      <input name="labHours" type="text" id="labHours" value="0" size="3" class="inpuText" />
      <input type="button" name="addLabBut" id="addLabBut" value="Add" onclick="addItem('labTopicTxt','labHours#hours','labs','labUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="labUl">
      <?php 
	  $conText = "";
	  while($labtopics_r = mysqli_fetch_row($labtopics_q)){ 
	  	echo "<li id=\"li_".$crsId."-lab".$labtopics_r[0]."\"><pre>".$labtopics_r[1]." {".$labtopics_r[2]." hours}&nbsp;&nbsp;<a href=\"javascript:remItem('labs','labUl','".$crsId."-lab".$labtopics_r[0]."','".$labtopics_r[1]." {".$labtopics_r[2]." hours}')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$labtopics_r[1]." {".$labtopics_r[2]." hours}";
	  } 
	  ?>
      </ol>
        <textarea name="labs" id="labs" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
      </div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Projects</td>
      <td align="left" valign="top"><label for="projectTxt">Title</label>
      <input name="projectTxt" type="text" id="projectTxt" size="50" class="inpuText" />
      <label for="projHours">Hours</label>
      <input name="projHours" type="text" id="projHours" value="0" size="3" class="inpuText" />
      <input type="button" name="addProjBut" id="addProjBut" value="Add" onclick="addItem('projectTxt','projHours#hours','projects','projectsUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="projectsUl">
      <?php 
	  $conText = "";
	  while($projects_r = mysqli_fetch_row($projects_q)){ 
	  	echo "<li id=\"li_".$crsId."-proj".$projects_r[0]."\"><pre>".$projects_r[1]." {".$projects_r[2]." hours}&nbsp;&nbsp;<a href=\"javascript:remItem('projects','projectsUl','".$crsId."-proj".$projects_r[0]."','".$projects_r[1]." {".$projects_r[2]." hours}')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$projects_r[1]." {".$projects_r[2]." hours}";
	  } 
	  ?>
      </ol>
        <textarea name="projects" id="projects" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
      </div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">CAD and Computer Tools Used</td>
      <td align="left" valign="top"><input name="addToolName" type="text" id="addToolName" size="50" class="inpuText" />
      <input type="button" name="addToolBut" id="addToolBut" value="Add" onclick="addItem('addToolName','_','compTools','toolsUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="toolsUl">
      <?php 
	  $conText = "";
	  while($comptools_r = mysqli_fetch_row($comptools_q)){ 
	  	echo "<li id=\"li_".$crsId."-tool".$comptools_r[0]."\"><pre>".$comptools_r[1]."&nbsp;&nbsp;<a href=\"javascript:remItem('compTools','toolsUl','".$crsId."-tool".$comptools_r[0]."','".$comptools_r[1]."')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$comptools_r[1];
	  } 
	  ?>
      </ol>
        <textarea name="compTools" id="compTools" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
      </div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Last Review</td>
      <td align="left" valign="top"><script><?php if($crsdata[4]){ ?>DateInput('reviewdate', true, 'YYYY-MM-DD', '<?php echo $crsdata[4]; ?>')<?php } else { ?>DateInput('reviewdate', true, 'YYYY-MM-DD')<?php } ?></script></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Course Coordinator</td>
      <td align="left" valign="top"><span id="sprytextfield1">
        <input name="crsCoordinator" type="text" id="crsCoordinator" size="40" class="inpuText" value="<?php if($crsdata[3]) echo $crsdata[3]; ?>" />
        <span class="textfieldRequiredMsg">Required.</span></span></td>
    </tr>
    <tr>
      <td align="left" valign="middle">&nbsp;</td>
      <td align="left" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" valign="middle">&nbsp;</td>
      <td align="left" valign="top"><input type="submit" name="save" id="save" value="Save    " class="btSave" />
        <input name="finish" type="submit" id="finish" value="Save &amp; Finish   " class="btSaveFinish" />&nbsp; or &nbsp;<a href="<?php echo BASEURL;?>system/syllabus1.php?crsid=<?php echo $crsId; ?>">Step Back</a></td>
    </tr>
  </table>
</form>
</td>
          </tr>
          
        </table></td>
            <td class="bodyMidRight">&nbsp;</td>
          </tr>
          <tr>
            <td height="47" class="bodyBotLeft">&nbsp;</td>
            <td class="bodyBotBG">&nbsp;</td>
            <td class="bodyBotRight">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="bottom"><?php require(INC_DIR."footer.php");?></td>
  </tr>
</table>
<form action="" method="post" name="recordsForm" id="recordsForm" style="display:none">
<input type="hidden" name="actionName" id="actionName" value="" />
<input type="hidden" name="recordNo" id="recordNo" value="" />
</form>
<?php db_disconnect(); ?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
</script>
</body>
</html>
