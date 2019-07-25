<?php session_start();
	 if(!isset($_SESSION['uuid'])){
        header('location: accueil.php?test=test');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] == 'BENEVOLE'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $commission= $bdd->query('SELECT * FROM commissions WHERE id=\''.$_GET['id'].'\'');
            $donnees_comm = $commission->fetch();
?>

            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>GBoC - Liste des benevoles</title>
                </head>
                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1><?php echo"Liste des Benevoles de la commission ".$donnees_comm['nom']?></h1>
                        <h3>Benevoles voulant participer à la commission</h3>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Prénom</td>
                                <td>Mail</td>
                                <td>Numero de Téléphone</td>
                                <td>Date de Naissance</td>
                                <td>Commissions</td>
                            </tr>
                            <?php
                            $benevoles = $bdd->query('SELECT b.id, b.nom, prenom, datenaissance, numerotel, mail, role FROM benevoles AS b, commissions AS c WHERE c.id = \''.$_GET['id'].'\' AND b.id = ANY (benevolesattente)');
                            while($donnees_benevole = $benevoles->fetch()){
                                $commissions = $bdd->query('SELECT nom FROM commissions WHERE \''.$donnees_benevole['id'].'\' = ANY (listbenevoles)');?>
                                <tr>
                               		<td><?php echo $donnees_benevole['nom']?></td>
                                    <td><?php echo $donnees_benevole['prenom']?></td>
                                    <td><?php echo $donnees_benevole['mail']?></td>
                                    <td><?php echo $donnees_benevole['numerotel']?></td>
                                    <td><?php echo date("d/m/Y", strtotime($donnees_benevole['datenaissance']))?></td>
                                    <td><?php $donnees_comms = $commissions->fetch();
                                    echo $donnees_comms['nom'];
                                    while($donnees_comms = $commissions->fetch()) echo ', '.$donnees_comms['nom']?></td>
                                    <td><form method="post" action="post_accepte_bene.php">
                                        <input type="hidden" name="id_bene" value=<?php echo'"'.$donnees_benevole['id'].'"'?>>
                                        <input type="hidden" name="id_comm" value=<?php echo'"'.$donnees_comm['id'].'"'?>>
                                        <input type="submit" name="attente" value="Accepter">
                                        <input type="submit" name="attente" value="Refuser">
                                    </form></td>
                                </tr><?php
                            } ?>
                        </table>
                        <h3>Benevoles participant à la commission</h3>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Prénom</td>
                                <td>Mail</td>
                                <td>Numero de Téléphone</td>
                                <td>Date de Naissance</td>
                                <td>Commissions</td>
                            </tr>
                            <?php
                            $benevoles = $bdd->query('SELECT b.id, b.nom, prenom, datenaissance, numerotel, mail, role FROM benevoles AS b, commissions AS c WHERE c.id = \''.$_GET['id'].'\' AND b.id = ANY (listbenevoles)');
                            while($donnees_benevole = $benevoles->fetch()){
                                $commissions = $bdd->query('SELECT nom FROM commissions WHERE \''.$donnees_benevole['id'].'\' = ANY (listbenevoles)');?>
                                <tr>
                                    <td><?php echo $donnees_benevole['nom']?></td>
                                    <td><?php echo $donnees_benevole['prenom']?></td>
                                    <td><?php echo $donnees_benevole['mail']?></td>
                                    <td><?php echo $donnees_benevole['numerotel']?></td>
                                    <td><?php echo date("d/m/Y", strtotime($donnees_benevole['datenaissance']))?></td>
                                    <td><?php $donnees_comms = $commissions->fetch();
                                    echo $donnees_comms['nom'];
                                    while($donnees_comms = $commissions->fetch()) echo ', '.$donnees_comms['nom']?></td>
                                    <td><form method="post" action="post_accepte_bene.php">
                                        <input type="hidden" name="id_bene" value=<?php echo'"'.$donnees_benevole['id'].'"'?>>
                                        <input type="hidden" name="id_comm" value=<?php echo'"'.$donnees_comm['id'].'"'?>>
                                        <input type="submit" name="renvoie" value="désincrire de la commission">
                                    </form></td>
                                </tr><?php
                            } ?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>