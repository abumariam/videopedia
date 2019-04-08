<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$bar_menu = "home";

$insertedBefore = 0;
$canotDel = 0;
$crsId = $_REQUEST['crsid'];
$crsName_r = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname`, `fk_catid` FROM `vb_courses` WHERE `crsid` = $crsId"));
// add videos from file
require('_upload_doc.php');

// add new video
if($_REQUEST['save']){

if (!get_magic_quotes_gpc()) $vidTitle = addslashes(trim($_REQUEST['vidTitle']));
else $vidTitle = trim($_REQUEST['vidTitle']);
$vidOrder = $_REQUEST['vidOrder'];

// filtering url from un-wanted characters
if(strpos($_REQUEST['vidUrl'],"&") !== false) $length = strpos($_REQUEST['vidUrl'],"&");
else $length = strlen($_REQUEST['vidUrl']);
$vidUrl = trim(str_replace("watch?v=","v/",substr($_REQUEST['vidUrl'],0,$length)));

$vidIndex = $_REQUEST['hideVidId'];
$hideOrder = $_REQUEST['hideOrder'];

$author = trim($_REQUEST['author']);
$desc = trim($_REQUEST['desc']);
$feedback = trim($_REQUEST['feedback']);
// update existed
if($vidIndex != ""){
	if($hideOrder > $vidOrder){
		$ordersAfter = mysqli_query($db,"SELECT `vidid`, `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidorder` >= $vidOrder");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]+1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = '$incrsOrder' WHERE `crsid_fk` = $crsId AND `vidid` = $ordersAfter_r[0] AND `vidorder` < $hideOrder;");
		}
	} else if($hideOrder < $vidOrder){
		$ordersAfter = mysqli_query($db,"SELECT `vidid`, `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidorder` <= $vidOrder");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]-1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = '$incrsOrder' WHERE `crsid_fk` = $crsId AND `vidid` = $ordersAfter_r[0] AND `vidorder` > $hideOrder;");
		}
	}
	$updatBook = mysqli_query($db,"UPDATE `vb_videos` SET `vidtitle` = '$vidTitle', `vidorder` = '$vidOrder', `vidurl` = '$vidUrl', `modify_date` = '".date("Y-m-d H:i:s")."', `author` = '$author', `desc` = '$desc', `feedback` = '$feedback' WHERE `crsid_fk` = $crsId AND `vidid` = $vidIndex;");
	
}

// insert new
else {
	$insBefore = mysqli_num_rows(mysqli_query($db,"SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidurl` LIKE '$vidUrl'"));
	if($insBefore == 0){
		$maxorder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId"));
		if($vidOrder <= $maxorder[0]){
			$ordersAfter = mysqli_query($db,"SELECT `vidid`, `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidorder` >= $vidOrder");
			while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
				$incrsOrder = $ordersAfter_r[1]+1;
				$chngOrder = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = '$incrsOrder' WHERE `crsid_fk` = $crsId AND `vidid` = $ordersAfter_r[0]");
			}
		}
		$insertNew = mysqli_query($db,"INSERT INTO `vb_videos` (`crsid_fk`, `vidtitle`, `vidurl`, `vidorder`, `modify_date`) VALUES ('$crsId', '$vidTitle', '$vidUrl', '$vidOrder', '".date("Y-m-d H:i:s")."');");
		
		
	}
	else $insertedBefore = 1;
	}
}
// delete video
if($_REQUEST['actionName'] == "delete" && isset($_REQUEST['recordNo'])){
	$recordID = explode("-",$_REQUEST['recordNo']);
		$delVidOrder = mysqli_fetch_row(mysqli_query($db,"SELECT `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $recordID[0] AND `vidid` = $recordID[1]"));
		$delRecord = mysqli_query($db,"DELETE FROM `vb_videos` WHERE `crsid_fk` = $recordID[0] AND `vidid` = $recordID[1]");
		$ordersAfter = mysqli_query($db,"SELECT `vidid`, `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $recordID[0] AND `vidorder` > $delVidOrder[0]");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]-1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = $incrsOrder WHERE `crsid_fk` = $recordID[0] AND `vidid` = $ordersAfter_r[0];");
		}
}

// select video data
if($_REQUEST['actionName'] == "edit"){
	$spRecord = explode("-",$_REQUEST['recordNo']);
	$editRecprd = mysqli_query($db,"SELECT `crsid_fk`, `vidid`, `vidtitle`, `vidurl`, `vidorder`, `author`, `desc`, `feedback` FROM `vb_videos` WHERE `crsid_fk` = $spRecord[0] AND `vidid` = $spRecord[1]");
	$editRecprd_r = mysqli_fetch_row($editRecprd);
}

// change video order up
if($_REQUEST['actionName'] == "chngOrderDown"){
	$spVideo = explode("-",$_REQUEST['recordNo']);
	$nextCrs = mysqli_fetch_row(mysqli_query($db,"SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $spVideo[0] AND `vidorder` = (SELECT `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $spVideo[0] AND `vidid` = $spVideo[1])+1"));
	$chngOrder1 = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = `vidorder`-1 WHERE `crsid_fk` = $spVideo[0] AND `vidid` = $nextCrs[0];");
	$chngOrder2 = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = `vidorder`+1 WHERE `crsid_fk` = $spVideo[0] AND `vidid` = $spVideo[1];");
}

// change video order down
if($_REQUEST['actionName'] == "chngOrderUp"){
	$spVideo = explode("-",$_REQUEST['recordNo']);
	$nextCrs = mysqli_fetch_row(mysqli_query($db,"SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $spVideo[0] AND `vidorder` = (SELECT `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $spVideo[0] AND `vidid` = $spVideo[1])-1"));
	$chngOrder1 = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = `vidorder`+1 WHERE `crsid_fk` = $spVideo[0] AND `vidid` = $nextCrs[0];");
	$chngOrder2 = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = `vidorder`-1 WHERE `crsid_fk` = $spVideo[0] AND `vidid` = $spVideo[1];");
}

// update videos order

if($_REQUEST['reorder']){
	$id_array = explode("#",trim($_REQUEST['listids'],"#"));
	$maxOrd = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId"));
	$updatedRecords = array();
	foreach($id_array as $value){
		
		$textbox = $_REQUEST["ordr_".$value];
		$oldvalue = mysqli_fetch_row(mysqli_query($db,"SELECT `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidid` = $value"));
		
		if($textbox > $maxOrd[0]) $textbox = $maxOrd[0];

		$oldid = mysqli_fetch_row(mysqli_query($db,"SELECT `vidid` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidorder` = '$textbox'"));
		if($value != $oldid[0] && !in_array($value,$updatedRecords)){
			$updateother = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = '$oldvalue[0]' WHERE `crsid_fk` = $crsId AND `vidid` = $oldid[0];");
			$updatethis = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = '$textbox' WHERE `crsid_fk` = $crsId AND `vidid` = $value;");
			array_push($updatedRecords,$oldid[0]);
		}
		
	}	
}

// delete group of videos

if($_REQUEST['delVid']){
	$id_array = explode("#",trim($_REQUEST['chcksid'],"#"));
	foreach($id_array as $value){
		$delVidOrder = mysqli_fetch_row(mysqli_query($db,"SELECT `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidid` = $value"));
		$delRecord = mysqli_query($db,"DELETE FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidid` = $value");
		$ordersAfter = mysqli_query($db,"SELECT `vidid`, `vidorder` FROM `vb_videos` WHERE `crsid_fk` = $crsId AND `vidorder` > $delVidOrder[0]");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]-1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_videos` SET `vidorder` = $incrsOrder WHERE `crsid_fk` = $crsId AND `vidid` = $ordersAfter_r[0];");
		}
		
	}	
}

// delete all videos

if($_REQUEST['delAllVid']){
		$delRecord = mysqli_query($db,"DELETE FROM `vb_videos` WHERE `crsid_fk` = $crsId");
}

// select existed videos
$_REQUEST['page'] ? $page = $_REQUEST['page'] : $page = 1;;
$start = ($page*20)-20;
$existed = mysqli_query($db,"SELECT `crsid_fk`, `vidid`, `vidtitle`, `vidorder`, `vidurl` FROM `vb_videos` WHERE `crsid_fk` = $crsId ORDER BY `vidorder` ASC LIMIT ".$start." , 20");
$existed_r = mysqli_num_rows($existed);
$num_q = mysqli_query($db,"SELECT COUNT(`vidid`), MAX(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsId");
$num_all = mysqli_fetch_row($num_q);
$pages = ceil($num_all[0]/20);

if(isset($_REQUEST['page']) && $pages < $page) echo"<script>self.location='videos.php?crsid=$crsId&page=$pages';</script>";


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
            <td align="left" valign="middle" class="bodyTopBG"><div id="bodyHeader"><a href="<?php echo BASEURL;?>">Home </a> / <a href="<?php echo BASEURL;?>system/index.php?selctCat=<?php echo $crsName_r[1]; ?>">Courses</a> / <?php echo $crsName_r[0]; ?></div></td>
            <td width="13" class="bodyTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="bodyMidLeft">&nbsp;</td>
            <td align="left" valign="top" class="bodyMidBG">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="left" valign="top"><br />
<?php if($insertNew){ ?>
<div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Adding "<?php echo $vidTitle; ?>" Video is done successfully!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } 
            else if($insertedBefore == 1){ ?>
<div id="popDiv" class="alertDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->This &quot;<?php echo $vidTitle; ?>&quot; Video was added before!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } 
			else if($updatBook){ ?>
<div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Modifing "<?php echo $vidTitle; ?>" Video ionformation is done successfully!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top">
      <fieldset style="padding:10px; display:<?php if($_REQUEST['actionName'] == "edit") echo"none"; else echo"block"; ?>; width:700px">
        <legend><strong>Search  &amp; Add Videos</strong></legend><br />
        
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
          <tr>
            <td align="left" valign="top" class="newAnnounce"><br />
Yes you can search YouTube videos according to  keywords, then choose the apropriate ones to add to this course.</td>
          </tr>
          <tr>
            <td align="right" valign="bottom"><a href="browse/index.php?crsid=<?php echo $crsId; ?>"><div class="butStyle" style="width:90px; padding-top:5px; font-weight:bold">Start</div></a></td>
          </tr>
        </table>
      </fieldset><br />
      <fieldset style="padding:10px; display:<?php if($_REQUEST['actionName'] == "edit") echo"block"; else echo"none"; ?>; width:700px">
        <legend><strong>Add New Video</strong></legend><br />
        
        <form id="form1" name="form1" method="post" action="">
          <table width="100%" border="0" cellspacing="3" cellpadding="0">
            <tr>
              <td width="20%" class="fieldLabelReq">Video Title</td>
              <td width="80%"><span id="sprytextfield1">
                <label>
                  <input name="vidTitle" type="text" id="vidTitle" size="40" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[2]; ?>" class="inpuText" />
                  </label>
                <span class="textfieldRequiredMsg">Required.</span></span></td>
              </tr>
            
            <tr>
              <td class="fieldLabelReq">Video URL</td>
              <td><span id="sprytextfield2">
                <label>
                  <input name="vidUrl" type="text" id="vidUrl" size="50" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[3]; ?>" class="inpuText" />
                  </label>
                <span class="textfieldRequiredMsg">Required.</span><span class="textfieldInvalidFormatMsg">Invalid URL.</span></span></td>
              </tr>
            <tr>
              <td class="fieldLabelReq">Author</td>
              <td>
                <input name="author" type="text" id="author" size="30" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[5]; ?>" class="inpuText" /></td>
            </tr>
            <tr>
              <td class="fieldLabelReq">Description</td>
              <td>
                <textarea name="desc" id="desc" cols="65" rows="5"><?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[6]; ?></textarea></td>
            </tr>
            <tr>
              <td class="fieldLabelReq">Feedback</td>
              <td>
                <textarea name="feedback" id="feedback" cols="65" rows="5"><?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[7]; ?></textarea></td>
            </tr>
            <tr>
              <td class="fieldLabelReq">Video Order</td>
              <td><span id="spryselect1">
                <label>
                  <select name="vidOrder" id="vidOrder">
                    <option>&lt;Select&gt;</option>
                    <option value="1" <?php if($_REQUEST['actionName'] == "edit" && $editRecprd_r[4] == 1) echo "selected"; ?>>First</option>
                    <?php for($i = 2; $i <= $num_all[1]; $i++){ ?>
                    <option value="<?php echo $i; ?>" <?php if($_REQUEST['actionName'] == "edit" && $editRecprd_r[4] == $i) echo "selected"; ?>><?php echo $i; if($i == 2) echo " nd"; else if($i == 3) echo " rd"; else echo " th"; ?></option>
                    <?php } if($_REQUEST['actionName'] != "edit"){ ?><option value="<?php echo $num_all[1]+1; ?>">Last</option><?php } ?>
                    </select>
                  </label>
                <span class="selectRequiredMsg">Required.</span></span></td>
              </tr>
            <tr>
              <td height="10" colspan="2"></td>
              </tr>
            <tr>
              <td><label>
                <input type="hidden" name="hideVidId" id="hideVidId" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[1]; else echo ""; ?>" />
                <input type="hidden" name="hideOrder" id="hideOrder" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[4]; else echo ""; ?>" />
                <input type="hidden" name="crsid" id="crsid" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[0]; else echo $crsId; ?>" />
                </label></td>
              <td align="left" valign="top" style="padding-bottom:10px"><input name="save" type="submit" id="save" class="btSave" value="Save     " />
                <input type="button" name="cancel" id="cancel" class="btCancel" value="Finish    " onclick="self.location='videos.php?crsid=<?php echo $crsId; ?>'" />                    </td>
              </tr>
            </table>
          </form>
        </fieldset></td>
  </tr>
</table>
             <br />
             <fieldset style="padding:10px"><legend><strong>Existed Videos</strong> (<?php echo $num_all[0]; ?>)</legend>
              <?php if($page != 0 && $existed_r == 0){ ?>
                <br />
                <div class="infoDiv">No videos existed, you can search youtube and add videos look above.<br /><br /></div>
                <?php } else { ?>
                <br />
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #CCCCCC; border-right:1px solid #CCCCCC; margin-top:5px">
                <form action="" method="post" name="reprderForm">
                <tr>
                  <td width="8%" height="25" align="center" class="header">#</td>
                  <td width="60%" class="header">Video Title</td>
                  <td width="15%" class="header"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>Order</td>
                        <td width="50" align="center"><input id="reorder" name="reorder" type="submit" value=" " class="btReorder" /></td>
                      </tr>
                    </table>
                    </td>
                  <td width="7%" class="header">Quiz</td>
                  <td width="10%" class="header">Operation</td>
                </tr>
                
                <?php $idsqrr = "";$count = $start+1; while($records = mysqli_fetch_row($existed)){ 
				$vidcount = mysqli_fetch_row(mysqli_query($db,"SELECT COUNT(`vidid`) FROM `vb_videos` WHERE `crsid_fk` = $records[0]"));
				?>
                <tr class="record0">
                  <td class="record"><input id="chck<?php echo $records[1];?>" name="chck<?php echo $records[1];?>" type="checkbox" value="<?php echo $records[1]; ?>" onclick="chckbox(this.id,'chcksid')" /> <label for="chck<?php echo $records[1];?>" style="font-weight:bold"><?php echo $count; ?></label></td>
                  <td class="record"><?php if($records[2]) echo $records[2]; else echo"N/A"; ?></td>
                  <td align="center" class="record"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><?php if($records[3] > 1){ ?><a href="javascript:carryOut('chngOrderUp','<?php echo $records[0]."-".$records[1];?>')"><img src="<?php echo BASEURL; ?>images/upld.png" width="16" height="15" border="0" alt="Up" title="Up" /></a>&nbsp;<?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if($records[3] < $num_all[1]){ ?>&nbsp;<a href="javascript:carryOut('chngOrderDown','<?php echo $records[0]."-".$records[1];?>')"><img src="<?php echo BASEURL; ?>images/down.png" width="16" height="15" border="0" alt="Down" title="Down" /></a><?php } ?></td>
                    <td width="50" align="center"><input name="ordr_<?php echo $records[1]; ?>" type="text" value="<?php echo $records[3]; ?>" size="3" maxlength="4" style="text-align:center" class="inpuText" /></td>
                  </tr>
                </table>
                </td>
                  <td align="center" class="record"><a href="quiz.php?crsid=<?php echo $records[0]; ?>&vidid=<?php echo $records[1]; ?>" target="_blank"><img src="<?php echo BASEURL; ?>images/ask-question.png" alt="Show" title="Show" width="25" height="25" border="0" /></a></td>
                  <td align="center" class="record"><a href="javascript:carryOut('edit','<?php echo $records[0]."-".$records[1];?>')"><img src="<?php echo BASEURL; ?>images/editrecord.png" alt="Edit" title="Edit" width="22" height="22" border="0" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:carryOut('delete','<?php echo $records[0]."-".$records[1];?>')"><img src="<?php echo BASEURL; ?>images/delrecord.png" alt="Delete" title="Delete" width="22" height="22" border="0" /></a></td>
                </tr>
                <?php $count++; $idsqrr .= "#".$records[1];} ?>
               <input name="listids" type="hidden" value="<?php echo $idsqrr; ?>" />
              </form> 
               <tr>
                  <td colspan="5" align="left" valign="middle" class="record" style="background-color:#EEE">
                  <form action="" method="post" id="delForm" name="delForm">
                  <img src="choose.gif" alt=" |_" width="30" height="25" align="left" /><strong>Choose videos to: </strong>
                    <input type="hidden" name="chcksid" id="chcksid" value="" />
                    <input type="button" name="act01" id="act01" value="Delete    " onclick="confirmAct('Are you sure, you need to delete choosed videos!','delForm','delVid')" class="btDelete" />
                    <strong>Or</strong>
                    <input type="button" name="act02" id="act02" value=" Delete All" onclick="confirmAct('Are you sure, you need to delete all videos!','delForm','delAllVid')" class="butStyle" />
                  </form>
                    </td>
                </tr>
              </table>
              
              <br />
              <div style="text-align:center"><?php $prev = $page-1; $next = $page+1;  if($page != 1) echo "<a href=\"videos.php?crsid=$crsId&page=$prev\" class=\"pageLink\">Previous</a>"; ?><strong> Page:</strong>             <?php for($i = 1 ; $i <= $pages ; $i++){if($i != $page) echo"<a href=\"videos.php?crsid=$crsId&page=$i\" class=\"pageLink\">$i</a>"; else echo"<strong style=\"font-size:14px; color:#333333; margin-left:3px; padding:2px 5px 2px 5px; border:0px\">$i</strong>"; } ?>
                  <?php if($page != $pages) echo "<a href=\"videos.php?crsid=$crsId&page=$next\" class=\"pageLink\">Next</a>"; ?>
              </div>
              <?php } ?>
              </fieldset>
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
<script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "url");
//-->
</script>
<?php db_disconnect(); ?>
</body>
</html>
