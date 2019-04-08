<?php 
$catList_q = mysqli_query($db,"SELECT * FROM `vb_categories` WHERE 1");

?>
<script language="javascript">
function showHideDiv(divID){
	var x = document.getElementById(divID);
	if(x.style.display == "none") x.style.display = "block";
	else x.style.display = "none";
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="12" height="38" class="menuTopLeft">&nbsp;</td>
            <td align="left" valign="middle" class="menuTopBG"><div id="menuHeader">Course Category</div></td>
            <td width="12" class="menuTopRight">&nbsp;</td>
          </tr>
          <tr>
            <td class="menuMidLeft">&nbsp;</td>
            <td class="menuMidBG"><br />
            <?php 
			
			while($catList_r = mysqli_fetch_row($catList_q)){
				$books_q = mysqli_query($db,"SELECT `crsid`, `crsname` FROM `vb_courses` WHERE `fk_catid` = $catList_r[0] ORDER BY `order` ASC");
				echo "<div class=\"itemDiv2\" onclick=\"showHideDiv('div$catList_r[0]')\">".$catList_r[1]."</div><div id=\"div$catList_r[0]\" style=\"display:none\">";
				if(mysqli_num_rows($books_q) != 0){
				while($books_r = mysqli_fetch_row($books_q)){ 
	  $videos = mysqli_num_rows(mysqli_query($db,"SELECT `vidid`, `vidtitle`, `vidurl` FROM `vb_videos` WHERE `crsid_fk` = $books_r[0] ORDER BY `vidorder` ASC"));
	  if($videos == 0) echo "<div class=\"itemDivNoLink\">".$books_r[1]."</div>";
	  else { ?>
      <a href="<?php echo BASEURL; ?>openbook.php?crsid=<?php echo $books_r[0]; ?>"><div class="itemDiv"><?php echo $books_r[1]; ?></div></a><?php }}} else {echo "<div style=\"color:#CCC; padding:5px\">There is no books</div>";}echo "</div><br />";} ?></td>
            <td class="menuMidRight">&nbsp;</td>
          </tr>
          <tr>
            <td height="44" class="menuBotLeft">&nbsp;</td>
            <td class="menuBotBG">&nbsp;</td>
            <td class="menuBotRight">&nbsp;</td>
          </tr>
        </table>