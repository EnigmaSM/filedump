<!DOCTYPE HTML>
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
					$filedir = "./files/";
					$newloc = $filedir . $file['name'];
					move_uploaded_file($file['tmp_name'], $newloc);

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
						"INSERT INTO files (id, filepath, elo) VALUES (". ($numfiles+1) .", mysqli::real_escape_string ('". $newloc ."'), 1400)"
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

