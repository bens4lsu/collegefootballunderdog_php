<?php
    
    //Which week is it now?
    include ('page-init.php');
    $row = $db->cbSqlQuery('SELECT * FROM `DWeeks` WHERE DATE(NOW()) < WeekDateEnd and idPoolSubtypes = '.$pool->subtypeId.' order by WeekDateEnd limit 0,1');
    $weekDescription = $row['WeekName'];
    $weekId = $row['idDWeeks'];
    
    $row = $db->cbSqlQuery('SELECT MAX(Kickoff) AS M 
                            from DGames where Kickoff >= (select WeekDateStart 
                                                          FROM DWeeks 
                                                          WHERE WeekDateEnd> NOW()  
							                              ORDER BY WeekDateEnd LIMIT 0,1)  ');
    $defaultDay = $row['M'];
    $r = $db->cbSqlQuery('select idDFootballTeams, TeamName from DFootballTeams order by TeamName', true);
    $opts = '';
    while ($row = mysql_fetch_array($r)){
        $opts .= '<option value="'.$row['idDFootballTeams'].'">'.$row['TeamName'].'</option>';
    }
    
?>

<html>
<head>
    <script language="javascript" type="text/javascript"  src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>
<body>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#away").focus();
        });
    </script>
    <form action="DSaveGame.php" method="post">

    <input type="hidden" name="week" value="<?php print $weekId; ?>">
    <table>
        <tr><td>Away:</td><td><select name="away" id="away"><?php print $opts; ?></select></td><td><input type="text" name="val-away"></td></tr>
        <tr><td>Home:</td><td><select name="home"><?php print $opts; ?></select></td><td><input type="text" name="val-home"></td></tr>
        <tr><td><Kickoff:</td><td><input type="text" name="kickoff" value="<?php print $defaultDay; ?>"></td></tr>
        <tr><td>Neutral Site:</td><td><input type="checkbox" name="ns"></td></tr>
    </table>
    <input type="submit">
    </form>
</body>
</html>