<?php 
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
        if(!commission_verified($_GET['id'])){
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
                                <td>Lieu(x)</td>
                                <td>Nombre de personne attendu</td>
                                <td>Commissions participantes</td>
                            </tr>
                            <?php $events=$db->query('SELECT * FROM events WHERE end_time_event >\''.date("Y-m-d H:i").'\' AND \''.$_GET['id'].'\'=ANY(commissions)');
                            while($data_event = $events->fetch()){
                                $data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                                $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);
                                $data_commission = $commissions->fetch();?>
                                <tr>
                                    <td><?php echo $data_event['name_event'] ?></td>
                                    <td><?php echo $data_event['info_event'] ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_time_event'])) ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_time_event'])) ?></td>
                                    <td><?php echo $data_event['places_event'] ?></td>
                                    <td><?php echo $data_event['expected_people'] ?></td>
                                    <td><?php echo $data_commission['name_commission'];
                                        while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                                    </td>
                                    <td><form method="post" action=<?php echo '"commission_tasks.php?id_commission='.$_GET['id'].'&id_event='.$data_event['id_event'].'"' ?>>
                                        <input type="submit" name="tasks" value="Voir les tâches">
                                    </form>
                                </tr>
                            <?php } ?>
                        </table>

                        <h3> Evénement passé</h3>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Description</td>
                                <td>Date et heure de début</td>
                                <td>Date et heure de fin</td>
                                <td>Lieu(x)</td>
                                <td>Nombre de personne attendu</td>
                                <td>Commissions participantes</td>
                            </tr>
                            <?php $events=$db->query('SELECT * FROM events WHERE end_time_event <=\''.date("Y-m-d H:i").'\' AND \''.$_GET['id'].'\'=ANY(commissions)');
                            while($data_event = $events->fetch()){
                                $data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                                $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);
                                $data_commission = $commissions->fetch();?>
                                <tr>
                                    <td><?php echo $data_event['name_event'] ?></td>
                                    <td><?php echo $data_event['info_event'] ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_time_event'])) ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_time_event'])) ?></td>
                                    <td><?php echo $data_event['places_event'] ?></td>
                                    <td><?php echo $data_event['expected_people'] ?></td>
                                    <td><?php echo $data_commission['name_commission'];
                                        while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                                    </td>
                                    <td><form method="post" action=<?php echo '"commission_tasks.php?id_commission='.$_GET['id'].'&id_event='.$data_event['id_event'].'"' ?>>
                                        <input type="submit" name="tasks" value="Voir les tâches">
                                    </form>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </body>
            </html>
            <?php
    }
?>