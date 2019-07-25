<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] == 'BENEVOLE'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $evenement = $bdd->query('SELECT * FROM evenements WHERE id=\''.$_GET['id_event'].'\'');
            $evenement = $evenement->fetch();
?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8"/>
                    <title>GBoC - Liste des tâches</title>
                </head>
                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1>Liste des tâches</h1>
                        <h3>Evénement</h3>

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
                            <?php $evenement['comsparticipantes'] = str_replace('{', '(\'', $evenement['comsparticipantes']);
                            $evenement['comsparticipantes'] = str_replace(',', '\',\'', $evenement['comsparticipantes']);
                            $evenement['comsparticipantes'] = str_replace('}', '\')', $evenement['comsparticipantes']);
                            $commissions = $bdd->query('SELECT id, nom FROM commissions WHERE id IN'.$evenement['comsparticipantes']);?>
                            <tr>
                                <td><?php echo $evenement['nom']?></td>
                                <td><?php echo $evenement['description']?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($evenement['datedebut']))?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($evenement['datefin']))?></td>
                                <td><?php echo $evenement['lieux']?></td>
                                <td><?php echo $evenement['nbpersattendu']?></td>
                                <td>
                                <?php $donnees_comms = $commissions->fetch();
                                    echo $donnees_comms['nom'];
                                    while($donnees_comms = $commissions->fetch()) echo ', '.$donnees_comms['nom']?>
                                </td>
                            </tr>
                        </table>
                        <h3>Créer une nouvelle tâche pour l'événement</h3>
                        <form method="post" action="post_crud_tache.php" id="creer_tache">
                            Nom de la tâche:<br>
                            <input type="text" name="nom" required=""<?php if(isset($_GET['nom'])) echo 'value="'.str_replace('+',' ',$_GET['nom']).'"' ?> ><br>
                            Description de la tâche:<br>
                            <textarea rows="4" cols="50" name="description" form="creer_tache"><?php if(isset($_GET['description'])) echo str_replace('+',' ',$_GET['description'])?></textarea><br>
                            Date et heure de début:<br>
                            <input type="date" name="date_debut" required=""<?php if(isset($_GET['date_debut'])) echo 'value="'.$_GET['date_debut'].'"' ?>><input type="time" name="heure_debut" required=""<?php if(isset($_GET['heure_debut'])) echo 'value="'.$_GET['heure_debut'].'"' ?>><br>
                            Date et heure de fin:<br>
                            <input type="date" name="date_fin" required=""<?php if(isset($_GET['date_fin'])) echo 'value="'.$_GET['date_fin'].'"' ?>><input type="time" name="heure_fin" required=""<?php if(isset($_GET['heure_fin'])) echo 'value="'.$_GET['heure_fin'].'"' ?>><br>
                            Lieux de la tache (à la mission bretonne par défaut):<br>
                            <input type="text" name="lieux"<?php if(isset($_GET['lieux'])) echo 'value="'.str_replace('+',' ',$_GET['lieux']).'"' ?> ><br>
                            Nombre de bénévole:<br>
                            <input type="number" name="nbpersonne" required=""<?php if(isset($_GET['nbpersonne'])) echo 'value="'.$_GET['nbpersonne'].'"' ?>><br>
                            <input type="hidden" name="id_comm" value=<?php echo '"'.$_GET['id_comm'].'"'?>>
                            <input type="hidden" name="id_event" value=<?php echo '"'.$evenement['id'].'"'?>>
                            <input type="submit" name="create" value="Créer la tache">
                        </form>
                        <h3>Tâches créées</h3>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Description</td>
                                <td>Date et heure de début</td>
                                <td>Date et heure de fin</td>
                                <td>Lieux</td>
                                <td>Nombre de bénévoles max</td>
                                <td>Nombre de bénévole inscrit</td>
                            </tr>
                            <?php
                            $taches = $bdd->query('SELECT nom, description, datedebut, datefin, lieux, nbbenemax, array_length(beneinscrit,1) AS nbbene FROM taches WHERE evenement = \''.$_GET['id_event'].'\' AND commission = \''.$_GET['id_comm'].'\'');
                            while($donnees_tache=$taches->fetch()){
                                if($donnees_tache['nbbene'] == NULL) $donnees_tache['nbbene'] = 0?>
                                <tr>
                                    <td><?php echo $donnees_tache['nom']?></td>
                                    <td><?php echo $donnees_tache['description']?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($donnees_tache['datedebut']))?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($donnees_tache['datefin']))?></td>
                                    <td><?php echo $donnees_tache['lieux']?></td>
                                    <td><?php echo $donnees_tache['nbbenemax']?></td>
                                    <td><?php echo $donnees_tache['nbbene']?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>