<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
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
                            <td>Commissions participantes</td>
                        </tr>
                        <tr>
                            <td><?php echo $evenement['nom']?></td>
                            <td><?php echo $evenement['description']?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($evenement['datedebut']))?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($evenement['datefin']))?></td>
                            <td><?php echo $evenement['lieux']?></td>
                        </tr>
                    </table>
                    <h3>Tâches</h3>
                    <?php $evenement['comsparticipantes'] = str_replace('{', '(\'', $evenement['comsparticipantes']);
                    $evenement['comsparticipantes'] = str_replace(',', '\',\'', $evenement['comsparticipantes']);
                    $evenement['comsparticipantes'] = str_replace('}', '\')', $evenement['comsparticipantes']);
                    $commissions = $bdd->query('SELECT id, nom FROM commissions WHERE id IN'.$evenement['comsparticipantes'].' AND \''.$_SESSION['uuid'].'\'=ANY(listbenevoles)');
                    while($donnees_comm = $commissions->fetch()){
                        $taches = $bdd->query('SELECT id, nom, description, datedebut, datefin, lieux, nbbenemax, array_length(beneinscrit,1) AS bene FROM taches WHERE evenement = \''.$_GET['id_event'].'\' AND commission = \''.$donnees_comm['id'].'\' AND \''.$_SESSION['uuid'].'\' != ALL (beneinscrit)')?>
                        <h4>Commission <?php echo $donnees_comm['nom']?></h4>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Description</td>
                                <td>Date et heure de début</td>
                                <td>Date et heure de fin</td>
                                <td>Lieux</td>
                                <td>Nombre de bénévoles manquant</td>
                            </tr>
                            <?php
                            while($donnees_tache=$taches->fetch()){?>
                                <tr>
                                    <td><?php echo $donnees_tache['nom']?></td>
                                    <td><?php echo $donnees_tache['description']?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($donnees_tache['datedebut']))?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($donnees_tache['datefin']))?></td>
                                    <td><?php echo $donnees_tache['lieux']?></td>
                                    <td><?php echo $donnees_tache['nbbenemax']-$donnees_tache['bene']?></td>
                                    <td><form method="post" action="post_crud_tache.php">
                                        <input type="hidden" name="id_bene" value=<?php echo'"'.$_SESSION['uuid'].'"'?>>
                                        <input type="hidden" name="id_tache" value=<?php echo'"'.$donnees_tache['id'].'"'?>>
                                        <input type="hidden" name="id_event" value=<?php echo'"'.$_GET['id_event'].'"'?>>
                                        <input type="submit" name="engagement" value="S'engager">
                                    </form></td>
                                </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>
                </div>
                <footer id="pied_de_page"></footer>
            </body>
        </html>
        <?php
    }
?>