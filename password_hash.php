
<?php

$password = 'admin12345';

$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

echo $hash;
