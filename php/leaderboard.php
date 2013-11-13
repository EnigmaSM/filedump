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
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

	<?php

		function formatBytes($bytes, $precision = 2) { 
			$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

			$bytes = max($bytes, 0); 
			$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
			$pow = min($pow, count($units) - 1); 

			// Uncomment one of the following alternatives
			 $bytes /= pow(1024, $pow);
			// $bytes /= (1 << (10 * $pow)); 

			return round($bytes, $precision) . ' ' . $units[$pow]; 
		}

		function ticket(){
			$ticket = "";
			$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			for ($i=0; $i<100; $i++){
				$ticket = $ticket . $characters[rand(0, strlen($characters) - 1)];
			}
			return $ticket;
		}

		function generateDownloadTicket($file){

			$ticket = ticket();

			while(mysql_query("SELECT key from downloadkeys WHERE key = " . $ticket)){
				$ticket = ticket();
			}

			if(!mysql_query(
					sprintf("INSERT INTO downloadkeys
						(ticket, partnerticket, fileid, expiretime)
						VALUES (
							'%s',
							'?',
							'%s',
							DATE_ADD(NOW(), INTERVAL 30 MINUTE)
						)",
						$ticket, $file['id'])
					)){
				echo(sprintf("INSERT INTO downloadkeys
						(ticket, partnerticket, fileid, expiretime)
						VALUES (
							'%s',
							'?',
							'%s',
							DATE_ADD(NOW(), INTERVAL 30 MINUTE)
						)",
						$ticket, $file['id'])
					);
				echo("HOW DO I INSERT TICKET 1<br>");
				echo(mysql_error());
			}

			return $ticket;
		}

		//ini_set('display_errors', 'On');
	?>

<html>
	<head>
		  <meta charset="utf-8">
		<title> File Dump </title>
		<link rel="stylesheet", href="mainstyle.css" />
	</head>
	<body>
		<div id="intro">
			<h1>Leaderboard</h1>
			<p>These are the best files.</p>
			<p>Perhaps not the best in terms of content, but they get people to download them.</p>
		</div>
		
		<div id="downloads">
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
				mysql_select_db("filedump");

				$query = mysql_query("SELECT * from files ORDER BY elo DESC LIMIT 10");

				for($i=0; $i<10; $i++){
					$file = mysql_fetch_assoc($query);
					if($file['filepath']!=''){
						$ticket = generateDownloadTicket($file);
						echo( sprintf(
							"<a class='uploadButton gridded'
								href='./download_leaderboard.php?ticket=%s'>
							  	/%s
							  </a>",
							  $ticket,
							  basename($file['filepath']) ) 
						);
					}
					else{
						echo("<div class='uploadButton gridded greyed'> / </div>");
					}
				}
			?>
		</div>
		
		<div id="outro">
		  <a href="./" >Return to Main Site</a>
		</div>

		<script type="text/javascript" src="jquery-2.0.3.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".uploadButton:not(.greyed)").click(function(evt){
					window.location.href = $(this).prop('href')
					$(this).addClass("greyed");
					$(this).attr("href","javascript: void(0)");

				})
			});
		</script>


	</body>
</html>

