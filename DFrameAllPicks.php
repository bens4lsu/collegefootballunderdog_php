<?php
    include('page-init.php');
    include('includes/classDogAllPicks.php');
    $dog = new DogAllPicks($pool, $db);
    
    $week = isset($_GET['week']) ? $_GET['week'] : $dog->currentWeek;
    
    $W = $db->cbSqlQuery('select WeekName from DWeeks where idDWeeks = '.$week);
    $weekDesc = $W['WeekName'];
    
    $hideLink = isset($_GET['hideLink']);
    
?>
<html>
<head>
<title>A Little Something to Make It Interesting</title>
<meta charset="UTF-8" />
<link rel="stylesheet" href="./css/jquery-ui-1.9.0.custom.min.css" />
<link rel="stylesheet" href="./css/jquery.dataTables.css" />
<link rel="stylesheet" href="./css/infodrivenlife.css" />
<style>
    ul {list-style-type: none;}
    .andbonus{margin-left:4em;}
    .winner{background-color:#ADFFA0;}
    .pick{margin-bottom:10px;}
    .loser{background-color:#FFC6A0;}
    p.key span{margin-left:3em;}
    p.key{margin-bottom:3em;}

</style>

</head>
<body>

    <?php if (! $hideLink) { ?>
        <a href="DFrameAllPicks.php?hideLink=1" target="_none">Open All Picks in a new tab</a>
    <?php } ?>
    <h4>All Picks Selected for <?php print $weekDesc; ?></h4>
        
    <p class="key">Key:  <span class="winner">Winner</span><span class="loser">Loser</span></p>

    <?php
        if ($dog->UserAlreadyPicked($poolUserId, $week)  || $dog->isWeekInThePast($week)){
            $dog->PrintAllPicksForWeek($week);
        }
        else {
            print '<div style="font-weight:bold; margin-bottom:1.5em;">Picks from games that are completed, ongoing, or which kick off in the next 30 minutes:</div>';
            if (isset($pool->arrConfigOptions['PICKPREVIEWINTERVAL'])){
            	$dog->PrintAllPicksIfTheGameIsAboutToStart($week, $pool->arrConfigOptions['PICKPREVIEWINTERVAL']); 
            }
            print '<div style="font-weight:bold; margin-top:1.5em;">All picks will be visible when your pick for this week is submitted.</div>';
        }
        
    ?>
</body>
</html>