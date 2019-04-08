<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$bar_menu = "home";

$crsId = $_REQUEST['crsid'];
// add new video
if($_REQUEST['save'] || $_REQUEST['continue']){

	if (!get_magic_quotes_gpc()) $crsName = addslashes(trim($_REQUEST['crsName']));
	else $crsName = trim($_REQUEST['crsName']);
	if (!get_magic_quotes_gpc()) $crsCode = addslashes(trim($_REQUEST['crsCode']));
	else $crsCode = trim($_REQUEST['crsCode']);
	if (!get_magic_quotes_gpc()) $crsDesc = addslashes(trim($_REQUEST['crsDesc']));
	else $crsDesc = trim($_REQUEST['crsDesc']);
	if (!get_magic_quotes_gpc()) $crsLecs = addslashes(trim($_REQUEST['crsLecs']));
	else $crsLecs = trim($_REQUEST['crsLecs']);
	if (!get_magic_quotes_gpc()) $crsLab = addslashes(trim($_REQUEST['crsLab']));
	else $crsLab = trim($_REQUEST['crsLab']);
	if (!get_magic_quotes_gpc()) $univName = addslashes(trim($_REQUEST['univName']));
	else $univName = trim($_REQUEST['univName']);
	if (!get_magic_quotes_gpc()) $facName = addslashes(trim($_REQUEST['facName']));
	else $facName = trim($_REQUEST['facName']);
	if (!get_magic_quotes_gpc()) $progName = addslashes(trim($_REQUEST['progName']));
	else $progName = trim($_REQUEST['progName']);
	$credits = $_REQUEST['credits'];
	$crsDesign = $_REQUEST['crsDesign'];
	//$crsId = $_REQUEST['crsid'];
	
	// update course data
	$updatCourse = mysqli_query($db,"UPDATE `vb_courses` SET `crsname` = '$crsName', `crscode` = '$crsCode', `crscredits` = '$credits', `crsdescription` = '$crsDesc', `crsdesign` = '$crsDesign', `univname` = '$univName', `facname` = '$facName', `progname` = '$progName', `lecture` = '$crsLecs', `laboratory` = '$crsLab' WHERE `crsid` = $crsId;");
	
	$comMembers = trim($_REQUEST['comMembers'],"#");
	if(!empty($comMembers)){
		mysqli_query($db,"DELETE FROM `vb_committee` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$comMembers = explode("#",$comMembers);
		foreach($comMembers as $memName){
			$insMamber = mysqli_query($db,"INSERT INTO `vb_committee` (`record`, `crsid_fk`, `pname`) VALUES ($count, $crsId, '$memName');");
			$count++;
		}
	}
	
	$txtBooks = trim($_REQUEST['txtBooks'],"#");
	if(!empty($txtBooks)){
		mysqli_query($db,"DELETE FROM `vb_textbooks` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$txtBooks = explode("#",$txtBooks);
		foreach($txtBooks as $bookName){
			$insBook = mysqli_query($db,"INSERT INTO `vb_textbooks` (`record`, `crsid_fk`, `bookname`) VALUES ($count, $crsId, '$bookName');");
			$count++;
		}
	}
	
	$references = trim($_REQUEST['references'],"#");
	if(!empty($references)){
		mysqli_query($db,"DELETE FROM `vb_references` WHERE `crsid_fk` = $crsId");
		$count = 1;
		$references = explode("#",$references);
		foreach($references as $refName){
			$insRef = mysqli_query($db,"INSERT INTO `vb_references` (`record`, `crsid_fk`, `refname`) VALUES ($count, $crsId, '$refName');");
			$count++;
		}
	}
	if($_REQUEST['continue']) echo"<script>self.location='syllabus2.php?crsid=$crsId';</script>";
		
}

// select inserted data
	$crsdata = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname`, `fk_catid`, `crscode`, `crscredits`, `crsdescription`, `crsdesign`, `univname`, `facname`, `progname`, `lecture`, `laboratory` FROM `vb_courses` WHERE `crsid` = $crsId"));
	$committee_q = mysqli_query($db,"SELECT `record`, `pname` FROM `vb_committee` WHERE `crsid_fk` = $crsId");
	$textbooks_q = mysqli_query($db,"SELECT `record`, `bookname` FROM `vb_textbooks` WHERE `crsid_fk` = $crsId");
	$references_q = mysqli_query($db,"SELECT `record`, `refname` FROM `vb_references` WHERE `crsid_fk` = $crsId");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php require(INC_DIR."header_include.php"); ?>
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
            <td colspan="2" align="left" valign="top"><h3>Course Syllabus - First Step</h3>
              <hr /><br />
<?php if($updatCourse){ ?>
<div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Updating "<?php echo $crsName; ?>" course information is done successfully!<!-- Message End --></div>
<script>adCount=0;adTime=10;showAd();</script>
<?php } ?>
<br />

<form id="form1" name="form1" method="post" action="">
<!--<input id="crsid" name="crsid" type="hidden" value="<?php //echo $crsId; ?>" />-->  
<table width="100%" border="0" cellspacing="3" cellpadding="0">
    
    <tr class="record0">
      <td class="fieldLabelReq">Course Info.</td>
      <td align="left" valign="top"><label for="crsCode" style="font-style:italic">Code: </label>
        <span id="sprytextfield1">
        <input name="crsCode" type="text" id="crsCode" size="10" class="inpuText" value="<?php if($crsdata[2]) echo $crsdata[2]; ?>" />
        <span class="textfieldRequiredMsg"></span></span>
        <label for="crsName" style="font-style:italic"> Title: </label>
        <span id="sprytextfield2">
        <input name="crsName" type="text" id="crsName" size="40" class="inpuText" value="<?php echo $crsdata[0]; ?>" />
        <span class="textfieldRequiredMsg"></span></span>
        <label for="credits" style="font-style:italic"> Credits: </label>
        <span id="spryselect2">
        <select name="credits" id="credits">
          <option selected="selected">&lt;Select&gt;</option>
          <?php for($i=1 ; $i<=16 ; $i++){?>
          <option value="<?php echo $i; ?>" <?php if($crsdata[3] == $i) echo "selected"; ?>><?php echo $i; ?> Hrs</option>
          <?php } ?>
        </select>
        <span class="selectRequiredMsg">Required.</span></span></td>
    </tr>
    
    <tr class="record0">
      <td class="fieldLabel">Course Description</td>
      <td align="left" valign="top"><textarea name="crsDesc" id="crsDesc" cols="50" rows="3"><?php if($crsdata[4]) echo $crsdata[4]; ?></textarea></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Designation</td>
      <td align="left" valign="top"><span id="spryselect1">
        <select name="crsDesign" id="crsDesign">
          <option selected="selected">&lt;Select&gt;</option>
          <option value="Required" <?php if($crsdata[5] == "Required") echo "selected"; ?>>Required</option>
          <option value="Elective" <?php if($crsdata[5] == "Elective") echo "selected"; ?>>Elective</option>
        </select>
        <span class="selectRequiredMsg"></span></span></td>
    </tr>
	<tr class="record0">
      <td width="16%" class="fieldLabel">University Name</td>
      <td width="84%" align="left" valign="top"><input name="univName" type="text" id="univName" size="40" class="inpuText" value="<?php if($crsdata[6]) echo $crsdata[6]; ?>" /></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Faculty Name</td>
      <td align="left" valign="top"><input name="facName" type="text" id="facName" size="40" class="inpuText" value="<?php if($crsdata[7]) echo $crsdata[7]; ?>" /></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Program Name</td>
      <td align="left" valign="top"><input name="progName" type="text" id="progName" size="40" class="inpuText" value="<?php if($crsdata[8]) echo $crsdata[8]; ?>" /></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Lecture</td>
      <td align="left" valign="top"><textarea name="crsLecs" id="crsLecs" cols="45" rows="2"><?php if($crsdata[9]) echo $crsdata[9]; ?></textarea></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabel">Laboratory</td>
      <td align="left" valign="top"><textarea name="crsLab" id="crsLab" cols="45" rows="2"><?php if($crsdata[10]) echo $crsdata[10]; ?></textarea></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Committee</td>
      <td align="left" valign="top"><input name="addMemName" type="text" id="addMemName" size="40" class="inpuText" />
      <input type="button" name="addMemBut" id="addMemBut" value="Add" onclick="addItem('addMemName','_','comMembers','commUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="commUl">
      <?php 
	  $conText = "";
	  while($committee_r = mysqli_fetch_row($committee_q)){ 
	  	echo "<li id=\"li_".$crsId."-mem".$committee_r[0]."\"><pre>".$committee_r[1]."&nbsp;&nbsp;<a href=\"javascript:remItem('comMembers','commUl','".$crsId."-mem".$committee_r[0]."','".$committee_r[1]."')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$committee_r[1];
	  } 
	  ?>
      </ol>
        <span id="sprytextarea1">
        <textarea name="comMembers" id="comMembers" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
        <span class="textareaRequiredMsg">Add at least one.</span></span></div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Textbooks</td>
      <td align="left" valign="top"><input name="textBookTit" type="text" id="textBookTit" size="50" class="inpuText" />
      <input type="button" name="addTxtBokBut" id="addTxtBokBut" value="Add" onclick="addItem('textBookTit','_','txtBooks','txtBokUl')" />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="txtBokUl">
      <?php 
	  $conText = "";
	  while($textbooks_r = mysqli_fetch_row($textbooks_q)){ 
	  	echo "<li id=\"li_".$crsId."-bok".$textbooks_r[0]."\"><pre>".$textbooks_r[1]."&nbsp;&nbsp;<a href=\"javascript:remItem('txtBooks','txtBokUl','".$crsId."-bok".$textbooks_r[0]."','".$textbooks_r[1]."')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$textbooks_r[1];
	  } 
	  ?>
      </ol>
        <span id="sprytextarea2">
        <textarea name="txtBooks" id="txtBooks" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
        <span class="textareaRequiredMsg">Add at least one.</span></span></div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">References</td>
      <td align="left" valign="top"><input name="addRefName" type="text" id="addRefName" size="50" class="inpuText" />
      <input type="button" name="addRefBut" id="addRefBut" value="Add" onclick="addItem('addRefName','_','references','refUl')"  />
      <div style="border:1px dotted #CCCCCC; width:550px"><ol id="refUl">
      <?php 
	  $conText = "";
	  while($references_r = mysqli_fetch_row($references_q)){ 
	  	echo "<li id=\"li_".$crsId."-ref".$references_r[0]."\"><pre>".$references_r[1]."&nbsp;&nbsp;<a href=\"javascript:remItem('references','refUl','".$crsId."-ref".$references_r[0]."','".$references_r[1]."')\" /><img src=\"../images/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" border=\"0\" /></a></pre></li>"; 
	  	$conText .= "#".$references_r[1];
	  } 
	  ?>
      </ol>
        <span id="sprytextarea3">
        <textarea name="references" id="references" cols="45" rows="1" style="visibility:hidden; display:none"><?php if($conText) echo $conText; ?></textarea>
        <span class="textareaRequiredMsg">Add at least one.</span></span></div></td>
    </tr>
    <tr>
      <td align="left" valign="middle">&nbsp;</td>
      <td align="left" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" valign="middle">&nbsp;</td>
      <td align="left" valign="top"><input name="save" type="submit" id="save" value="Save   " class="btSave" />&nbsp;<input name="continue" type="submit" id="continue" value="Save &amp; Continue   " class="btSaveContinue" />&nbsp; or &nbsp;<a href="<?php echo BASEURL;?>system/index.php?selctCat=<?php echo $crsdata[1]; ?>">Cancel</a></td>
    </tr>
  </table>
</form></td>
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
<?php db_disconnect(); ?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var sprytextarea2 = new Spry.Widget.ValidationTextarea("sprytextarea2");
var sprytextarea3 = new Spry.Widget.ValidationTextarea("sprytextarea3");
</script>
</body>
</html>
