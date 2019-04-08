<?php
#########################################################
# Arab Open University CV Generator     		        #
#########################################################
#                                                       #
# Created by: Abdel Kawy  Abdel Hady                    #
#                                                       #
#########################################################

    session_start();
	require("../config.php");
    unset($_SESSION['person']);
    unset($_SESSION['pname']);
	session_destroy();
//	$redUrl = DOMAIN.$_REQUEST['redirect'];
	header("Location:".BASEURL);
?>