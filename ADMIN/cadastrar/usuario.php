<?php

$senha = "123456";

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

echo $senhaHash;