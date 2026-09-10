<?php

session_start();

unset($_SESSION["fogoEsangue"]);

header("Location: index.php");
exit;