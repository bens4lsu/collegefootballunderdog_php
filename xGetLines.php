<?php

$json = file_get_contents("http://localhost:8086/getLines");

$returnData = json_decode($json);

print_r($json);
print_r($returnData);