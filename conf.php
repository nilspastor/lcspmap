<?php


// Connexion à la BDD
$db = new PDO('mysql:host=localhost_or_adress;dbname=my_db_name', 'my_db_login', 'my_db_pass');
// ERRORMODE
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
