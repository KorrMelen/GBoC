<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] != 'ADMIN'){
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
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1>Liste des commissions</h1>
                        <?php $commissions = $bdd->query('SELECT id, nom, active FROM commissions');
                        while($donnees_comms = $commissions->fetch()){
                            echo '<a href="commission.php?id='.$donnees_comms['id'].'">'.$donnees_comms['nom'].'</a> Cette commission ';
                            if($donnees_comms['active']) echo "est active.<br>"; else echo "n'est pas active <br>";
                        } ?>
                        <h2>Créer une nouvelle commission</h2>
                        <?php $benevoles = $bdd->query('SELECT id, nom, prenom, mail FROM benevoles');?>
                        <form method="post" action="post_crud_comm.php">
                            Nom de la commission<br>
                            <input type="text" name="nom" required=""><br>
                            Un moderateur<br>
                            <input type="text" list="benevole" name="moderateur" required=""><br>
                            <datalist id="benevole">
                                <?php while($donnees_benevoles=$benevoles->fetch()){
                                    echo '<option value="'.$donnees_benevoles['nom'].' '.$donnees_benevoles['prenom'].' ('.$donnees_benevoles['mail'].')"></option>';
                                }?>
                            </datalist>
                            <input type="submit" name="add_comm" value="Ajouter une commission">
                        </form>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>