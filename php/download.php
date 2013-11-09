<?php

	ini_set('display_errors', 'On');
	
	$dbloc = getenv("OPENSHIFT_MYSQL_DB_HOST");
	$dbusr = getenv("OPENSHIFT_MYSQL_DB_USERNAME");
	$dbpass = getenv("OPENSHIFT_MYSQL_DB_PASSWORD");
	$dbport = getenv("OPENSHIFT_MYSQL_DB_PORT");
	/*
	$dbloc = "localhost";
	$dbusr = "root";
	$dbpass = "root";
	*/
	if(isset($_GET['ticket'])){
		$link = mysql_connect($dbloc, $dbusr, $dbpass);
		mysql_select_db("filedump");
		
		$query = mysql_query(
			sprintf(
				'SELECT * FROM downloadkeys
				WHERE ticket = "%s" ',
				$_GET['ticket']), $link );
		if(!$query){
			echo(mysql_error());
		}
		$ticket = mysql_fetch_assoc($query);

		$query = mysql_query(
			sprintf(
				'SELECT * FROM downloadkeys
				WHERE ticket = "%s" ',
				$ticket['partnerticket']), $link );
		if(!$query){
			echo(mysql_error());
		}
		$partnerticket = mysql_fetch_assoc($query);

		//remove temp keys from table
		if(!mysql_query( sprintf(
			'DELETE FROM downloadkeys WHERE ticket="%s"',
			$ticket['ticket']), $link
			)){
			echo(mysql_error()."<br>");
		}

		if(!mysql_query( sprintf(
			'DELETE FROM downloadkeys WHERE ticket="%s"',
			$partnerticket['ticket']), $link
			)){
			echo(mysql_error()."<br>");
		}

		//check that the ticket is not expired
		if( mysql_query('SELECT id FROM files WHERE id=' .$ticket['fileid'], $link ) ){

			// grab the requested file's name
			$query = mysql_query('SELECT * FROM files WHERE id=' .$ticket['fileid'], $link );
			$file = mysql_fetch_assoc($query);

			$query = mysql_query('SELECT * FROM files WHERE id=' .$partnerticket['fileid'], $link );
			$partnerfile = mysql_fetch_assoc($query);

			
			$Ra = intval($file['elo']);
			$Rb = intval($partnerfile['elo']);

			$Ea = 1/(1 + pow(10, (($Rb - $Ra) / 400) ));
			$Eb = 1/(1 + pow(10, (($Ra - $Rb) / 400) ));

			$Ra = $Ra + 32 * (1 - $Ea);
			$Rb = $Rb + 32 * (0 - $Eb);

			if(!mysql_query('UPDATE files
				SET elo= ' .strval($Ra).
				' WHERE  id
				='.$file['id']
				, $link )){
				echo(mysql_error());
			}

			if(!mysql_query('UPDATE files
				SET elo= ' .strval($Rb).
				' WHERE  id='.$partnerfile['id']
				, $link )){
				echo(mysql_error());
			}


			$file_name = $file['filepath'];
			// make sure it's a file before doing anything!
			if(is_file($file_name)) {

				// required for IE
				if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off'); }

				// get the file mime type using the file extension
				switch(strtolower(substr(strrchr($file_name, '.'), 1))) {
				case 'pdf': $mime = 'application/pdf'; break;
				case 'zip': $mime = 'application/zip'; break;
				case 'jpeg':
				case 'jpg': $mime = 'image/jpg'; break;
				default: $mime = 'application/force-download';
				}

				header('Pragma: public');   // required
				header('Expires: 0');       // no cache
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
				header('Cache-Control: private',false);
				header('Content-Type: '.$mime);
				header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: '.filesize($file_name));    // provide file size
				header('Connection: close');

				readfile($file_name);       // push it out
			}
			exit();
		}
	}

	echo('
		<head>
		  <meta charset="utf-8">
				<title> File Dump </title>
				<link rel="stylesheet", href="mainstyle.css" />
			</head>
			<body>
				<div id="intro">
					<h1>Download Ticket Expired</h1>
					<p>You done goof\'d.</p>
				</div>
				
				<div id="downloads">
					<a href="./index.php" id="uploadButton">
						Return home
					</div>
				</div>

			</body>
		');

	

?>