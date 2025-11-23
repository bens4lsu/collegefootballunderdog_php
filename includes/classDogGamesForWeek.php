<?php

    include("classLines.php");
    include("classGameFinder.php");
    
    class GamesForWeek
    {
        private $arrGames;  //array of Games to load
        private $db;
        private $idWeek;
        private $weekStartDate;
        private $weekEndDate;
        
        function __construct($weekId, $db, $constructType = GamesConstructType::fromLines, $showAdminMessage = false)
        {
            //print 'geaux'.$showAdminMessage;
            $this->db = $db;
            $this->idWeek = $weekId;
            $a = array();
            
            if ($constructType == GamesConstructType::fromLines) {
                $linesObject = new Lines('https://www.vegasinsider.com/college-football/odds/las-vegas/');
                $timeFormatEspn = 'D, M d h:i a';   // Monday, December 20 2:30 PM
                $timeFormatVI = 'm/d  g:i A';  // 01/01  2:00 PM
                //$lines = $linesObject->currentLines_espn();
                $lines = $linesObject->currentLines();
                
                foreach($lines as $line) {
                    $finder = new GameFinder($db, $line['gameTime'], $line['homeTeam'], $line['awayTeam'], $line['spread'], $line['underdog'], $showAdminMessage, $timeFormatVI); 
                    //print ("line:"); print_r($line);
                    //print ("finder:"); print_r($finder);
                    if ($finder->game) {
                        $a[] = $finder->game;
                    }
                }
            }
            else if ($constructType == GamesConstructType::fromDB){
            
            }
            else if ($constructType == GamesConstructType::fromDBPicksOnly){
                $gamesTbs = $db->getGamesWithPicks($weekId); 
                foreach ($gamesTbs as $game) {
                    $a[] = new Game($db, $game['idDGames'], NULL, NULL);
                }    
            }
            usort($a, array("GamesForWeek", "gameSort"));
            $this->arrGames = $a;
            $week = $db->getDatesForWeek($weekId);
            $this->weekStartDate = DateTime::createFromFormat('Y-m-d', $week['WeekDateStart']);
            $this->weekEndDate = DateTime::createFromFormat('Y-m-d H:i:s', $week['WeekDateEnd']." 23:59:59");
        }
                

        private static function gameSort($g1, $g2) {
            $g1DateTime = DateTime::createFromFormat('Y-m-d H:i:s', $g1->kickoff);
            $g2DateTime = DateTime::createFromFormat('Y-m-d H:i:s', $g2->kickoff);
            if ($g1DateTime == $g2DateTime){
                if ($g1->gameId == $g2-> gameId) {
                    return 0;
                }
                return ($g1->gameId < $g2->gameId) ? -1 : 1; 
            }
            return ($g1DateTime < $g2DateTime) ? -1 : 1; 
        }
        

        function printPickTable()
        {            
            print '<form action="DSavePick.php" method="post" id="frmBigPicker">';
            foreach($this->arrGames as $game){
                $kickDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $game->kickoff);
                
                //print ($this->weekEndDate->format("Y-m-d")."<br>".$kickDateTime->format("Y-m-d")."<br>".($kickDateTime > $this->weekEndDate)."<br>");
                
                if (! ($kickDateTime < new DateTime("now") ||
                       $kickDateTime > $this->weekEndDate))
                {
                    print '<div class="pickgames">';
                    $game->PrintGameSelectTable();
                    print'</div>';
                }
                
            }
            print '<input type="submit" value="Lock In Your Selection" id="buttonlockpick"></form>';
        }

        
        function printScoreForm()
        {
            print '<form action="PSaveScores.php" method="post">';
            foreach($this->arrGames as $game){
                $game->printGameScoreTable();
            }
            print '<input type="submit" value="Submit" id="buttonlockpick"></form>';
        }
        
        function PrintAdjustSpreadForm()
        {
            print '<form action="PAdjustSpread.php" method="post">';
            foreach($this->arrGames as $game){
                $game->PrintAdjustSpreadTable();
            }
            print '<input type="submit" value="Submit" id="buttonlockpick"></form>';
        }
    }

?>