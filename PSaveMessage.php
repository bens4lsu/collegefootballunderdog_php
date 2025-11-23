<?php
    
    include ('page-init.php');
    include ('./includes/fnGetCurrentUrl.php');
    print_r($_POST);
    
    //[pool] => 2 [parent] => 0 [user] => 4 [message] => fsdfasdf erggsdf sdf dfg d [em] => on
    
    $message = $_POST['message'];
       
    $db->saveMessage ($_POST['pool'], $_POST['parent'], $message, $user->personId);
    
    // if ($_POST['em'] == 'on'){
//         $messageForEmail = str_replace("\n", "<br>", $_POST['message']);
//         
// 		if ($_POST['parent'] > 0){
// 			$P = $db->cbSqlQuery('select Message from PoolMessages where idPoolMessages = '.$_POST['parent']);
// 			$messageForEmail .= '<br><br><br><strong>in response to...</strong><br><br>'.$P['Message'];
// 		}
//         $path=cbGetCurrentUrl().'/Dindex.php?tab=3';
//         $messageForEmail .= '<br><br><br><br><strong>See messages inline at <a href="'.$path.'">'.$path.'</a>.</strong><br>Post replies to message board.  Responses to this email will not be delivered.';
//         $subject = 'A new message was posted by '.$user->name.' in '.$pool->name;
//         $pool->EmailAllMembers ($subject, $messageForEmail);
//     }
    
    cbRedirect('Dindex.php?tab=3', $isBeforeAnyOutput = true);
?>