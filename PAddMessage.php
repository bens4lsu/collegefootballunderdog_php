<?php
    include ('page-init.php');
    
    //$pool = isset($_GET['pool']) ? $_GET['pool'] : 0;
    $parent = isset($_GET['parent']) ? $_GET['parent'] : 0;
    
    if ($parent > 0){
        $arrParentInfo = $pool->GetMessageInfo($parent);
        $heading = 'Post Reply to the Following Message from '.$arrParentInfo['Name'];
        $messBody = '<p style="color:#1D1D1D; margin-left:40px;">'.$arrParentInfo['Message'].'</p>';
        $inputLabel="Message:";
    }
    else {
        $heading = 'Post New Message for All Pool Members';
        $messBody = '';
        $inputLabel="Reply";
    }
    ?>

<html><head>
</head>
<body>
<h4><?php print $heading;?></h4>
<?php print $messBody; ?>

<form action="PSaveMessage.php" method="post">

<input type="hidden" name="pool" value="<?php print $poolId; ?>">
<input type="hidden" name="parent" value="<?php print $parent; ?>">

<table>
<tr><td style="width:9em; vertical-align:top;"><?php print $inputLabel; ?></td><td><textarea rows="20" cols="100" name="message"></textarea></td></tr>
<tr><td>Email Pool Members:</td><td><input type="checkbox" name="em" checked></td></tr>
</table>
<input type="submit" value="Post Message">
</form>

</body>
</html>