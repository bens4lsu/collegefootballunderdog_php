<?php
    
class GamesForWeekFromAPI {
    
    public $json;
    public $arrGames;  //array of Games to load
    
    function __construct($poolUserEntryId = null) {
        $url = 'http://localhost:8086/getLines';
        if ($poolUserEntryId != null) {
            $url = $url . "?poolUserEntryId=" . $poolUserEntryId;
        }
        $this->json = file_get_contents($url);
        $returnData = json_decode($this->json);
        
        $this->arrGames = $returnData->games;
    
    }
    
    
    function printPickTable() {            
        print '<form action="DSavePick.php" method="post" id="frmBigPicker">';
        foreach($this->arrGames as $game){
            $homeFont = $game->whoFavored == 'A' ? "font-weight:bold;" : '';
            $awayFont = $game->whoFavored == 'H' ? "font-weight:bold;" : '';
            $awaySpread = $game->whoFavored == 'A' ? (float) $game->spread * -1 : '';
            $homeSpread = $game->whoFavored == 'H' ? (float) $game->spread * -1 : '';
            $underdogTeam = $game->whoFavored == 'H' ? 'A' : 'H';
            
            print '<table class="game" id="game-'.$game->gameId.'">';
            print '<tr><td class="gameid" rowspan="2">'.$game->gameId.'<br><img src="images/watchlist-n.png" id="wl'.$game->gameId.'" class="wl-img"></td><td rowspan="2" class="radioselect"><input type="radio" name="picks" value="'.$game->gameId.'"></td><td style="'.$awayFont.'" class="team">'.$game->awayTeam.'</td><td class="spread">'.$awaySpread.'</td></tr>';
            print '<tr><td style="'.$homeFont.'" class="team">'.$game->homeTeam.'</td><td class="spread">'.$homeSpread.'</td></tr>';
            print '<tr><td colspan="4">Pick unavailable after '.$game->kickoff.'</td></tr>';
            print '</table>';
            print '<div style="display:none"><input name="spread-'.$game->gameId.'" value="'.$game->spread.'"><input name="team-'.$game->gameId.'" value="'.$underdogTeam.'">"</div>';
          
        }
        print '<input type="submit" value="Lock In Your Selection" id="buttonlockpick"></form>';
        
    }
}
