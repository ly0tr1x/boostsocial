<?php

$u=$_GET["cur"];

$_SESSION['curid']=$u;
    header("Location: " . $_SERVER['HTTP_REFERER']);

 