<?php
#########################################################
# Arab Open University Question Bank			        #
#########################################################
#                                                       #
# Created by: Abdel Kawy  Abdel Hady                    #
#                                                       #
#########################################################

	function db_connect()
	{
		global $db;

		if ($db)
			return;

		$db = mysqli_connect(MYSQHOST, MYSQLUSER, MYSQLPASS, MYSQLDB);

		if (!$db)
		{
			echo "Cant not connect to database server!\n";
			exit;
		}
	}

	function db_disconnect()
	{
		global $db;

		if ($db)
		{
			mysqli_close($db);
			$db = NULL;
		}
	}

?>
