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
                </tr>
                <?php $events=$db->prepare('SELECT DISTINCT id_event, name_event, info_event, begin_time_event, end_time_event, places_event FROM events AS e, commissions AS c WHERE end_time_event > :today_date AND id_commission IN 
                    (SELECT id_commission FROM commissions WHERE :id_volunteer = ANY (volunteers)) AND id_commission = ANY (commissions)');
                $events->execute(array(
                    'today_date' => date("Y-m-d H:i"),
                    'id_volunteer' => $_SESSION['uuid']));
                while($data_event = $events->fetch()){ ?>
                    <tr>
                        <td><?php echo $data_event['name_event'] ?></td>
                        <td><?php echo $data_event['info_event'] ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_time_event'])) ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_time_event'])) ?></td>
                        <td><?php echo $data_event['places_event'] ?></td>
                        <td><form method="post" action=<?php echo '"volunteer_tasks.php?id_event='.$data_event['id_event'].'"' ?>>
                            <input type="submit" name="taches" value="Voir les tâches">
                        </form></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </body>
</html>
        