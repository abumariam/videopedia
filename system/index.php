<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$bar_menu = "home";

$insertedBefore = 0;
$canotDel = 0;
$noCatname = 0;
// add new book
if($_REQUEST['save']){

$bookName = trim($_REQUEST['bookName']);
$crsCode = trim($_REQUEST['crsCode']);
$bookOrder = $_REQUEST['bookOrder'];
$crsIndex = $_REQUEST['hideCrsId'];
$hideOrder = $_REQUEST['hideOrder'];
$bookCat = $_REQUEST['bookCat'];
if($crsIndex != ""){
	if($hideOrder > $bookOrder){
		$ordersAfter = mysqli_query($db,"SELECT `crsid`, `order` FROM `vb_courses` WHERE `order` >= $bookOrder AND `fk_catid` = $bookCat");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]+1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_courses` SET `order` = '$incrsOrder' WHERE `crsid` = $ordersAfter_r[0] AND `fk_catid` = $bookCat AND `order` < $hideOrder;");
		}
	} else if($hideOrder < $bookOrder){
		$ordersAfter = mysqli_query($db,"SELECT `crsid`, `order` FROM `vb_courses` WHERE `fk_catid` = $bookCat AND `order` <= $bookOrder");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]-1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_courses` SET `order` = '$incrsOrder' WHERE `crsid` = $ordersAfter_r[0] AND `fk_catid` = $bookCat AND `order` > $hideOrder;");
		}
	}
	$updatBook = mysqli_query($db,"UPDATE `vb_courses` SET `crsname` = '$bookName', `order` = '$bookOrder', `modify_date` = '".date("Y-m-d")."', `crscode` = '$crsCode' WHERE `crsid` = $crsIndex;");
	
}
else {
	$insBefore = mysqli_num_rows(mysqli_query($db,"SELECT `crsid` FROM `vb_courses` WHERE `crsname` LIKE '$bookName'"));
	if($insBefore == 0){
		$maxorder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`order`) FROM `vb_courses` WHERE  `fk_catid` = $bookCat"));
		if($bookOrder <= $maxorder[0]){
			$ordersAfter = mysqli_query($db,"SELECT `crsid`, `order` FROM `vb_courses` WHERE `order` >= $bookOrder AND `fk_catid` = $bookCat");
			while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
				$incrsOrder = $ordersAfter_r[1]+1;
				$chngOrder = mysqli_query($db,"UPDATE `vb_courses` SET `order` = '$incrsOrder' WHERE `crsid` = $ordersAfter_r[0]");
			}
		}
		$insertNew = mysqli_query($db,"INSERT INTO `vb_courses` (`crsname`, `order`, `modify_date`, `fk_catid`, `crscode`) VALUES ('$bookName', '$bookOrder', '".date("Y-m-d")."', $bookCat, '$crsCode');");
		
		
	}
	else $insertedBefore = 1;
	}
}
// delete book
if($_REQUEST['actionName'] == "delete" && isset($_REQUEST['recordNo'])){
	$recordID = $_REQUEST['recordNo'];
	$bCat = $_REQUEST['bCat'];
	$thereAreVid = mysqli_fetch_row(mysqli_query($db,"SELECT COUNT(`vidid`) FROM `vb_videos` WHERE `crsid_fk` = $recordID"));
	if($thereAreVid[0] != 0) $canotDel = 1;
	else {
		$delBookOrder = mysqli_fetch_row(mysqli_query($db,"SELECT `order` FROM `vb_courses` WHERE `crsid` = $recordID"));
		$delRecord = mysqli_query($db,"DELETE FROM `vb_courses` WHERE `crsid` = $recordID");
		$ordersAfter = mysqli_query($db,"SELECT `crsid`, `order` FROM `vb_courses` WHERE `fk_catid` = $bCat AND `order` > $delBookOrder[0]");
		while($ordersAfter_r = mysqli_fetch_row($ordersAfter)){
			$incrsOrder = $ordersAfter_r[1]-1;
			$chngOrder = mysqli_query($db,"UPDATE `vb_courses` SET `order` = '$incrsOrder' WHERE `crsid` = $ordersAfter_r[0];");
		}
	}
}

// select book data
if($_REQUEST['actionName'] == "edit"){
	$spRecord = $_REQUEST['recordNo'];
	$bCat = $_REQUEST['bCat'];
	$editRecprd = mysqli_query($db,"SELECT `crsid`, `crsname`, `order`, `fk_catid`, `crscode` FROM `vb_courses` WHERE `crsid` = $spRecord");
	$editRecprd_r = mysqli_fetch_row($editRecprd);
}

// change book order up
if($_REQUEST['actionName'] == "chngOrderDown"){
	$spBook = $_REQUEST['recordNo'];
	$bCat = $_REQUEST['bCat'];
	$nextCrs = mysqli_fetch_row(mysqli_query($db,"SELECT `crsid` FROM `vb_courses` WHERE `fk_catid` = $bCat AND `order` = (SELECT `order` FROM `vb_courses` WHERE `crsid` = $spBook)+1"));
	$chngOrder1 = mysqli_query($db,"UPDATE `vb_courses` SET `order` = `order`-1 WHERE `crsid` = $nextCrs[0];");
	$chngOrder2 = mysqli_query($db,"UPDATE `vb_courses` SET `order` = `order`+1 WHERE `crsid` = $spBook;");
}

// change book order down
if($_REQUEST['actionName'] == "chngOrderUp"){
	$spBook = $_REQUEST['recordNo'];
	$bCat = $_REQUEST['bCat'];
	$previousCrs = mysqli_fetch_row(mysqli_query($db,"SELECT `crsid` FROM `vb_courses` WHERE `fk_catid` = $bCat AND `order` = (SELECT `order` FROM `vb_courses` WHERE `crsid` = $spBook)-1"));
	$chngOrder1 = mysqli_query($db,"UPDATE `vb_courses` SET `order` = `order`+1 WHERE `crsid` = $previousCrs[0];");
	$chngOrder2 = mysqli_query($db,"UPDATE `vb_courses` SET `order` = `order`-1 WHERE `crsid` = $spBook;");
}

// update books order

if($_REQUEST['reorder']){
	$id_array = explode("#",trim($_REQUEST['listids'],"#"));
	$bkCat = $_REQUEST['bkCat'];
	$maxOrd = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`order`) FROM `vb_courses` WHERE `fk_catid` = $bkCat"));
	$updatedRecords = array();
	foreach($id_array as $value){
		$textbox = $_REQUEST['ordr_'.$value];
		$oldvalue = mysqli_fetch_row(mysqli_query($db,"SELECT `order` FROM `vb_courses` WHERE `crsid` = $value"));
		if($textbox > $maxOrd[0]) $textbox = $maxOrd[0];
		
		$oldid = mysqli_fetch_row(mysqli_query($db,"SELECT `crsid` FROM `vb_courses` WHERE `fk_catid` = $bkCat AND `order` = $textbox"));
		
		if($value != $oldid[0] && !in_array($value,$updatedRecords)){
			$updateother = mysqli_query($db,"UPDATE `vb_courses` SET `order` = $oldvalue[0] WHERE `crsid` = $oldid[0];");
			$updatethis = mysqli_query($db,"UPDATE `vb_courses` SET `order` = $textbox WHERE `crsid` = $value;");
			array_push($updatedRecords,$oldid[0]);
		}
		
	}	
}

// update books categories

if($_REQUEST['addtocat']){
	$id_array = explode("#",trim($_REQUEST['chcksid'],"#"));
	$category = $_REQUEST['category'];
	$catName = trim($_REQUEST['otherInput']);
		
	if($category == "other"){
		if($catName != ""){
			$insBefore = mysqli_num_rows(mysqli_query($db,"SELECT `catid` FROM `vb_categories` WHERE `catname` LIKE '$catName'"));
			if($insBefore == 0){
				$insertCat = mysqli_query($db,"INSERT INTO `vb_categories` (`catname`) VALUES ('$catName');");
				if($insertCat) $category = mysql_insert_id();
			}
			foreach($id_array as $value){
				$xorder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`order`) FROM `vb_courses` WHERE  `fk_catid` = $category"));
				$bbOrder = $xorder[0]+1;
				$updateother = mysqli_query($db,"UPDATE `vb_courses` SET `fk_catid` = '$category', `order` = '$bbOrder' WHERE `crsid` = $value;");
			}
		}
		else $noCatname = 1;
	}
	else{
		foreach($id_array as $value){
			$xorder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`order`) FROM `vb_courses` WHERE  `fk_catid` = $category"));
			$bbOrder = $xorder[0]+1;
			$updateother = mysqli_query($db,"UPDATE `vb_courses` SET `fk_catid` = '$category', `order` = '$bbOrder' WHERE `crsid` = $value;");
		}
	}
		
}

// select existed books
$_REQUEST['page'] ? $page = $_REQUEST['page'] : $page = 1;;
$start = ($page*20)-20;

if($_REQUEST['speCat'] && $_REQUEST['speCat'] != "all") $clause = "`fk_catid` = '".$_REQUEST['speCat']."'";
else if($_REQUEST['selctCat']){$clause = "`fk_catid` = '".$_REQUEST['selctCat']."'";$selectedCat = $_REQUEST['selctCat'];}
else if(isset($bCat)) {$clause = "`fk_catid` = '".$bCat."'";$selectedCat = $bCat;}
else if(isset($bkCat)) {$clause = "`fk_catid` = '".$bkCat."'";$selectedCat = $bkCat;}
else if(isset($bookCat)) {$clause = "`fk_catid` = '".$bookCat."'";$selectedCat = $bookCat;}
else if(isset($category)) {$clause = "`fk_catid` = '".$category."'";$selectedCat = $category;}
else $clause = "0";

$existed = mysqli_query($db,"SELECT `crsid`, `crsname`, `order`, `modify_date`, `crscode` FROM `vb_courses` WHERE ".$clause." ORDER BY `order` ASC LIMIT ".$start." , 20");
$existed_r = mysqli_num_rows($existed);
$num_q = mysqli_query($db,"SELECT COUNT(`crsid`), MAX(`order`) FROM `vb_courses` WHERE ".$clause);
$num_all = mysqli_fetch_row($num_q);
$pages = ceil($num_all[0]/20);

if(isset($_REQUEST['page']) && $pages < $page) echo"<script>self.location='index.php?selctCat=$selectedCat&page=$pages';</script>";

$allCatAll_q = mysqli_query($db,"SELECT *  FROM `vb_categories` ORDER BY `catname` ASC");
$allCat_q = mysqli_query($db,"SELECT *  FROM `vb_categories` ORDER BY `catname` ASC");
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
            <td align="left" valign="middle" class="bodyTopBG"><div id="bodyHeader"><a href="<?php echo BASEURL;?>">Home </a> / Add &amp; Modify Courses</div></td>
            <td width="13" class="bodyTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="bodyMidLeft">&nbsp;</td>
            <td align="left" valign="top" class="bodyMidBG">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="left" valign="top"><br />
<?php if($insertNew){ ?><div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Adding "<?php echo $bookName; ?>" course is done successfully!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } else if($insertedBefore == 1){ ?><div id="popDiv" class="alertDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->This course was added before!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } else if($updatBook){ ?><div id="popDiv" class="confDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Modifing "<?php echo $bookName; ?>" course ionformation is done successfully!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } if($canotDel == 1){ ?><div id="popDiv" class="errorDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->You can't delete this course, because it contains <strong><?php echo $thereAreVid[0]; ?></strong> video inside!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } 
if(isset($_REQUEST['speCat']) && $_REQUEST['speCat'] != "all" || isset($selectedCat)){
?>
             <fieldset style="width:600px; padding:10px">
              <legend><strong>Add New Course</strong></legend><br />
              <form id="form1" name="form1" method="post" action="">
                <table width="100%" border="0" cellspacing="3" cellpadding="0">
                  <tr>
                    <td class="fieldLabelReq">Course Code</td>
                    <td><span id="sprytextfield3">
                    <input name="crsCode" type="text" id="crsCode" size="10" class="inpuText" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[4]; ?>" />
                    <span class="textfieldRequiredMsg">Required.</span></span>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" class="fieldLabelReq">Course Title</td>
                    <td width="75%"><span id="sprytextfield1">
                      <input name="bookName" type="text" id="bookName" size="40" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[1]; ?>" class="inpuText" />
                      <span class="textfieldRequiredMsg">Required.</span></span></td>
                  </tr>
                  <tr>
                    <td class="fieldLabelReq">Course Order</td>
                    <td><span id="spryselect1">
                      <select name="bookOrder" id="bookOrder">
                        <option>&lt;Select&gt;</option>
                        <option value="1" <?php if($_REQUEST['actionName'] == "edit" && $editRecprd_r[2] == 1) echo "selected"; ?>>First</option>
                        <?php for($i = 2; $i <= $num_all[1]; $i++){ ?>
                        <option value="<?php echo $i; ?>" <?php if($_REQUEST['actionName'] == "edit" && $editRecprd_r[2] == $i) echo "selected"; ?>><?php echo $i; if($i == 2) echo " nd"; else if($i == 3) echo " rd"; else echo " th"; ?></option>
                        <?php } if($_REQUEST['actionName'] != "edit"){ ?><option value="<?php echo $num_all[1]+1; ?>">Last</option><?php } ?>
                      </select>
                      <span class="selectRequiredMsg">Required.</span></span></td>
                  </tr>
                  <tr>
                    <td height="10" colspan="2"></td>
                  </tr>
                  <tr>
                    <td>
                    <input type="hidden" name="hideCrsId" id="hideCrsId" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[0]; else echo ""; ?>" />
                    <input type="hidden" name="hideOrder" id="hideOrder" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[2]; else echo ""; ?>" />
                    <input type="hidden" name="bookCat" id="bookCat" value="<?php if($_REQUEST['actionName'] == "edit") echo $editRecprd_r[3]; else if($_REQUEST['speCat']) echo $_REQUEST['speCat']; else echo $selectedCat; ?>" />
                    </td>
                    <td align="left" valign="top" style="padding-bottom:10px"><input name="save" type="submit" id="save" class="btSave" value="Save     " />
                      <input type="button" name="cancel" id="cancel" class="btCancel" value="Finish    " onclick="self.location='index.php?<?php if($_REQUEST['speCat']) echo "speCat=".$_REQUEST['speCat']; else if(isset($selectedCat)) echo "speCat=".$selectedCat; ?>'" />                    </td>
                  </tr>
                </table>
                  </form>
              </fieldset>
              <?php } ?>
             <br />
             
              <fieldset style="padding:10px"><legend><form id="form2" name="form2" method="post" action="index.php">
                  <select name="speCat" id="speCat" style="font-size:16px; font-weight:bold; color:#06C" onchange="submit()">
                    <option value="all">-- Select Specific Category --</option>
                    <?php
                      while($allCatAll_r = mysqli_fetch_row($allCatAll_q)){?>
						<option value="<?php echo $allCatAll_r[0]; ?>" <?php if($_REQUEST['speCat'] && $_REQUEST['speCat'] == $allCatAll_r[0]) echo "selected"; else if($selectedCat == $allCatAll_r[0]) echo "selected"; ?>><?php echo $allCatAll_r[1];?></option>
				  <?php } ?>
                  </select>
                </form></legend><br /> <strong>Existed Courses</strong> (<?php echo $num_all[0]; ?>)<br />

                <?php if($noCatname == 1){ ?><div id="popDiv" class="alertDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Please enter "other" category name!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } if($page != 0 && $existed_r == 0){ ?><div class="infoDiv">No courses existed in this category, or you didn't select specific category! <br /> <br /></div><?php } else { ?>
                <br />
			
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #CCCCCC; border-right:1px solid #CCCCCC; margin-top:5px">
                <form action="" method="post" name="reorderForm">
                <tr>
                  <td width="10%" height="25" align="center" class="header">CRS Code</td>
                  <td width="45%" class="header">Course Title</td>
                  <td width="10%" class="header">Syllabus</td>
                  <td width="15%" class="header"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>Order</td>
                        <td width="50" align="center"><input name="reorder" type="submit" value=" " class="btReorder" /></td>
                      </tr>
                    </table></td>
                  <td width="10%" class="header"># Videos</td>
                  <td width="10%" class="header">Operation</td>
                </tr>
                <?php $count = $start+1; while($records = mysqli_fetch_row($existed)){ 
				$vidcount = mysqli_fetch_row(mysqli_query($db,"SELECT COUNT(`vidid`) FROM `vb_videos` WHERE `crsid_fk` = $records[0]"));
				?>
                <tr class="record0">
                  <td class="record"><input id="chck<?php echo $records[0];?>" name="chck<?php echo $records[0];?>" type="checkbox" value="<?php echo $records[0]; ?>" onclick="chckbox(this.id,'chcksid')" /> <label for="chck<?php echo $records[0];?>" style="font-weight:bold"><?php if($records[4]) echo $records[4];else echo "___"; ?></label></td>
                  <td class="record"><?php if($records[1]) echo $records[1]; else echo"N/A"; ?></td>
                  <td class="record">[ <a href="syllabus1.php?crsid=<?php echo $records[0]; ?>">Edit</a> ] &nbsp;<a href="syllabuspdf.php?crsid=<?php echo $records[0]; ?>" target="_blank"><img src="<?php echo BASEURL; ?>images/pdf_icon.gif" alt="PDF" width="22" height="23" border="0" title="Show PDF" /></a></td>
                  <td align="center" class="record"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><?php if($records[2] > 1){ ?><a href="javascript:carryOut('chngOrderUp','<?php echo $records[0];?>')"><img src="<?php echo BASEURL; ?>images/upld.png" width="16" height="15" border="0" alt="Up" title="Up" /></a>&nbsp;<?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if($records[2] < $num_all[1]){ ?>&nbsp;<a href="javascript:carryOut('chngOrderDown','<?php echo $records[0];?>')"><img src="<?php echo BASEURL; ?>images/down.png" width="16" height="15" border="0" alt="Down" title="Down" /></a><?php } ?></td>
                    <td width="50" align="center"><input name="ordr_<?php echo $records[0]; ?>" type="text" value="<?php echo $records[2]; ?>" size="3" maxlength="4" style="text-align:center" class="inpuText" /></td>
                  </tr>
                </table>
				  
				  </td>
                  <td class="record"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr><td width="50" align="center"><strong><?php echo $vidcount[0]; ?></strong></td>
                    <td><a href="videos.php?crsid=<?php echo $records[0]; ?>"><img src="<?php echo BASEURL; ?>images/editvideo.png" alt="Add / Edit" title="Add / Edit" width="22" height="22" border="0" /></a></td></tr></table>
</td>
                  <td align="center" class="record"><a href="javascript:carryOut('edit','<?php echo $records[0];?>')"><img src="<?php echo BASEURL; ?>images/editrecord.png" alt="Edit" title="Edit" width="22" height="22" border="0" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:carryOut('delete','<?php echo $records[0];?>')"><img src="<?php echo BASEURL; ?>images/delrecord.png" alt="Delete" title="Delete" width="22" height="22" border="0" /></a></td>
                </tr>
                <?php $count++; $idsqrr .= "#".$records[0];} ?>
                <input name="listids" type="hidden" value="<?php echo $idsqrr; ?>" />
              <input type="hidden" name="bkCat" id="bkCat" value="<?php if($_REQUEST['speCat']) echo $_REQUEST['speCat']; else echo $selectedCat; ?>" />
			</form>
            <form action="" method="post" name="catForm">
                <tr>
                  <td colspan="3" align="left" valign="middle" class="record" style="background-color:#FFFFF2">
                  
                  <img src="choose.gif" alt=" |_" width="30" height="25" align="left" /><strong>Transfer to: </strong><span id="spryselect2">
                    <select name="category" id="category" onchange="showHideField('otherInput','category')">
                      <option>-- Select Category --</option>
                      <?php
                      while($allCat_r = mysqli_fetch_row($allCat_q)){
						  if($_REQUEST['speCat'] && $_REQUEST['speCat'] == $allCat_r[0]) continue; 
						  else if($selectedCat == $allCat_r[0]) continue;
						  else echo "<option value=\"".$allCat_r[0]."\">".$allCat_r[1]."</option>";
					  }
						  ?>
                       <option value="other"><- Other -></option>   
                    </select>
                    <span class="selectRequiredMsg"></span></span>
                    <input name="otherInput" id="otherInput" type="text" size="30" value="" style="visibility:hidden;" /></td>
					<td colspan="3" align="right" valign="middle" style="border-top:1px solid #CCCCCC;background-color:#FFFFF2"><input type="submit" name="addtocat" id="addtocat" value="     Do Transfer" class="btDoAction" />
					<span id="sprytextfield2">
                    <input type="hidden" name="chcksid" id="chcksid" value="" />
                    <span class="textfieldRequiredMsg"> -Choose Courses</span></span>
                    &nbsp;&nbsp;</td>
                </tr>
			</form>
              </table>
              
              <br />
              <div style="text-align:center"><?php $prev = $page-1; $next = $page+1;  if($page != 1) echo "<a href=\"javascript:chngpos('$prev','index.php','form2')\" class=\"pageLink\">Previous</a>"; ?><strong> Page:</strong>                  
                  <?php for($i = 1 ; $i <= $pages ; $i++){if($i != $page) echo"<a href=\"javascript:chngpos('$i','index.php','form2')\" class=\"pageLink\">$i</a>"; else echo"<strong style=\"font-size:14px; color:#333333; margin-left:3px; padding:2px 5px 2px 5px; border:0px\">$i</strong>"; } ?>
                  <?php if($page != $pages) echo "<a href=\"javascript:chngpos('$next','index.php','form2')\" class=\"pageLink\">Next</a>"; ?>
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
<input type="hidden" name="bCat" id="bCat" value="<?php if($_REQUEST['speCat']) echo $_REQUEST['speCat']; else echo $selectedCat; ?>" />
</form>
<script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
//-->
</script>
<?php db_disconnect(); ?>
</body>
</html>
