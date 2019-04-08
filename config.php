<?php
#########################################################
# Video Course Aggregator (VCA)		     		        #
#########################################################
#                                                       #
# Created by: Abdel Kawy  Abdel Hady                    #
#                                                       #
#########################################################
   	define( '_JEXEC', 1 );
	define( 'YOURBASEPATH', dirname(__FILE__) );
    define("SITENAME",	"Video Course Aggregator");
	define("DOMAIN",	"http://".$_SERVER['SERVER_NAME']);
	define("BASEURL",	DOMAIN."/~abdelkawy/videopedia/");
	define("IMG_DIR",	DOMAIN."/~abdelkawy/videopedia/images");
	define("FROMEMAIL",	"abdelkawy@frigatesoft.com");
	define("COMSITE",	"WWW.FRIGATESOFT.COM");
	define("INC_DIR",	YOURBASEPATH."/inc/");
	define("PAG_DIR",	YOURBASEPATH."/system/");
	define("LIB_DIR",	YOURBASEPATH."/library/");
	define("INC_DB",	INC_DIR."/db.php");
	define("MYSQHOST", "127.0.0.1"); // mysql host
	define("MYSQLUSER",	"root"); // mysql username
	define("MYSQLPASS",	"admin2009"); // mysql password
	define("MYSQLDB",	"videopedia");    // mysql database name
	date_default_timezone_set('Africa/Cairo');
	require(INC_DB);

?>
