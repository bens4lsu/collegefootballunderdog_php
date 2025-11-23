<?php
    include('page-init.php');
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

</style>
    
</head>
<body>
    <ul>
    
    <?php
        $subtype = $pool->subtypeId;
        $wr = $db->cbSqlQuery('select * from DWeeks where idPoolSubtypes = '.$subtype.' order by WeekDateStart', true	);
        foreach ($wr as $row){
            print '<li class="node_addTime"><a href="#" onclick="parent.window.frames[\'frAllPicks\'].location=\'DFrameAllPicks.php?week='.$row['idDWeeks'].'\'">'.$row['WeekName'].'</a></li>';
        }
    ?>
    </ul>
</body>
</html>