<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Liste des engagements</title>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h1>Liste des engagements</h1>
            <table>
                <tr>
                    <td>tache</td>
                    <td>événement</td>
                    <td>commission</td>
                </tr>
                <?php $tasks=$db->query('SELECT id_task, name_task, name_commission, name_event FROM tasks, commissions, events WHERE id_commission = commission AND id_event = event AND \''.$_SESSION['uuid'].'\' = ANY (registered_volunteers) ORDER BY event, commission');
                while($data_task = $tasks->fetch()){?>
                    <tr>
                        <td><?php echo $data_task['name_task']?></td>
                        <td><?php echo $data_task['name_event']?></td>
                        <td><?php echo $data_task['name_commission']?></td>
                        <td><form method="post" action=<?php echo '"task.php?id_task='.$data_task['id_task'].'"'?>>
                            <input type="submit" name="task" value="Voir la tâche">
                        </form></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </body>
</html>