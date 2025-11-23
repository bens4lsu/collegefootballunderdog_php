<?php

class Watchlist {

    public $arrGames = Array();  

    function __construct($weekId, $poolUserEntryId, $db) {
        $wlFromDb = $db->getWatchlist($poolUserEntryId, $weekId); 
        if (is_array($wlFromDb)) {
            foreach($wlFromDb as $node) {
                $this->arrGames[] = intval($node[0]);
            }
        }
    }
    
    function jsonArray() {
        print (json_encode($this->arrGames));
    }
}