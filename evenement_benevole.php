<?php session_start();
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
 
    <!-- L'en-tête -->
    
    <header>
       
    </header>
    
    <!-- Le menu -->
    <?php include("menus.php"); ?>
    <!-- Le corps -->
    <div id="corps">
    <h1>Liste des événements à venir</h1>
    <a href="accueil.php">Retourner à la page de connexion</a><br>


    </div>
    
    <!-- Le pied de page -->
    
    <footer id="pied_de_page">
    </footer>
    
    </body>
</html>