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
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Chargé de commission</td>
                                <td>Mail</td>
                                <td>Numero de Téléphone</td>
                            </tr>
                            <?php
                            $commissions = $bdd->query('SELECT id, nom, moderateurs, active FROM commissions');
                            while($donnees_comms = $commissions->fetch()){
                                $moderateurs = $bdd->query('SELECT b.id, b.nom, prenom, mail, numerotel FROM benevoles AS b, commissions AS C WHERE c.id =\''.$donnees_comms['id'].'\' AND b.id = ANY (moderateurs)');
                                $benevoles = $bdd->query('SELECT b.id, b.nom, prenom, mail FROM benevoles AS b, commissions AS c WHERE c.id=\''.$donnees_comms['id'].'\' AND b.id != ALL (moderateurs)');
                                $moderateurs = $moderateurs->fetchall();
                                ?>
                                <tr>
                                    <td><?php echo $donnees_comms['nom']?></td>
                                    <td><?php echo $moderateurs[0]['nom'].' '.$moderateurs[0]['prenom'];
                                    for($i=1; $i<sizeof($moderateurs); $i++) echo '<br>'.$moderateurs[$i]['nom'].' '.$moderateurs[$i]['prenom']?><br></td>
                                    <td><?php echo $moderateurs[0]['mail'];
                                    for($i=1; $i<sizeof($moderateurs); $i++) echo '<br>'.$moderateurs[$i]['mail']?></td>
                                    <td><?php echo $moderateurs[0]['numerotel'];
                                    for($i=1; $i<sizeof($moderateurs); $i++) echo '<br>'.$moderateurs[$i]['numerotel']?></td>
                                    <td><?php echo '<form method="post" action="post_creer_comm.php">
                                            <input type="hidden" name="id_modo" value="'.$moderateurs[0]['id'].'">
                                            <input type="hidden" name="id_comm" value="'.$donnees_comms['id'].'">
                                            <input type="submit" name="remove_modo" value="retirer moderateur">
                                        </form>';
                                        for($i=1; $i<sizeof($moderateurs); $i++) echo '<form method="post" action="post_creer_comm.php">
                                            <input type="hidden" name="id_modo" value="'.$moderateurs[$i]['id'].'">
                                            <input type="hidden" name="id_comm" value="'.$donnees_comms['id'].'">
                                            <input type="submit" name="remove_modo" value="retirer moderateur">
                                        </form>'?></td>
                                    <?php if($donnees_comms['active']){ echo'
                                        <td><form method="post" action="commission_benevoles.php?id='.$donnees_comms['id'].'">
                                            <input type="submit" value="voir la liste des bénévoles participant">
                                        </form></td>
                                        <td><form method="post" action="post_creer_comm.php">
                                            <input type="text" list="benevoles_'.$donnees_comms['nom'].'" name="moderateur" required=""><br>
                                            <datalist id="benevoles_'.$donnees_comms['nom'].'">';
                                                while($donnees_benevoles=$benevoles->fetch()){
                                                    echo '<option value="'.$donnees_benevoles['nom'].' '.$donnees_benevoles['prenom'].' ('.$donnees_benevoles['mail'].')"></option>';
                                                }
                                            echo '</datalist>
                                            <input type="hidden" name="id_comm" value="'.$donnees_comms['id'].'">
                                            <input type="submit" name="add_modo" value="Ajouter un moderateur">
                                        </form></td>';}
                                    ?>
                                    <td><form method="post" action="post_creer_comm.php">
                                        <input type="hidden" name="id_comm" value=<?php echo '"'.$donnees_comms['id'].'"'?>>
                                        <input type="submit" <?php if($donnees_comms['active']) echo 'name="desactive_comm" value="desactiver commission"'; else echo 'name="reactive_comm" value="reactiver commission"'?>>
                                    </form>
                                </tr><?php
                                $benevoles->closeCursor();
                            }
                            $commissions->closeCursor(); ?>
                        </table>
                        <h2>Créer une nouvelle commission</h2>
                        <?php $benevoles = $bdd->query('SELECT id, nom, prenom, mail FROM benevoles');?>
                        <form method="post" action="post_creer_comm.php">
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