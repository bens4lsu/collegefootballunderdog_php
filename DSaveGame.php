<?php
    include ('page-init.php');

    
    //   [week] => 2 [away] => 17 [val-away] => 1 [home] => 81 [val-home] => 2 [kickoff] => 2013-09-07 12:00 [ns] => on
    
    $week = $_POST['week'];
    $kickoff = $_POST['kickoff'];
    
    if (isset ($_POST['val-away']) && is_numeric($_POST['val-away']) && $_POST['val-away'] < 0){
        $idFav = $_POST['away'];
        $idDog = $_POST['home'];
        $spread = -1 * $_POST['val-away'];
        $site = 2;
    }
    else if (isset ($_POST['val-away']) && is_numeric($_POST['val-away']) && $_POST['val-away'] >= 0){
        $idDog = $_POST['away'];
        $idFav = $_POST['home'];
        $spread = $_POST['val-away'];
        $site=3;
    }
    else if (isset ($_POST['val-home']) && is_numeric($_POST['val-home']) && $_POST['val-home'] < 0){
        $idDog = $_POST['away'];
        $idFav = $_POST['home'];
        $spread = -1 * $_POST['val-home'];
        $site=3;
    }
    else if (isset ($_POST['val-home']) && is_numeric($_POST['val-home']) && $_POST['val-home'] >= 0){
        $idDog = $_POST['home'];
        $idFav = $_POST['away'];
        $spread = $_POST['val-home'];
        $site=2;
    }
    else {
        $err = true;
        $errMess = 'Invalid spread number';
    }
    if (isset($_POST['ns']) && $_POST['ns'] == 'on'){
        $site=4;
    }
    //$spread = abs($_POST['])
    
    
    
    $result = $db->cbSqlQuery ('insert DGames (idDWeeks, idDFootballTeamsFav, idDFootballTeamsDog, Spread, idSiteTypes, Kickoff) values ('.$week.', '.$idFav.', '.$idDog.', '.$spread.', '.$site.', \''.$kickoff.'\')');
                                       
    if (! $result){
        Print 'Error saving game: '.$db->error;
    }
    else{
        cbRedirect('DAddGame.php');
    }
    
    //print_r($db);
    
    ?>