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
                    <title>GBoC - Liste des bénévoles</title>
                </head>
             
                <body>
                    <?php include("menus.php"); ?>
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
                                $benevoles = $bdd->query('SELECT id, nom, prenom, datenaissance, numerotel, mail, role FROM benevoles');
                                while($donnees_bene = $benevoles->fetch()){
                                    $commissions = $bdd->query('SELECT nom FROM commissions WHERE \''.$donnees_bene['id'].'\' = ANY (listbenevoles)');?>
                                    <tr>
                                        <td><?php echo $donnees_bene['nom']?></td>
                                        <td><?php echo $donnees_bene['prenom']?></td>
                                        <td><?php echo $donnees_bene['mail']?></td>
                                        <td><?php echo $donnees_bene['numerotel']?></td>
                                        <td><?php echo $donnees_bene['datenaissance']?></td>
                                        <td><?php $donnees_comms = $commissions->fetch();
                                        echo $donnees_comms['nom'];
                                        while($donnees_comms = $commissions->fetch()) echo ', '.$donnees_comms['nom']?></td>
                                        <td><?php
                                        echo $donnees_bene['role'].'<br>';
                                        if($donnees_bene['role'] == 'MODERATEUR'){
                                            $moderateur = $bdd->query('SELECT nom FROM commissions WHERE \''.$donnees_bene['id'].'\' = ANY(moderateurs)');
                                            $donnees_modo = $moderateur->fetch();
                                            echo "(".$donnees_modo['nom'].")";
                                        }?>
                                        </td>
                                    </tr><?php
                                }
                                $benevoles->closeCursor();
                            ?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>