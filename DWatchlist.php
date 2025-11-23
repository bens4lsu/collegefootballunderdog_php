<?php

    include ('page-init.php');

    $db = new Db();
    
    //load $user from Session
    if (isset($_SESSION['user']) &&
        $user = unserialize($_SESSION['user']) && 
        $user->lastTimeVerified < time() - 14400 &&
        $user->lastIPVerified != cbGetCurrentIpAddress()
    ){
        print ( json_encode(Array('status' => 'error', 'message' => 'user session not verified.  re-login to continue.')) );
    }
    
    else if (isset($_POST['action']) && $_POST['action'] == 'set' && isset($_POST['game'])  && isset($_POST['userEntry'])){
        $db->addWatchlist($_POST['userEntry'], $_POST['game']);
        print json_encode(Array('status' => 'success'));
    }
    else if (isset($_POST['action']) && $_POST['action'] == 'unset' && isset($_POST['game']) && isset($_POST['userEntry'])){
        $db->deleteWatchlist($_POST['userEntry'], $_POST['game']);
        print json_encode(Array('status' => 'success', 'game' => $_POST['game'], 'userEntry' => $_POST['userEntry']));
    }
    else {
        print ( json_encode(Array('status' => 'error', 'message' => 'Invalid post request.')));
    }
