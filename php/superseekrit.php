<html>
<!--

	Copyright 2013 Jeffrey Tao, Maxwell Huang-Hobbs, William Saulnier, 2013
	Distributed under the terms of the GNU General Public License.
	
	This file is part of Dgr_dr
	
	This is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This file is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this file.  If not, see <http://www.gnu.org/licenses/>.
	
-->
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