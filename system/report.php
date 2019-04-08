<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$bar_menu = "home";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php require(INC_DIR."header_include.php"); ?>
</head>

<body>
<fieldset style="padding:20px">
  <legend>Uploading Report</legend>
  <?php
	 require($_REQUEST['file']); 
  ?>
</fieldset>
</body>
</html>