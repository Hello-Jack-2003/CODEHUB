<?php
    $sname='localhost';
    $suname='root';
    $spasswd='';
    $dbase='codehub';

    //new connection
    $conn = new mysqli($sname,$suname,$spasswd,$dbase);

    //check connection
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
    }
?>