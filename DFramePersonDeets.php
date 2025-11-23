<?php
    include ('page-init.php');
    include ('./includes/classDogAllPicks.php');
    
    $dog = new DogAllPicks($pool, $db);
    $poolUserEntryId = $_GET['pueid'];
    
    $NQuery = $db->cbSqlQuery('select pu.Name from PoolUserEntries pue join PoolUsers pu on pue.idPoolUsers = pu.idPoolUsers where pue.idPoolUserEntries = '.$poolUserEntryId);
    $name = $NQuery['Name'];
?>

<html>
<head>
<title>A Little Something to Make It Interesting</title>
<meta charset="UTF-8" />
<link rel="stylesheet" href="./css/jquery-ui-1.9.0.custom.min.css" />
<link rel="stylesheet" href="./css/jquery.dataTables.css" />
<link rel="stylesheet" href="./css/infodrivenlife.css" />
<style>
    table.game td, th {border:1px solid #000000;}
    th{background-color:#5C9CCC;color:#FFFFFF;font-weight:bold;}
    .game {border-collapse:collapse; margin-bottom:1.5em;margin-left:20px;}
    .picked{margin-left:40px;margin-bottom:1.5em;}
    .team {min-width:215px;}
    .spread{min-width:55px; text-align:center;}
    
</style>
        
</head>
<body>

<h4>Prior Week Picks for <?php print $name; ?></h4>

<p><a href="DFrameStandings.php">Back to Full Standings</a></p>
<div><?php
$dog->PrintAllPicksForUser($poolUserEntryId);
?></div>
<p>Current week scoring hidden on this screen until completion of all games for the week.
<p><a href="DFrameStandings.php">Back to Full Standings</a></p>



</body>
</html>