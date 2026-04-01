<?php

$conn = oci_connect("ration_web","1234","//localhost:1521/XEPDB1");

if(!$conn){
    $e = oci_error();
    print_r($e);
}

?>