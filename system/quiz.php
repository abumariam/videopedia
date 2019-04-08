<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$bar_menu = "home";

$crsid = $_REQUEST['crsid'];
$vidid = $_REQUEST['vidid'];
$bloomlevels = ["Remembering"=>"Recall or retrieve previous learned information","Understanding"=>"Comprehending the meaning, translation, interpolation, and interpretation of instructions and problems. State a problem in one's own words","Applying"=>"Use a concept in a new situation or unprompted use of an abstraction. Applies what was learned in the classroom into novel situations in the work place","Analyzing"=>"Separates material or concepts into component parts so that its organizational structure may be understood. Distinguishes between facts and inferences","Evaluating"=>"Make judgments about the value of ideas or materials","Creating"=>"Builds a structure or pattern from diverse elements. Put parts together to form a whole, with emphasis on creating a new meaning or structure"];
$levelStatus = ["Remembering"=>"no","Understanding"=>"no","Applying"=>"no","Analyzing"=>"no","Evaluating"=>"no","Creating"=>"no"];

// add new video
if(isset($_REQUEST['save'])){
	$question = trim($_REQUEST['question']);
	$bloomlevel = trim($_REQUEST['bloomlevel']);
	$answer = trim($_REQUEST['answer']);
	$qstChoices = trim($_REQUEST['qstChoices'],"#");

	$insMamber = mysqli_query($db,"INSERT INTO `vb_questions` (`question`, `bloomlevel`, `choices`, `answer`, `videoid_fk`, `crsid_fk`) VALUES ('$question', '$bloomlevel', '$qstChoices','$answer','$vidid','$crsid');");


}

// select inserted data
	$crsdata = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname` FROM `vb_courses` WHERE `crsid` = $crsid"));
	$viddata = mysqli_fetch_row(mysqli_query($db,"SELECT `vidtitle`, `vidorder`, `vidurl` FROM `vb_videos` WHERE `vidid` = $vidid"));
	$questions_q = mysqli_query($db,"SELECT `question`, `bloomlevel`, `answer`, `choices` FROM `vb_questions` WHERE `videoid_fk` = $vidid");
while($questions_r = mysqli_fetch_row($questions_q)){
	$levelStatus[$questions_r[1]] = "yes";
	
}

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
            <td align="left" valign="middle" class="bodyTopBG"><div id="bodyHeader"><a href="<?php echo BASEURL;?>">Home </a> / <a href="<?php echo BASEURL;?>system/index.php?selctCat=<?php echo $qstdata[1]; ?>">Courses</a> / <?php echo $qstdata[0]; ?></div></td>
            <td width="13" class="bodyTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="bodyMidLeft">&nbsp;</td>
            <td align="left" valign="top" class="bodyMidBG">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="left" valign="top"><h3>Quiz - Questions</h3>
              <hr /><br />
<?php if($dataSaved){ ?>
<div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br />
<!-- Message Start -->Saving information is done successfully!<!-- Message End --></div>
<script>adCount=0;adTime=10;showAd();</script>
<?php } ?>
<br />

<form id="form1" name="form1" method="post" action="">
<!--<input id="qstid" name="qstid" type="hidden" value="<?php //echo $qstid; ?>" />-->  
<table width="100%" border="0" cellspacing="3" cellpadding="0">
    
    <tr class="record0">
      <td width="16%" class="fieldLabel">Video</td>
      <td width="84%" align="left" valign="top"><?php echo $viddata[0]; ?></td>
    </tr>
    
    <tr class="record0">
      <td class="fieldLabelReq">Bloom's Taxonomy Level</td>
      <td align="left" valign="top"><span id="spryselect2">
        <select name="bloomlevel" id="bloomlevel">
          <option selected="selected">&lt;Select&gt;</option>
          <?php foreach($bloomlevels as $level=>$desc){ ?>
          <option value="<?php echo $level;?>" <?php if($qstdata[5] == $level) echo "selected"; ?>><?php echo $level; ?></option>
          <?php } ?>
        </select>
        <span class="selectRequiredMsg"></span></span></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Question</td>
      <td align="left" valign="top"><textarea name="question" id="question" cols="50" rows="3"></textarea></td>
    </tr>
    
    <tr class="record0">
      <td class="fieldLabelReq">Choices</td>
      <td align="left" valign="top"><input name="addChoice" type="text" id="addChoice" size="40" class="inpuText" />
        <input type="button" name="addChoiceBut" id="addChoiceBut" value="Add" onclick="addItem('addChoice','_','qstChoices','choicesList')" />
        <div style="border:1px dotted #CCCCCC; width:550px"><ol type="A" id="choicesList">
          
          </ol>
          <span id="sprytextarea1">
            <textarea name="qstChoices" id="qstChoices" cols="45" rows="1" style="visibility:hidden; display:none"></textarea>
            <span class="textareaRequiredMsg">Add at least one.</span></span></div></td>
    </tr>
    <tr class="record0">
      <td class="fieldLabelReq">Correct Answer</td>
      <td align="left" valign="top"><span id="spryselect1">
        <select name="answer" id="answer">
          <option selected="selected">&lt;Select&gt;</option>
          <option value="a" >A</option>
          <option value="b" >B</option>
          <option value="c" >C</option>
          <option value="d" >D</option>
          <option value="e" >E</option>
        </select>
        <span class="selectRequiredMsg"></span></span></td>
    </tr>
    <tr>
      <td align="left" valign="middle">&nbsp;</td>
      <td align="left" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" valign="middle">&nbsp;</td>
      <td align="left" valign="top"><input name="save" type="submit" id="save" value="Save   " class="btSave" />&nbsp; or &nbsp;<a href="<?php echo BASEURL;?>system/videos.php?crsid=<?php echo $crsid; ?>">Cancel</a></td>
    </tr>
  </table>
</form></td>
          </tr>
          
        </table>
           <br>
           <h2>Bloom's Taxonomy</h2>
           <table width="100%" border="0" cellspacing="5" cellpadding="0">
  <tbody>
   <?php
	  foreach($bloomlevels as $level=>$desc){
		  
		  ?>
    <tr>
     <td align="left" valign="middle"><img src="../images/<?php if($levelStatus[$level] == "yes") echo "good"; else echo "wearn"; ?>.png" width="34" height="36" alt=""/></td>
      <td align="left" valign="middle"><?php echo "<b>".$level.":</b>"; ?> </td>
      <td align="left" valign="middle"><?php echo $desc; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
           </td>
            <td class="bodyMidRight">&nbsp;</td>
          </tr>
          <tr>
            <td height="47" class="bodyBotLeft">&nbsp;</td>
            <td class="bodyBotBG">&nbsp; </td>
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
