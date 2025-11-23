<?php
	include ('page-init.php');

    echo (extension_loaded('openssl')?'SSL loaded':'SSL not loaded')."\n"; 
    $headers = "From: gottakeepitautomated@collegefootballunderdog.com\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$sys = new Sys();
    $sys->sendAnEmail('bens@theskinnyonbenny.com', 'subject', 'message', $headers )
    
?>	

<html>
<head>


</head>
<body class="mainbox" >


</body>
</html>


