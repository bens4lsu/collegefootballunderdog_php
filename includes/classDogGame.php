<?php

    class Game
    {
        private $db;
        public $gameId;
        private $weekId;
        private $awayTeamID;
        private $homeTeamID;
        private $neutralSite;
        public $kickoff;
        public $currentSpread;
        public $underdogTeam;
        public $homeTeamName;
        public $awayTeamName;
        public $homeScore;
        public $awayScore;
        private $homeFont;
        private $awayFont;
        private $homeSpread;
        private $awaySpread;
        private $homeTeamUrl;
        private $awayTeamUrl;
        public $quality;
        private $showGameInPickList;
        
       
        function __construct($db, $gameId, $currentSpread, $underdogTeam)
        {
            $this->db = $db;
            $this->gameId = $gameId;
            $this->currentSpread = $currentSpread;
            $this->underdogTeam = $underdogTeam;
            $dbInfo = $db->getGameInfo($gameId);
            $this->weekId = $dbInfo['idDWeeks'];
            $this->awayTeamID = $dbInfo['idDFootballTeamsAway'];
            $this->homeTeamID = $dbInfo['idDFootballTeamsHome'];
            $this->neutralSite = $dbInfo['isNeutralSite'];
            $this->kickoff = $dbInfo['Kickoff'];
            $this->homeScore = $dbInfo['ScoreHome'];
            $this->awayScore = $dbInfo['ScoreAway'];
            $this->awayTeamName = $dbInfo['AwayTeamName'];
            $this->homeTeamName = $dbInfo['HomeTeamName'];
            $this->homeTeamUrl = $dbInfo['HomeTeamUrl'];
            $this->awayTeamUrl = $dbInfo['AwayTeamUrl'];
            $this->homeFont = $underdogTeam == 'H' ? "font-weight:bold;" : '';
            $this->awayFont = $underdogTeam == 'A' ? "font-weight:bold;" : '';
            $this->awaySpread = $underdogTeam == 'H' ? (float) $currentSpread * -1 : '';
            $this->homeSpread = $underdogTeam == 'A' ? (float) $currentSpread * -1 : '';
            
            global $poolId;
            $quality = $db->getGameQuality($gameId, $poolId);
           // print_r($quality);
            $this->quality = $quality['Q'];
            $this->showGameInPickList = $quality['ShowGameFlag'];
        }
        
        function PrintGameSelectTable()
        {
            if ($this->showGameInPickList == 0){
                return;
            }
            $date = new DateTime($this->kickoff);
            $homeTeamLink = $this->homeTeamName;
            $awayTeamLink = $this->awayTeamName;
            if (isset($this->homeTeamUrl) && $this->homeTeamUrl != ''){
                $homeTeamLink = '<a href="'.$this->homeTeamUrl.'" target="_blank">'.$this->homeTeamName.'</a>';
            }
            if (isset($this->awayTeamUrl) && $this->awayTeamUrl != ''){
                $awayTeamLink = '<a href="'.$this->awayTeamUrl.'" target="_blank">'.$this->awayTeamName.'</a>';
            }
            print '<table class="game" id="game-'.$this->gameId.'">';
            print '<tr><td class="gameid" rowspan="2">'.$this->gameId.'<br><img src="images/watchlist-n.png" id="wl'.$this->gameId.'" class="wl-img"></td><td rowspan="2" class="radioselect"><input type="radio" name="picks" value="'.$this->gameId.'"></td><td style="'.$this->awayFont.'" class="team">'.$awayTeamLink.'</td><td class="spread">'.$this->awaySpread.'</td></tr>';
            print '<tr><td style="'.$this->homeFont.'" class="team">'.$homeTeamLink.'</td><td class="spread">'.$this->homeSpread.'</td></tr>';
            print '<tr><td colspan="4">Pick unavailable after '.$date->format("n/d/Y g:i A").'</td></tr>';
            print '</table>';
            print '<div style="display:none"><input name="spread-'.$this->gameId.'" value="'.$this->currentSpread.'"><input name="team-'.$this->gameId.'" value="'.$this->underdogTeam.'">"</div>';
        }
        
        function printGameScoreTable()
        {
            print '<table class="game">';
            print '<tr><td rowspan="2" class="gameid">'.$this->gameId.'</td><td style="'.$this->awayFont.'" class="team">'.$this->awayTeamName.'</td><td><input name="g'.$this->gameId.'_awayScore" value="'.$this->awayScore.'"></td></tr>';
            print '<tr><td style="'.$this->homeFont.'" class="team">'.$this->homeTeamName.'</td><td><input name="g'.$this->gameId.'_homeScore" value="'.$this->homeScore.'"></td></tr>';
            print '</table>';
        }
    }
?>