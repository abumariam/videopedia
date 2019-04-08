<?php
session_start();
require('config.php');
db_connect();
$bar_menu = "home";
$crsID = $_REQUEST['crsid'];
$_REQUEST['vidorder'] ? $vidOrder = $_REQUEST['vidorder'] : $vidOrder = 1;
$crsname = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname` FROM `vb_courses` WHERE `crsid` = $crsID"));
$vidList_q = mysqli_query($db,"SELECT `vidorder`, `vidtitle` FROM `vb_videos` WHERE `crsid_fk` = $crsID ORDER BY `vidorder` ASC");
$video_q = mysqli_query($db,"SELECT `vidtitle`, `vidurl`, `vidorder`, `watch`, `author`, `desc`, `duration`,`vidid`,`feedback` FROM `vb_videos` WHERE `crsid_fk` = $crsID AND `vidorder` = $vidOrder");
$video_r = mysqli_fetch_row($video_q);
$watchcount = $video_r[3]+1;
$incWatch = mysqli_query($db,"UPDATE `vb_videos` SET `watch` = '$watchcount' WHERE `crsid_fk` = $crsID AND `vidorder` = $vidOrder;");
$maxorder = mysqli_fetch_row(mysqli_query($db,"SELECT MAX(`vidorder`), MIN(`vidorder`) FROM `vb_videos` WHERE `crsid_fk` = $crsID"));
$video_r[2] < $maxorder[0]  ? $next = $video_r[2] + 1 : $next = 0;
$video_r[2] > $maxorder[1] ? $back = $video_r[2] - 1 : $back = 0;

$questions_q = mysqli_query($db,"SELECT `qstid`,`question`, `bloomlevel`, `answer`, `choices` FROM `vb_questions` WHERE `videoid_fk` = ".$video_r[7]);

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
        <td width="300" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="12" height="38" class="menuTopLeft">&nbsp;</td>
            <td align="left" valign="middle" class="menuTopBG"><div id="menuHeader">Videos List</div></td>
            <td width="12" class="menuTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="menuMidLeft">&nbsp;</td>
            <td class="menuMidBG"><br />
            <?php while($vidList_r = mysqli_fetch_row($vidList_q)){ if($vidList_r[0] == $vidOrder) echo "<div class=\"itemDivNoLink\">".$vidList_r[1]."</div>";
	  else { ?>
      <a href="openbook.php?crsid=<?php echo $crsID."&vidorder=".$vidList_r[0];?>"><div class="itemDiv"><?php echo $vidList_r[1]; ?></div></a><?php }} ?></td>
            <td class="menuMidRight">&nbsp;</td>
          </tr>
          <tr>
            <td height="44" class="menuBotLeft">&nbsp;</td>
            <td class="menuBotBG">&nbsp;</td>
            <td class="menuBotRight">&nbsp;</td>
          </tr>
        </table></td>
        <td width="700" align="right" valign="top"><table width="99%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="13" height="36" class="bodyTopLeft">&nbsp;</td>
            <td align="left" valign="middle" class="bodyTopBG"><div id="bodyHeader"><a href="<?php echo BASEURL;?>">Home </a> /  <?php echo $crsname[0];?></div></td>
            <td width="13" class="bodyTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="bodyMidLeft">&nbsp;</td>
            <td align="left" valign="top" class="bodyMidBG"><h3><?php echo $video_r[2]."- ".stripslashes($video_r[0]);?></h3>
              <p style="text-align:center;">
              <object width="600" height="362"><param name="movie" value="<?php echo $video_r[1];?>?fs=1&amp;hl=en_US&amp;color1=0x006699&amp;color2=0x54abd6"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="<?php echo $video_r[1];?>?fs=1&amp;hl=en_US&amp;color1=0x006699&amp;color2=0x54abd6" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="600" height="362" style="border:1px dotted #999;"></embed></object>
              </p> 
              <div id="viewsDiv" style="display: none"><strong>Views:</strong> <?php echo $video_r[3]; ?></div>
                    <?php if(!empty($video_r[4])){ ?><div id="authDiv" style="display: none"><strong>Author:</strong> <?php echo $video_r[4]; ?></div><?php } if(!empty($video_r[5])){ ?>
                    <div id="descDiv"><strong>Description:</strong>  <?php $str = stripslashes($video_r[5]); if(strlen($str) <= 1000) echo $str; else echo substr($str,0,1000)."..."; ?></div><?php } ?>

                    <div id="durationDiv"><strong>Duration:</strong>  <?php echo $video_r[6]." Seconds"; ?></div>
                    <br>
                               <?php if(mysqli_num_rows($questions_q) > 0){ ?>
                                <strong>Questions:</strong>
                                <br>
                                 <form>
                                 <ol>
                                 	<?php 
									 $qstcount = 1;
									 while($questions_r = mysqli_fetch_row($questions_q)){
										 ?>
                                	<li>
                                	<?php echo($questions_r[1]."<br>"); 
									
										 $charray = ["a","b","c","d","e"];
										$choices = explode("#",$questions_r[4]);
									$count = 0;
										 foreach($choices as $choice){
										?>
                               <label class="radio-inline">
  <input class="form-check-input" type="radio" name="qstChoices_<?php echo $qstcount; ?>" id="qstChoices_<?php echo $qstcount; ?>" value="<?php echo $charray[$count]; ?>"> <?php echo $choice; ?>
  </label>
  <?php $count++;}  ?>
									 <br>
</li>
                                <?php $qstcount++;} ?>
                                </ol>
                                <button class="btn btn-primary" type="submit">Submit</button>

                                 </form>  
                        <?php } ?>                    
              </td>
            <td class="bodyMidRight">&nbsp;</td>
          </tr>
          <tr>
            <td height="47" class="bodyBotLeft">&nbsp;</td>
            <td class="bodyBotBG"><table width="100%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                    <td width="100"><?php if($back != 0){ ?><input name="previous" type="button" value="Previous" class="btPrevios" onclick="self.location='openbook.php?crsid=<?php echo $crsID."&vidorder=".$back;?>'" /><?php } else echo"&nbsp;"; ?></td>
                    <td width="521">&nbsp;</td>
                    <td width="100"><?php if($next != 0){ ?><input name="next" type="button" value="Next" class="btNext" onclick="self.location='openbook.php?crsid=<?php echo $crsID."&vidorder=".$next;?>'" /><?php } else echo"&nbsp;"; ?></td>
                  </tr>
            </table></td>
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

<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal"></button>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Feedback</h4>
      </div>
      <div class="modal-body">
        <p><?php echo $video_r[8]; ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
</body>
</html>
