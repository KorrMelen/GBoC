<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    $event = $db->query('SELECT * FROM events WHERE id_event=\''.$_GET['id_event'].'\'');
    $event = $event->fetch();
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
                    <td>Lieu(x)</td>
                </tr>
                <tr>
                    <td><?php echo $event['name_event']?></td>
                    <td><?php echo $event['info_event']?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['begin_time_event']))?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['end_time_event']))?></td>
                    <td><?php echo $event['places_event']?></td>
                </tr>
            </table>
            <h3>Tâches</h3>
            <?php $event['commissions'] = str_replace('{', '(\'', $event['commissions']);
            $event['commissions'] = str_replace(',', '\',\'', $event['commissions']);
            $event['commissions'] = str_replace('}', '\')', $event['commissions']);
            $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$event['commissions'].' AND \''.$_SESSION['uuid'].'\'=ANY(volunteers)');
            while($data_commmission = $commissions->fetch()){
                $tasks = $db->query('SELECT id_task, name_task, info_task, begin_time_task, end_time_task, places_task, max_volunteers, array_length(registered_volunteers,1) AS volunteers FROM tasks WHERE event = \''.$_GET['id_event'].'\' AND commission = \''.$data_commmission['id_commission'].'\' AND \''.$_SESSION['uuid'].'\' != ALL (registered_volunteers)')?>
                <h4>Commission <?php echo $data_commmission['name_commission']?></h4>
                <table>
                    <tr>
                        <td>Nom</td>
                        <td>Description</td>
                        <td>Date et heure de début</td>
                        <td>Date et heure de fin</td>
                        <td>Lieu</td>
                        <td>Nombre de bénévoles manquant</td>
                    </tr>
                    <?php
                    while($data_task=$tasks->fetch()){?>
                        <tr>
                            <td><?php echo $data_task['name_task']?></td>
                            <td><?php echo $data_task['info_task']?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['begin_time_task']))?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['end_time_task']))?></td>
                            <td><?php echo $data_task['places_task']?></td>
                            <td><?php echo $data_task['max_volunteers']-$data_task['volunteers']?></td>
                            <td><form method="post" action="post_crud_task.php">
                                <input type="hidden" name="id_volunteer" value=<?php echo'"'.$_SESSION['uuid'].'"'?>>
                                <input type="hidden" name="id_task" value=<?php echo'"'.$data_task['id_task'].'"'?>>
                                <input type="hidden" name="id_event" value=<?php echo'"'.$_GET['id_event'].'"'?>>
                                <input type="submit" name="undertaking" value="S'engager">
                            </form></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </body>
</html>