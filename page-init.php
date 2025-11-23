<?php
	include_once ('./includes/classDb.php');	
	include_once ('config.php');
	//include_once ('./includes/db.php');
	include_once ('./includes/classPerson.php');
    include_once ('./includes/fnRedirect.php');
    include_once ('./includes/classPool.php');
    include_once ('./includes/classSys.php');

    $db = new Db();
    session_start();
    
    //load $user from Session
    if (!isset($_SESSION['user'])){
        cbRedirect('login.php');
    }
   

    $user = unserialize($_SESSION['user']);
    // user must have authenticated within the last four hours
    if ($user->lastTimeVerified < time() - 14400){
        cbRedirect('login.php');
    }
    if ($user->lastIPVerified != cbGetCurrentIpAddress()){
        cbRedirect('login.php');
    }


    if (isset($user->timezone)){
        date_default_timezone_set($user->timezone);
    }
    else {
        date_default_timezone_set("America/Chicago");
    }

    $user->setDb($db);
    $poolId = $user->lastPoolIDUsed;
    $showPoolSelector = (count($user->arr_Pools) > 1);
    $pool = new Pool($poolId, $db);
    
    // $poolUserId is the normal unique identifier to get a user within a pool
    if (isset($poolId) && isset($user->arr_Pools[$poolId])){
        $poolUserId = $user->arr_Pools[$poolId];
    }

    
?>