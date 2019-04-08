<table width="100%" border="0" cellspacing="0" cellpadding="0" id="bannerTable">
  <tr>
    <td width="8" height="110">&nbsp;</td>
    <td height="110"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" height="80" align="left" valign="middle"><a href="<?php echo BASEURL; ?>"><div id="logoDiv"></div></a></td>
        <td width="50%" height="80" align="right" valign="bottom"><?php if($_SESSION['person']){ ?>
          <div style="margin-bottom:17px;"><strong>Welcome! </strong><span class="nav_path"><?php echo $_SESSION['pname']; ?> [<a href="<?php echo BASEURL; ?>system/index.php">Admin CPanel</a>]</span> <a href="<?php echo BASEURL; ?>inc/logout.php"><img src="<?php echo BASEURL; ?>images/logout.png" alt="Logout" width="76" height="25" border="0" /></a></div>
          <?php } else { ?>
          <form id="form2" name="form2" method="post" action="<?php echo BASEURL; ?>inc/member.php">
            <table width="300" border="0" cellspacing="5" cellpadding="0" style="margin-bottom:17px">
              <tr>
                <td align="left" valign="middle" style="border-left:3px solid #333; padding-left:3px">Username</td>
                <td align="left" valign="middle" style="border-left:3px solid #333; padding-left:3px">Password</td>
                <td align="left" valign="middle">&nbsp;</td>
                </tr>
              <tr>
                <td align="left" valign="middle"><span id="sprytextfield1">
                  <input name="username" type="text" id="username" size="15" class="inpuText" />
                  <span class="textfieldRequiredMsg"></span></span></td>
                <td align="left" valign="middle"><span id="sprytextfield2">
                  <input name="password" type="password" id="password" size="15" class="inpuText" />
                  <span class="textfieldRequiredMsg"></span></span></td>
                <td align="left" valign="middle"><input type="submit" name="login" id="login" value="Login" class="loginBut" /></td>
                </tr>
              </table>
            </form>
          <?php } ?>
          </td>
      </tr>
    </table></td>
    <td width="8" height="110">&nbsp;</td>
  </tr>
</table>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
</script>
