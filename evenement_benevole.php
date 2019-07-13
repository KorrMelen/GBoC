<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        try{
           $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
        }catch (Exception $e){
           die('Erreur : ' . $e->getMessage());
        }
?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>GBoC - événement à venir</title>
            </head>
            <body>
            <!-- Le menu -->
                <?php include("menus.php"); ?>
            <!-- Le corps -->
                <div id="corps">
                    <h1>Liste des événements à venir</h1>
                </div>
                <footer id="pied_de_page"></footer>
            </body>
        </html>
    <?php
    }
?>