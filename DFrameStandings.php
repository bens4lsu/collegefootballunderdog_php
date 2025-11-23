<?php
    include ('page-init.php');
    include ('./includes/classDogAllPicks.php');
    $dog = new DogAllPicks($pool, $db);
?>

<html>
<head>
<title>A Little Something to Make It Interesting</title>
<meta charset="UTF-8" />
<link rel="stylesheet" href="./css/jquery-ui-1.9.0.custom.min.css" />
<link rel="stylesheet" href="./css/jquery.dataTables.css" />
<link rel="stylesheet" href="./css/infodrivenlife.css" />
<style>
    table td, th {border:1px solid #000000; font-family:sans-serif; }
    th {background-color:#5C9CCC;color:#FFFFFF;font-weight:bold;}
    table {border-collapse:collapse; margin-bottom:1.5em;margin-left:20px;}
        .name {min-width:220px;}
        .score, .bonuses, .winners {text-align:right}
        

</style>

</head>
<body>
<ul>

<?php
    $dog->PrintStandings();

?>
</ul>
</body>
</html>