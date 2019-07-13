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
                <title>GBoC - Désincription</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    Vous êtes sur le point de vous désincrire du site, si vous continuez, toutes les informations vous consernant serons supprimmées.
                    <form method="post" action="post_desincription.php">
                        <input type="submit" value="Continuer">
                    </form>
                    <form method="post" action="donnees_perso.php">
                        <input type="submit" value="Annuler">
                    </form>
                </div>
                <footer id="pied_de_page"></footer>
            </body>
        </html>
    <?php
    }
?>