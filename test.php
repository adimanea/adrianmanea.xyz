<!DOCTYPE html>
<meta charset="utf-8">

<head>

  <style>
	.center {
		font-size: xx-large;
		margin: 0;
		position: absolute;
		top: 50%;
		left: 50%;
		-ms-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);
	}
  </style>

  <body>
	<div class="center">
	  <a href="
		<?php 
		   $files = scandir('rt'); 
		   $ip = $_SERVER['REMOTE_ADDR']; 
		   $split = explode(".", $ip);
		   \array_splice($files, 0, 2);
		   echo "rt/" . $files[$split[3] % count($files)];
		?>">
	  <b>TEST</b></a>
	</div>

	<form action="/date.php" method="post">
	  <label for="fname">Nume de familie:</label>
	  <input type="text" id="fname" name="fname"><br><br>
	  <label for="lname">Prenume:</label>
	  <input type="text" id="lname" name="lname"><br><br>
	  <input type="submit" value="Înscrie-te!">
	</form>
	<p>Introduceți numele de familie și prenumele în cîmpurile de mai sus și înscrieți-vă înainte de a începe testul!</p>

  </body>
</html>
