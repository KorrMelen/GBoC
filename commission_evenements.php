<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] == 'BENEVOLE'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
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
                                <td>Nombre de personne attendu</td>
                                <td>Commissions participantes</td>
                            </tr>
                            <?php $evenements=$bdd->query('SELECT * FROM evenements WHERE datefin >\''.date("Y-m-d H:i").'\' AND \''.$_GET['id'].'\'=ANY(comsparticipantes)');
                            while($donnees_event = $evenements->fetch()){
                                $donnees_event['comsparticipantes'] = str_replace('{', '(\'', $donnees_event['comsparticipantes']);
                                $donnees_event['comsparticipantes'] = str_replace(',', '\',\'', $donnees_event['comsparticipantes']);
                                $donnees_event['comsparticipantes'] = str_replace('}', '\')', $donnees_event['comsparticipantes']);
                                $commissions = $bdd->query('SELECT id, nom FROM commissions WHERE id IN'.$donnees_event['comsparticipantes']);
                                echo '<tr>
                                    <td>'.$donnees_event['nom'].'</td>
                                    <td>'.$donnees_event['description'].'</td>
                                    <td>'.date("d/m/Y H:i", strtotime($donnees_event['datedebut'])).'</td>
                                    <td>'.date("d/m/Y H:i", strtotime($donnees_event['datefin'])).'</td>
                                    <td>'.$donnees_event['lieux'].'</td>
                                    <td>'.$donnees_event['nbpersattendu'].'</td>
                                    <td>';
                                    $donnees_comms = $commissions->fetch();
                                        echo $donnees_comms['nom'];
                                        while($donnees_comms = $commissions->fetch()) echo ', '.$donnees_comms['nom'];
                                    echo '</td><td><form method="post" action="commission_taches.php?id_event='.$donnees_event['id'].'&id_comm='.$_GET['id'].'">
                                        <input type="submit" name="taches" value="Voire les tâches">
                                    </form>
                                </tr>';
                            }?>
                        </table>
                        <h3> Evénement passé</h3>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Description</td>
                                <td>Date et heure de début</td>
                                <td>Date et heure de fin</td>
                                <td>Lieux</td>
                                <td>Nombre de personne attendu</td>
                                <td>Commissions participantes</td>
                            </tr>
                            <?php $evenements=$bdd->query('SELECT * FROM evenements WHERE datefin <=\''.date("Y-m-d H:i").'\' AND \''.$_GET['id'].'\'=ANY(comsparticipantes)');
                            while($donnees_event = $evenements->fetch()){
                                $donnees_event['comsparticipantes'] = str_replace('{', '(\'', $donnees_event['comsparticipantes']);
                                $donnees_event['comsparticipantes'] = str_replace(',', '\',\'', $donnees_event['comsparticipantes']);
                                $donnees_event['comsparticipantes'] = str_replace('}', '\')', $donnees_event['comsparticipantes']);
                                $commissions = $bdd->query('SELECT id, nom FROM commissions WHERE id IN'.$donnees_event['comsparticipantes']);
                                echo '<tr>
                                    <td>'.$donnees_event['nom'].'</td>
                                    <td>'.$donnees_event['description'].'</td>
                                    <td>'.date("d/m/Y H:i", strtotime($donnees_event['datedebut'])).'</td>
                                    <td>'.date("d/m/Y H:i", strtotime($donnees_event['datefin'])).'</td>
                                    <td>'.$donnees_event['lieux'].'</td>
                                    <td>'.$donnees_event['nbpersattendu'].'</td>
                                    <td>';
                                        $donnees_comms = $commissions->fetch();
                                        echo $donnees_comms['nom'];
                                        while($donnees_comms = $commissions->fetch()) echo ', '.$donnees_comms['nom'];
                                echo '</td><td><form method="post" action="commission_taches.php">
                                        <input type="hidden" name="id_comm" value="'.$_GET['id'].'">
                                        <input type="hidden" name="id_event" value="'.$donnees_event['id'].'">
                                        <input type="submit" name="taches" value="Voire les tâches">
                                    </form>
                                </tr>';
                            }?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>