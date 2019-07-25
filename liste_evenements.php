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
                    <title>GBoC - Liste des événements</title>
                </head>
                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1>Liste des événements</h1>
                        <h3>Créer un événement</h3>
                        <form method="post" action="post_crud_evenement.php" id="creer_eve">
                            Nom de l'événemnt:<br>
                            <input type="text" name="nom" required=""<?php if(isset($_GET['nom'])) echo 'value="'.str_replace('+',' ',$_GET['nom']).'"' ?> ><br>
                            Description de l'événement:<br>
                            <textarea rows="4" cols="50" name="description" form="creer_eve"><?php if(isset($_GET['description'])) echo str_replace('+',' ',$_GET['description'])?></textarea><br>
                            Date et heure de début:<br>
                            <input type="date" name="date_debut" required=""<?php if(isset($_GET['date_debut'])) echo 'value="'.$_GET['date_debut'].'"' ?>><input type="time" name="heure_debut" required=""<?php if(isset($_GET['heure_debut'])) echo 'value="'.$_GET['heure_debut'].'"' ?>><br>
                            Date et heure de fin:<br>
                            <input type="date" name="date_fin" required=""<?php if(isset($_GET['date_fin'])) echo 'value="'.$_GET['date_fin'].'"' ?>><input type="time" name="heure_fin" required=""<?php if(isset($_GET['heure_fin'])) echo 'value="'.$_GET['heure_fin'].'"' ?>><br>
                            Lieux de l'événement (à la mission bretonne par défaut):<br>
                            <input type="text" name="lieux"<?php if(isset($_GET['lieux'])) echo 'value="'.str_replace('+',' ',$_GET['lieux']).'"' ?> ><br>
                            Nombre de personne attendu:<br>
                            <input type="number" name="nbpersonne"<?php if(isset($_GET['nbpersonne'])) echo 'value="'.$_GET['nbpersonne'].'"' ?>><br>
                            Commissions participantes:<br>
                            <?php
                                $commissions = $bdd->query('SELECT * FROM commissions WHERE active');
                                while($donnees_comms = $commissions->fetch()){
                                    echo '<input type="checkbox" name ="'.$donnees_comms['nom'].'" value="'.$donnees_comms['id'].'">'.$donnees_comms['nom'].'<br>';
                                }
                            ?>
                            <input type="submit" value="Créer l'événement">
                        </form>
                        <?php if(isset($_GET['error']) && $_GET['error'] == 'date'){
                            echo "Attention, l'événement se termine avant qu'il ne commance";
                        }?>
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
                            <?php $evenements=$bdd->query('SELECT * FROM evenements WHERE datefin >\''.date("Y-m-d H:i").'\'');
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
                                    echo '</td></tr>';
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
                            <?php $evenements=$bdd->query('SELECT * FROM evenements WHERE datefin <=\''.date("Y-m-d H:i").'\'');
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
                                echo '</td></tr>';
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