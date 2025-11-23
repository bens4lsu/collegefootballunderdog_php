<?php
    include ('page-init.php');
    include ('/var/www/apps/collegefootballunderdog/includes/classGamesForWeekFromAPI.php');
    
    
    $gamesAPI = new GamesForWeekFromAPI();
    
    print_r($gamesAPI->json);