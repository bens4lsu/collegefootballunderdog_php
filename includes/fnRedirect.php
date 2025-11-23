<?php
    
    function cbRedirect($page, $isBeforeAnyOutput = true, $onlyRedirectLocalLevel = false)
    {
        if ($isBeforeAnyOutput && ! $onlyRedirectLocalLevel) {
            header( 'Location: '.$page) ;
        }
        else if ($onlyRedirectLocalLevel){
			print '<script> window.location = "'.$page.'";  </script>';
        }
        else {
            print '<script> window.top.location = "'.$page.'";  </script>';
        }
    }
    
?>