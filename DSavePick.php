<?php
    include ('page-init.php');
    
    //print_r($_POST);
    
    $pick =  isset($_POST['picks']) ? $_POST['picks'] : null;
    $spreadIdx = 'spread-'.$pick;
    $spread = isset($_POST[$spreadIdx]) ? $_POST[$spreadIdx] : null;
    $teamIdx = 'team-'.$pick;
    $team = isset($_POST[$teamIdx]) ? $_POST[$teamIdx] : null;
    $userId = $user->arr_Pools[$poolId];

    if ($db->savePick($pick, (float) $spread, (int) $userId, $team)){
        cbRedirect('Dindex.php');
    }
    else {
        Print 'Error saving pick: '.$db->error;
    }

?>