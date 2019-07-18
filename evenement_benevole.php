<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
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