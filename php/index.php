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
			if($bytes==0){
				return "??? B";
			}
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

		function generateDownloadTickets($file1, $file2){

			$ticket1 = ticket();
			$ticket2 = ticket();

			while(mysql_query("SELECT key from downloadkeys WHERE key = " . $ticket1)){
				$ticket1 = ticket();
			}

			while(mysql_query("SELECT key from downloadkeys WHERE key = " . $ticket2)){
				$ticket2 = ticket();
			}

			if(!mysql_query(
					sprintf("INSERT INTO downloadkeys
						(ticket, partnerticket, fileid, expiretime)
						VALUES
						(
							'%s',
							'%s',
							%s,
							DATE_ADD(NOW(), INTERVAL 30 MINUTE)
						)",
						$ticket1, $ticket2, $file1['id'])
					)){
				echo("HOW DO I INSERT TICKET 1<br>");
				echo(mysql_error());
			}

			if(!mysql_query(
					sprintf("INSERT INTO downloadkeys
						(ticket, partnerticket, fileid, expiretime)
						VALUES
						(
							'%s',
							'%s',
							%s,
							DATE_ADD(NOW(), INTERVAL 30 MINUTE)
						)",
						$ticket2, $ticket1, $file2['id'])
					)){
				echo("HOW DO I INSERT TICKET 2<br>");
				echo(mysql_error());
				echo("<br>");
			}

			return array($ticket1, $ticket2);
		}

		//ini_set('display_errors', 'On');
		

		
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

		$query = mysql_query('SELECT * FROM files ORDER BY RAND() LIMIT 1', $link );
		$file1 = mysql_fetch_assoc($query);

		$query = mysql_query('SELECT * FROM files WHERE id!='. $file1['id'] .' ORDER BY ABS(' . $file1['elo'] .' - elo) DESC');
		$file2 = mysql_fetch_assoc($query);

		$tickets = generateDownloadTickets($file1, $file2);

		/*
			Please make note of these MySQL credentials again:
			  Root User: adminw8mhkXB
			  Root Password: iRDY89u5GHCR
			URL: https://filedump-object.rhcloud.com/phpmyadmin/
		*/

	?>

<html>
	<head>
		  <meta charset="utf-8">
		<title> File Dump </title>
		<link rel="stylesheet", href="mainstyle.css" />
	</head>
	<body>
		<div id="intro">
			<h1>Welcome To<br>File Dump</h1>
			<p>The simple file sharing service.</p>
			<p> It's so simple that we've removed the chore of controlling what file you want to download.</p>
		</div>
		
		<div id="downloads">
			<a <?php echo(sprintf("href='download.php?ticket=%s&side=%s'",$tickets[0], 0))?> class='bigtile dl'>
				<?php 
					echo(sprintf("
						<h1>%s</h1>
						<p>%s</p>
						<p>%s</p>
						<p>%s</p>",

						basename($file1["filepath"]),
						formatBytes(filesize($file1["filepath"])),
						filetype($file1["filepath"]),
						date("n/j/Y", filemtime($file1["filepath"])
						)
					));
				?>  

			  <p>Trustworthyness: questionable</p>
		  </a>
		  <a <?php echo(sprintf("href='download.php?ticket=%s&side=%s'",$tickets[1], 1))?> class='bigtile dl'>
			  <?php 
					echo(sprintf("
						<h1>%s</h1>
						<p>%s</p>
						<p>%s</p>
						<p>%s</p>",

						basename($file2["filepath"]),
						formatBytes(filesize($file2["filepath"])),
						filetype($file2["filepath"]),
						date("n/j/Y", filemtime($file2["filepath"])
						)
					));
				?>  
			  
			  <p>Trustworthyness: questionable</p>
		  </a>
		  <a class="uploadButton" style="height:0px; opacity:0; display:none;" href="./">
			Give Me Another Pair
		  </a>
		</div>
		
		<div id="outro">
		  		<a href="upload.php" >Upload File</a>
		  		<a href="./leaderboard.php">
					Leaderboard
				</a>
		</div>

		<div id="footerpositioner">
			<div id="footer">
				Site by Maxwell Huang-Hobbs of 
				<a href="http://www.enigmasm.com">Those Assholes at EnigmaSM</a>
			</div>
		</div>

		<script type="text/javascript" src="jquery-2.0.3.min.js"></script>
		<script type="text/javascript">
			var disabled = false;
			$(document).ready(function(){
				$(".dl").click(function(evt){
					evt.preventDefault();
					$('.dl').animate(
						{
							height: "0px",
							opacity: '0'
						},
						callback=function(){
							$(this).hide()
							if ($(".dl:animated").length === 0){
								if (!disabled){
									s = $(evt.target);
									while (!s.is('a')){
										s=$(s.parent());
									}

									$('.uploadButton').show().animate(
									{
										height: "40px",
										opacity: '1'
									}, callback=function(){
										console.log(s.prop('href'));
										window.location.href = s.prop('href')
									});
								}
							}
						}
						);
				});
			});

			j = function(s){
				console.log(s);
				return parseInt(s.replace('p','').replace('x',''));
			}

			$( '.dl' ).each(function () {

				console.log(this);

				var width = 171;
				var line = $( this ).children()[ 0 ];

				var s = j($(line).css('font-size'));
				var n = s;

				while ( $(line).width() > width ) {
					n = n-1;
					$(line).css( 'font-size', n+"px" );
					console.log($(line).width());
					if(n<12){
						$(line).css( 'font-size', 12+"px" );
						$(this).css("word-wrap","break-word");
						$(this).css("overflow-wrap","break-word");
						break;
					}
				}
				$($(this).children()[0]).css("float","none");

			});
		</script>


	</body>
</html>

