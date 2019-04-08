<?php
#########################################################
# Arab Open University SASVB            		        #
#########################################################
#                                                       #
# Created by: Abdel Kawy  Abdel Hady                    #
#                                                       #
#########################################################
    session_start();
    if(!empty($_SESSION['person'])){
       unset($_SESSION['person']);
	   //session_unregister("person");
	   unset($_SESSION['pname']);
	  // session_unregister("pname");
    }
	require("../config.php");
	db_connect();
	$plogin = trim($_REQUEST['username']);
    $pwrd = trim($_REQUEST['password']);
	$res_q = mysqli_query($db,"SELECT `userid`, `name` FROM `vb_users` WHERE `username` = '$plogin' AND `password` = PASSWORD('$pwrd')");
	$res  = mysqli_fetch_array($res_q);

    if(mysqli_num_rows($res_q) == 0) {
		unset($_SESSION['person']);
		//header("Location:".BASEURL."?loginVulate=1");
		echo"<script> self.location='".BASEURL."?loginVulate=1';</script>";
	} else {
		//session_register("person");
			$_SESSION['person'] = $res[0];
		//session_register("pname");
			$_SESSION['pname'] = $res[1];
		//header("Location:".BASEURL."system/");
		echo"<script> self.location='".BASEURL."system/';</script>";

	}
	db_disconnect();
?>
