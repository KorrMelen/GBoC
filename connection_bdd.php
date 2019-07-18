<?php
try{
    $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
}catch (Exception $e){
    die('Erreur : ' . $e->getMessage());
}
?>