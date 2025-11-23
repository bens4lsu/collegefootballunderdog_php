<?php
    include ('page-init.php');
    
    //print_r($_POST);
    
    $arrGames = array();   // want to make something like array(15=>array(dog=>14, fav=>7), 81=>array(dog=>9, fav=>21))
    foreach ($_POST as $key=>$val){
        $gPos = strpos($key, 'g');
        $usPos = strpos($key, '_');
        $homePos = strpos($key, 'homeScore');
        $awayPos = strpos($key, 'awaySco');
        if ($gPos == 0 && $usPos > 1 && $homePos > $usPos){
            $game = substr($key, 1, $usPos-1);
            if (! isset($arrGames[$game])){
                $arrGames[$game] = array('home'=>$val);
            }
            else{
                $arrGames[$game]['home'] = $val;
            }
        }
        if ($gPos == 0 && $usPos > 1 && $awayPos > $usPos){
            $game = substr($key, 1, $usPos-1);
            if (! isset($arrGames[$game])){
                $arrGames[$game] = array('away'=>$val);
            }
            else{
                $arrGames[$game]['away'] = $val;
            }
        }
    }
    
    //print_r($arrGames);
    
    foreach ($arrGames as $game => $updates){
        if (is_numeric($updates['home']) && is_numeric($updates['away'])){
            $sql = 'update DGames2 set ScoreHome = '.$updates['home'].', ScoreAway = '.$updates['away'].' where idDGames = '.$game;
            $db->cbSqlQuery($sql);
        }
    }
    cbRedirect('DScoreGames.php');

?>