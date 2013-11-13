<!DOCTYPE HTML>
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
<html>
	<head>
		  <meta charset="utf-8">
		<title> File Dump </title>
		<link rel="stylesheet", href="mainstyle.css" />
	</head>
	<body>
		<div id="intro">
			<h1>Upload You File<span>(pls)</span></h1>
		<?php
			if(isset($_FILES['upfile'])){
				if($_FILES['upfile']['name']==''){
					echo('<p>ACTUALLY SUBMIT A FILE THIS TIME?</p>');
				} else{
					$file = $_FILES['upfile'];
					$filedir = getenv("OPENSHIFT_DATA_DIR");
					$n = $filedir . $file['name'];
					$counter = 0;
					while(file_exists($n)) {
						$p = pathinfo($newloc);
						$n = $p["dirname"] ."/". $p["filename"] . "_". strval($counter).".". $p["extension"];
						$counter++;
					}
					echo($file['name']);
					move_uploaded_file($file['tmp_name'], $n);

					$dbloc = getenv("OPENSHIFT_MYSQL_DB_HOST");
					$dbusr = getenv("OPENSHIFT_MYSQL_DB_USERNAME");
					$dbpass = getenv("OPENSHIFT_MYSQL_DB_PASSWORD");
					/*
					$dbloc = "localhost";
					$dbusr = "root";
					$dbpass = "root";
					*/

					$link = mysql_connect($dbloc, $dbusr, $dbpass);
					mysql_select_db("filedump");

					$numfiles = mysql_fetch_assoc(mysql_query("SELECT MAX(id) FROM files"));
					$numfiles = $numfiles["MAX(id)"];

					if(!mysql_query(
						//should not use mysql_real_escape_string, because it is depreciated. can't mysqli with openshift, I think...
						"INSERT INTO files (id, filepath, elo) VALUES (". ($numfiles+1) .", '". mysql_real_escape_string ($n) ."', 1400)"
						)){
						echo("<p>". mysql_error() ."</p>");
					}
					echo('<p>File Submitted</p>');
				}
			} else{
				echo('<p>Or don\'t. Free country.</p>');
			}
		?>
		
		</div>
		<div id="downloads">
			<form id = "fileselector" action="upload.php" method="post" enctype="multipart/form-data">
				<input type="file" name="upfile" id="filebutton">
				<input type="submit" class="uploadButton" value="Upload File">
			</form>
			</div>
		</div>
		
		<div id="outro">
		  <a href="./index.php" >But I don't want to upload anything...</a>
		</div>
	</body>
</html>

