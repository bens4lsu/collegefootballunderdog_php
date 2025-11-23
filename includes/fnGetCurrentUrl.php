<?php
    
    function cbGetCurrentUrl(){
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    }
    
?>