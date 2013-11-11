
<!DOCTYPE HTML>


	
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
			<a href='download.php?ticket=MNkZQaJsdq8IpthrUYMnoXknAKbzyOIlB2krc4Uqu28Uvplqo8NM589GSlfq9XLKZ6cca6CE9KyFaU5y2Tl72vOVQ4lZ17K1dWeo&side=0' class='bigtile dl'>
				
						<h1>10519922964_f56eb0fffc_k.jpg</h1>
						<p>??? B</p>
						<p></p>
						<p>1/1/1970</p>  

			  <p>Trustworthyness: questionable</p>
		  </a>
		  <a href='download.php?ticket=3Q2dBBSMvYlxRGFUctP3xb2zjNBxJPVNGX0iyT43RpAJ6fDjJsmgDpQWcrtWhpJXmKgVEkZvKzfRPSazlxQZWGW88q4pPOnczD7d&side=1' class='bigtile dl'>
			  
						<h1>5_out.jpg</h1>
						<p>648.85 KB</p>
						<p>file</p>
						<p>11/9/2013</p>  
			  
			  <p>Trustworthyness: questionable</p>
		  </a>
		  <a class="uploadButton" style="height:0px; opacity:0; display:none;" href="./">
			Give Me Another Pair
		  </a>
		  <a class="uploadButton" style="height:0px; opacity:0; display:none;" href="./leaderboard.php">
			Leaderboard
		  </a>
		</div>
		
		<div id="outro">
		  <a href="upload.php" >Click here to upload</a>
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

				var width = $( this ).width() - 2*j($(this).css('padding'));
				var line = $( this ).children()[ 0 ];

				var s = j($(line).css('font-size'));
				var n = s;

				while ( $(line).width() > width ) {
					n = n-1;
					$(line).css( 'font-size', n+"px" );
					console.log($(line).width());
					if(n<12){
						$(line).css( 'font-size', s+"px" );
						$(this).css("word-wrap","break-word");
						$(this).css("overflow-wrap","break-word");

						$($(this).children()[0]).css("float","none");

						break;
					}
				}

			});
		</script>


	</body>
</html>

