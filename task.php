<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    $task = $db->query('SELECT *, array_length(registered_volunteers,1) AS volunteers FROM tasks WHERE id_task=\''.$_GET['id_task'].'\'');
    $task = $task->fetch();
    $moderators = $db->query('SELECT id_commission FROM commissions WHERE id_commission = \''.$task['commission'].'\' AND \''.$_SESSION['uuid'].'\' = ANY (moderators)');
    $moderator = ($moderators->rowCount() > 0 || $_SESSION['role'] == 'ADMIN')
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Tâche</title>
        <link rel="stylesheet" type="text/css" href="stylechat.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="chat.js"></script>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h1>Tâche</h1>
            <h3>Evénement:</h3>
            <table>
                <tr>
                    <td>Nom</td>
                    <td>Description</td>
                    <td>Date et heure de début</td>
                    <td>Date et heure de fin</td>
                    <td>Lieu(x)</td>
                </tr>
                <?php $event=$db->query('SELECT * FROM events WHERE id_event=\''.$task['event'].'\'');
                $event = $event->fetch()?>
                <tr>
                    <td><?php echo $event['name_event'] ?></td>
                    <td><?php echo $event['info_event'] ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['begin_time_event'])) ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['end_time_event'])) ?></td>
                    <td><?php echo $event['places_event'] ?></td>
            </table>

            <h3>Tache:</h3>
            <?php if($moderator){ ?>
                <form method="post" action="post_crud_task.php" id="create_task">
                    Nom de la tâche:<br>
                    <input type="text" name="name" required="" value=<?php echo '"'.$task['name_task'].'"'?> ><br>
                    Description de la tâche:<br>
                    <textarea rows="4" cols="50" name="info" form="create_task"><?php echo $task['info_task'] ?></textarea><br>
                    <table>
                        <tr>
                            <td>Date et heure de début:</td>
                            <td>Date et heure de fin:</td>
                        </tr>
                        <tr>
                            <td><input type="date" name="begin_date" required="" value=<?php echo '"'.date('Y-m-d', strtotime($task['begin_time_task'])).'"' ?>>
                                <input type="time" name="begin_time" required="" value=<?php echo '"'.date('H:i', strtotime($task['begin_time_task'])).'"' ?>></td>
                            <td><input type="date" name="end_date" required="" value=<?php echo '"'.date('Y-m-d', strtotime($task['end_time_task'])).'"' ?>>
                                <input type="time" name="end_time" required="" value=<?php echo '"'.date('H:i', strtotime($task['end_time_task'])).'"' ?>></td>
                        </tr>
                    </table>
                    Lieu de la tâche (à la mission bretonne par défaut):<br>
                    <input type="text" name="places" value=<?php echo '"'.$task['places_task'].'"' ?>><br>
                    Nombre de bénévole:<br>
                    <input type="number" name="max_volunteers" required="" value=<?php echo '"'.$task['max_volunteers'].'"' ?>><br>
                    <input type="hidden" name="id_commission" value=<?php echo '"'.$task['commission'].'"'?>>
                    <input type="hidden" name="id_task" value=<?php echo '"'.$task['id_task'].'"'?>>
                    <input type="submit" name="update" value="Modifier la tache">
                </form>
            <?php }else{ ?>
                <table>
                    <tr>
                        <td>Nom</td>
                        <td>Description</td>
                        <td>Date et heure de début</td>
                        <td>Date et heure de fin</td>
                        <td>Lieu</td>
                        <td>Nombre de bénévoles manquant</td>
                    </tr>
                    <tr>
                        <td><?php echo $task['name_task']?></td>
                        <td><?php echo $task['info_task']?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($task['begin_time_task']))?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($task['end_time_task']))?></td>
                        <td><?php echo $task['places_task']?></td>
                        <td><?php echo $task['max_volunteers']-$task['volunteers']?></td>
                    </tr>
                </table>
            <?php } ?>
            <h3>Bénévoles</h3>
            <table>
                <tr>
                    <td>Nom</td>
                    <td>Prénom</td>
                </tr>
                <?php $volunteers=$db->query('SELECT id_volunteer, name_volunteer, surname_volunteer FROM volunteers, tasks WHERE id_task=\''.$task['id_task'] .'\' AND id_volunteer = ANY (registered_volunteers)');
                while($volunteer=$volunteers->fetch()){ ?>
                    <tr>
                        <td><?php echo $volunteer['name_volunteer']?></td>
                        <td><?php echo $volunteer['surname_volunteer']?></td>
                        <?php if($moderator){?>
                            <td><form method="POST" action="post_crud_task.php">
                                <input type="hidden" name="id_volunteer" value=<?php echo '"'.$volunteer['id_volunteer'].'"'?>>
                                <input type="hidden" name="id_task" value=<?php echo '"'.$task['id_task'].'"'?>>
                                <input type="submit" name="unsubscribe" value="désinscrire de la tâche">
                            </form></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>

            <h3>Chat</h3>
            <table class="chat"><tr>        
            <!-- zone des messages -->
                <td valign="top" id="text-td">
                    <div id="text">
                        <script>
                            var id_task = <?php echo json_encode($task['id_task']); ?>;
                            getMessages(id_task)</script>
                    </div>
                </td>
            </tr></table>

            <!--  Enfin, nous affichons la barre contenant la zone de texte pour taper le message et le bouton : -->

            <!--Zone de texte ////////////////////////////////////////////////////////-->
            <a name="post"></a>
            <table class="post_message"><tr>
                <td>
                <form action="" method="" onsubmit="postMessage(id_task); return false;">
                    <input type="text" id="message" maxlength="255" />
                    <input type="button" onclick="postMessage(id_task)" value="Envoyer" id="post" />
                </form>
                        <div id="responsePost" style="display:none"></div>
                </td>
            </tr></table>
        </div>
    </body>
</html>