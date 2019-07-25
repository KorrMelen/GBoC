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
                <title>GBoC - Liste des événements</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    <h1>Liste des événements</h1>
                    <h3>Evénement à venir</h3>
                    <table>
                        <tr>
                            <td>Nom</td>
                            <td>Description</td>
                            <td>Date et heure de début</td>
                            <td>Date et heure de fin</td>
                            <td>Lieux</td>
                        </tr>
                        <?php $evenements=$bdd->query('SELECT DISTINCT e.id, e.nom, description, datedebut, datefin, lieux FROM evenements AS e, commissions AS c WHERE datefin >\''.date("Y-m-d H:i").'\' AND c.id IN (SELECT id FROM commissions WHERE \''.$_SESSION['uuid'].'\' = ANY (listbenevoles)) AND c.id = ANY (comsparticipantes)');
                        while($donnees_event = $evenements->fetch()){
                            echo '<tr>
                                <td>'.$donnees_event['nom'].'</td>
                                <td>'.$donnees_event['description'].'</td>
                                <td>'.date("d/m/Y H:i", strtotime($donnees_event['datedebut'])).'</td>
                                <td>'.date("d/m/Y H:i", strtotime($donnees_event['datefin'])).'</td>
                                <td>'.$donnees_event['lieux'].'</td>
                                <td><form method="post" action="benevole_taches.php?id_event='.$donnees_event['id'].'">
                                    <input type="submit" name="taches" value="Voire les tâches">
                                </form></td>
                            </tr>';
                        }?>
                    </table>
                </div>
                <footer id="pied_de_page"></footer>
            </body>
        </html>
        <?php
    }
?>    