<?php
	$files = glob('../rt' . '/*.pdf');
	$file = array_rand($files);
	echo $files[$file];
?>
