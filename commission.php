<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] != 'ADMIN'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $commission = $bdd->query('SELECT * FROM commissions WHERE id=\''.$_GET['id'].'\'');
            $commission = $commission->fetch();
?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>GBoC - Commission</title>
                </head>
                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <table>
                            <tr>
                                <td>Chargé de commission</td>
                                <td>Mail</td>
                                <td>Numero de Téléphone</td>
                            </tr>
                            <?php
                            $moderateurs = $bdd->query('SELECT b.id, b.nom, prenom, mail, numerotel FROM benevoles AS b, commissions AS C WHERE c.id =\''.$commission['id'].'\' AND b.id = ANY (moderateurs)');
                            while($donnees_moderateur = $moderateurs->fetch()){?>
                                <tr>
                                    <td><?php echo $donnees_moderateur['nom'].' '.$donnees_moderateur['prenom']?></td>
                                    <td><?php echo $donnees_moderateur['mail']?></td>
                                    <td><?php echo $donnees_moderateur['numerotel']?></td>
                                    <?php if($moderateurs->rowcount() > 1){?>
                                        <td><form method="post" action="post_crud_comm.php">
                                            <input type="hidden" name="id_modo" value=<?php echo'"'.$donnees_moderateur['id'].'"'?>>
                                            <input type="hidden" name="id_comm" value=<?php echo'"'.$commission['id'].'"'?>>
                                            <input type="submit" name="remove_modo" value="Retirer moderateur">
                                        </form></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </table>
                        <form method="post" action="post_crud_comm.php">
                            <input type="text" list="benevoles" name="moderateur" size="35" required=""><br>
                            <datalist id="benevoles">
                                <?php $benevoles = $bdd->query('SELECT b.id, b.nom, prenom, mail FROM benevoles AS b, commissions AS c WHERE c.id=\''.$commission['id'].'\' AND b.id != ALL (moderateurs)');
                                while($donnees_benevoles=$benevoles->fetch()){
                                    echo '<option value="'.$donnees_benevoles['nom'].' '.$donnees_benevoles['prenom'].' ('.$donnees_benevoles['mail'].')"></option>';
                                } ?>
                            </datalist>
                            <input type="hidden" name="id_comm" value=<?php echo '"'.$commission['id'].'"'?>>
                            <input type="submit" name="add_modo" value="Ajouter un moderateur">
                        </form>
                        <?php if($commission['active']){ ?>
                            <form method="post" action=<?php echo '"commission_benevoles.php?id='.$commission['id'].'"'?>>
                                <input type="submit" value="Voir la liste des bénévoles participant">
                            </form>
                            <form method="post" action=<?php echo '"commission_evenements.php?id='.$commission['id'].'"'?>>
                                <input type="submit" value="Voir la liste des taches créer">
                            </form>
                        <?php } ?>
                        <form method="post" action="post_crud_comm.php">
                            <input type="hidden" name="id_comm" value=<?php echo '"'.$commission['id'].'"'?>>
                            <input type="submit" <?php if($commission['active']) echo 'name="desactive_comm" value="desactiver commission"'; else echo 'name="reactive_comm" value="reactiver commission"'?>>
                        </form>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>