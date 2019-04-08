<?php
session_start();
require('config.php');
db_connect();
$bar_menu = "home";
$mostWatch_q = mysqli_query($db,"SELECT `crsid_fk`, `vidtitle`, `vidorder` FROM `vb_videos` ORDER BY `watch` DESC LIMIT 0 , 5");
$mostRecent_q = mysqli_query($db,"SELECT `crsid_fk`, `vidtitle`, `vidorder` FROM `vb_videos` ORDER BY `modify_date` DESC LIMIT 0 , 5");
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
        <td width="250" align="left" valign="top"><?php require(INC_DIR."leftside.php");?></td>
        <td width="750" align="right" valign="top"><table width="99%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="13" height="36" class="bodyTopLeft">&nbsp;</td>
            <td align="left" valign="middle" class="bodyTopBG"><div id="bodyHeader">Home</div></td>
            <td width="13" class="bodyTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="bodyMidLeft">&nbsp;</td>
            <td class="bodyMidBG"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="54%" align="left" valign="top" class="nav_path">&nbsp;</td>
            <td width="46%" align="right" valign="top" class="nav_path" style="border-bottom:1px solid #99CC00; padding-right:5px">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="left" valign="top" style="background:url(<?php echo BASEURL; ?>images/homeimg1.jpg) 100px right no-repeat; border-bottom:1px solid #039">
            <?php if($_REQUEST['loginVulate']){ ?><div id="popDiv" class="errorDiv"><div style="text-align:right"><a href="javascript:closeAd('popDiv')"><img src="<?php echo BASEURL; ?>images/close.gif" border=0 /></a></div><br /><!-- Message Start -->Username or password is invalid!<!-- Message End --></div><script>adCount=0;adTime=10;showAd();</script><?php } ?>
            <h3>Why Video Courses!</h3>
              <ul style="padding-left:15px">
              <li>Learning from any where</li>
              <li>Learning many times and at any time</li>
              <li>Learning on your own pace</li>
              <li>Solving the problem of lack of staff</li>
              <li>Offering learning for large learners communities</li>
              <li>Saving time for busy learners</li>
              <li>Learning English from native speakers</li>
              <li>Convergence of theory and practice</li>
              <li>Learning within the problem context</li>
              </ul><br />				</td>
            </tr>
          <tr>
            <td align="left" valign="top" style="background:url(<?php echo BASEURL."images"; ?>/td_separator.jpg) right center no-repeat"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="186" height="28" class="news_header">Most Recent Videos</td>
                </tr>
                <tr>
                  <td align="left" valign="top"><ul type="square">
                      <?php while($mostRecent_r = mysqli_fetch_row($mostRecent_q)){
					  $crsName1 = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname` FROM `vb_courses` WHERE `crsid` = $mostRecent_r[0]"));
					  ?>
                      <li><?php echo $crsName1[0]." / ".$mostRecent_r[1]; ?> ( <a href="openbook.php?crsid=<?php echo $mostRecent_r[0]."&vidorder=".$mostRecent_r[2];?>">Show</a>)</li>
                      <?php } ?>
                  </ul></td>
                </tr>
            </table></td>
            <td align="left" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="186" height="28" class="news_header">Most Watched Videos</td>
                </tr>
                <tr>
                  <td align="left" valign="top"><ul type="square">
                      <?php while($mostWatch_r = mysqli_fetch_row($mostWatch_q)){
					  $crsName = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname` FROM `vb_courses` WHERE `crsid` = $mostWatch_r[0]"));
					  ?>
                      <li><?php echo $crsName[0]." / ".$mostWatch_r[1]; ?> ( <a href="openbook.php?crsid=<?php echo $mostWatch_r[0]."&vidorder=".$mostWatch_r[2];?>">Show</a>)</li>
                      <?php } ?>
                  </ul></td>
                </tr>
            </table></td>
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
</body>
</html>
