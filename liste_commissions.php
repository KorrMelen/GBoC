<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        try{
            $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
        }catch (Exception $e){
            die('Erreur : ' . $e->getMessage());
        }
        $check = $bdd->prepare('SELECT role FROM benevoles WHERE id=:id');
        $check->execute(array('id'=> $_SESSION['uuid']));
        $check = $check->fetch();
        if($check['role'] != 'ADMIN'){
            echo $check['role'];
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Liste des commissions</title>
    </head>
 
    <body>
 
    <!-- L'en-tête -->
    
    <header>
       
    </header>
    
    <!-- Le menu -->
    <?php include("menus.php"); ?>
    <!-- Le corps -->
    <div id="corps">
    <h1>Liste des commissions</h1>
    <table>
        <tr>
            <td>Nom</td>
            <td>Chargé de commission</td>
            <td>Mail</td>
            <td>Numero de Téléphone</td>
        </tr>
    <?php
    $reponse = $bdd->query('SELECT id, nom, moderateur FROM commissions');
            while($donnees = $reponse->fetch()){
                $moderateur = $bdd->query('SELECT id, nom, prenom, mail, numerotel FROM benevoles WHERE id = \''.$donnees['moderateur'].'\'');
                $modo = $moderateur->fetch();?>
                <tr>
                    <td><?php echo $donnees['nom']?></td>
                    <td><?php echo "<font style=\"text-transform: uppercase;\">".$modo['nom'].'</font> <font style="text-transform: capitalize;">'.$modo['prenom'].'</font>'?></td>
                    <td><?php echo $modo['mail']?></td>
                    <td><?php echo $modo['numerotel']?></td>
                    <td><form method="post" action="liste_all_benevoles.php">
                        <input type="hidden" name="id" value=<?php echo'"'.$donnees['id'].'"'?>>
                        <input type="submit" value="voir la liste des bénévoles participant">
                    </form></td>
                </tr><?php
            }
            $reponse->closeCursor();
        }
    }
    ?>

    </div>
    
    <!-- Le pied de page -->
    
    <footer id="pied_de_page">
    </footer>
    
    </body>
</html>