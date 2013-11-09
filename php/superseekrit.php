<html>
	<body>
		<?php
			
			$dbloc = getenv("OPENSHIFT_MYSQL_DB_HOST");
			$dbusr = getenv("OPENSHIFT_MYSQL_DB_USERNAME");
			$dbpass = getenv("OPENSHIFT_MYSQL_DB_PASSWORD");
			/*
			$dbloc = "localhost";
			$dbusr = "root";
			$dbpass = "root";
			*/
			$link = mysql_connect($dbloc, $dbusr, $dbpass);

			if(! mysql_query( 'CREATE DATABASE filedump', $link ) ){
						  echo ("<p>" . mysql_error() . "</p>");
						} else{
							echo("Database comictitle created.<br>");
						}

			mysql_select_db("filedump");

			if(mysql_query( 
							'CREATE TABLE files (
								id INT NOT NULL,
								filepath TINYTEXT NOT NULL,
								elo FLOAT NOT NULL,
								PRIMARY KEY (id) )',
							 $link )){
				echo("okay 1");
			} else{
				echo(mysql_error());
			}
			echo("<br>");
			if(mysql_query( 
							'CREATE TABLE downloadkeys (
								ticket CHAR(100) NOT NULL,
								partnerticket TEXT NOT NULL,
								fileid INT NOT NULL,
								expiretime DATETIME NOT NULL,
								PRIMARY KEY (ticket) )',
							 $link )){
				echo("okay 2");
			} else{
				echo(mysql_error());
			}
			echo("<br>");
			echo("done");
		?>

	</body>
</html>