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
			ini_set('display_errors', 'On');
			if(isset($_FILES['upfile'])) {
				
			} else{
				echo('<p>Or don\'t. Free country.</p>');
			}
		?>
		
		</div>
		<div id="downloads">
			<form id = "fileselector" action="upload.php" method="post" enctype="multipart/form-data">
				<input type="file" name="upfile" id="filebutton">
				<input type="submit" id="uploadButton" value="Upload File">
			</form>
			</div>
		</div>
		
		<div id="outro">
		  <a href="./index.php" >But I don't want to upload anything...</a>
		</div>
	</body>
</html>

