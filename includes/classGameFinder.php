<?php

class GameFinder {

    public $game;

    function __construct($db, $gameDate, $homeTeamName, $awayTeamName, $spread, $underdog, $showAdminMessage, $timeFormat = 'D, M d h:i a') {
        
        $tz = new DateTimeZone('America/Chicago');
        $dt = DateTime::createFromFormat($timeFormat, $gameDate, $tz);
        $gameMonth = $dt->format('m');
        $now = new DateTime();
        $currentMonth = $now->format('m');
        if (($gameMonth == 1 || $gameMonth == 2) && $currentMonth > 2){
            $dt = $dt->add(new DateInterval('P1Y'));
        }
        $dtstr = $dt->format('Y-m-d G:i');
              
        if ($spread == 0) {
            $this->game = false;
        }
        else {
            if ($result = $db->findGame($dtstr, $homeTeamName, $awayTeamName)) {
                $game = new Game($db, $result['idDGames'], $spread, $underdog);  // function __construct($db, $gameId, $currentSpread, $currentTeamFavored)
                $this->game = $game;
            }
            else if ($result = $db->createGame($dtstr, $homeTeamName, $awayTeamName, 0, $spread, $underdog) && $db->numRowsAffected == 1) {
                //print_r($db);
                $game = new Game($db, $db->insertIdentity, $spread, $underdog);
                $this->game = $game;
            }
            else if ($showAdminMessage) {
                //print_r($db);
                print ("Could not find or create game between $awayTeamName and $homeTeamName on date $dtstr.<br>");
                $this->game = false;
            }
        }
    }
}