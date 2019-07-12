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
        <title>GBoC - Liste des bénévoles</title>
    </head>
 
    <body>
 
    <!-- L'en-tête -->
    
    <header>
       
    </header>
    
    <!-- Le menu -->
    <?php include("menus.php"); ?>
    <!-- Le corps -->
    <div id="corps">
    <h1>Liste des bénévoles</h1>
    <table>
        <tr>
            <td>Nom</td>
            <td>Prénom</td>
            <td>Mail</td>
            <td>Numero de Téléphone</td>
            <td>Date de Naissance</td>
            <td>Commissions</td>
            <td>rôle</td>
        </tr>
    <?php 
            if(isset($_POST['id'])){
                $reponse = $bdd->query('SELECT b.id, b.nom, prenom, datenaissance, numerotel, mail, role FROM benevoles AS b, commissions WHERE commissions.id = \''.$_POST['id'].'\' AND b.id = ANY (listbenevoles)');
            }else{
                $reponse = $bdd->query('SELECT id, nom, prenom, datenaissance, numerotel, mail, role FROM benevoles');
            }
            while($donnees = $reponse->fetch()){
                $commissions = $bdd->query('SELECT nom FROM commissions WHERE \''.$donnees['id'].'\' = ANY (listbenevoles)');?>
                <tr>
                    <td><font style="text-transform: uppercase;"><?php echo $donnees['nom']?></font></td>
                    <td><font style="text-transform: capitalize;"><?php echo $donnees['prenom']?></font></td>
                    <td><?php echo $donnees['mail']?></td>
                    <td><?php echo $donnees['numerotel']?></td>
                    <td><?php echo $donnees['datenaissance']?></td>
                    <td><?php $com = $commissions->fetch();
                    echo $com['nom'];
                    while($com = $commissions->fetch()) echo ', '.$com['nom']?></td>
                    <td><?php
                    if($donnees['role'] == 'MODERATEUR'){
                        $moderateur = $bdd->query('SELECT nom FROM commissions WHERE moderateur = \''.$donnees['id'].'\'');
                        $modo = $moderateur->fetch();
                        echo $donnees['role'].'<br>';
                        echo "(".$modo['nom'].")";
                    }else{ ?>
                        <form method="post" action="post_edit_role.php">
                            <select name="role" size="1">
                                <option <?php if($donnees['role'] == 'BENEVOLE') echo "selected"; ?> > BENEVOLE
                                <option <?php if($donnees['role'] == 'ADMIN') echo "selected"; ?> > ADMIN
                            </select>
                            <input type="hidden" name="id" value=<?php echo'"'.$donnees['id'].'"'?>>
                            <input type="submit" value="Changer le role">
                        </form>
                    <?php } ?>
                    </td>
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