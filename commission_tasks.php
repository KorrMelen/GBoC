<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    if(!commission_verified($_GET['id_commission'])){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else{
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
                            <td>Lieux</td>
                            <td>Nombre de personne attendu</td>
                            <td>Commissions participantes</td>
                        </tr>
                        <?php $event['commissions'] = str_replace('{', '(\'', $event['commissions']);
                        $event['commissions'] = str_replace(',', '\',\'', $event['commissions']);
                        $event['commissions'] = str_replace('}', '\')', $event['commissions']);
                        $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$event['commissions']);?>
                        <tr>
                            <td><?php echo $event['name_event']?></td>
                            <td><?php echo $event['info_event']?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($event['begin_time_event']))?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($event['end_time_event']))?></td>
                            <td><?php echo $event['places_event']?></td>
                            <td><?php echo $event['expected_people']?></td>
                            <td>
                            <?php $data_commission = $commissions->fetch();
                                echo $data_commission['name_commission'];
                                while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                            </td>
                        </tr>
                    </table>
                        
                    <h3>Tâches créées</h3>
                    <table>
                        <tr>
                            <td>Nom</td>
                            <td>Description</td>
                            <td>Date et heure de début</td>
                            <td>Date et heure de fin</td>
                            <td>Lieux</td>
                            <td>Nombre de bénévoles max</td>
                            <td>Nombre de bénévole inscrits</td>
                        </tr>
                        <?php
                        $tasks = $db->query('SELECT name_task, info_task, begin_time_task, end_time_task, places_task, max_volunteers, array_length(registered_volunteers,1) AS nb_volunteer FROM tasks WHERE event = \''.$_GET['id_event'].'\' AND commission = \''.$_GET['id_commission'].'\'');
                        while($data_task=$tasks->fetch()){
                            if($data_task['nb_volunteer'] == NULL) $data_task['nb_volunteer'] = 0?>
                            <tr>
                                <td><?php echo $data_task['name_task']?></td>
                                <td><?php echo $data_task['info_task']?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_task['begin_time_task']))?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_task['end_time_task']))?></td>
                                <td><?php echo $data_task['places_task']?></td>
                                <td><?php echo $data_task['max_volunteers']?></td>
                                <td><?php echo $data_task['nb_volunteer']?></td>
                            </tr>
                        <?php } ?>
                    </table>

                    <h3>Créer une nouvelle tâche pour l'événement</h3>
                    <form method="post" action="post_crud_task.php" id="create_task">
                        Nom de la tâche:<br>
                        <input type="text" name="name" required=""<?php if(isset($_GET['name'])) echo 'value="'.str_replace('+',' ',$_GET['name']).'"' ?> ><br>
                        Description de la tâche:<br>
                        <textarea rows="4" cols="50" name="info" form="create_task"><?php if(isset($_GET['info'])) echo str_replace('+',' ',$_GET['info'])?></textarea><br>
                        Date et heure de début:<br>
                        <input type="date" name="begin_date" required=""<?php if(isset($_GET['begin_date'])) echo 'value="'.$_GET['begin_date'].'"' ?>>
                        <input type="time" name="begin_time" required=""<?php if(isset($_GET['begin_time'])) echo 'value="'.$_GET['begin_time'].'"' ?>><br>
                        Date et heure de fin:<br>
                        <input type="date" name="end_date" required=""<?php if(isset($_GET['end_date'])) echo 'value="'.$_GET['end_date'].'"' ?>>
                        <input type="time" name="end_time" required=""<?php if(isset($_GET['end_time'])) echo 'value="'.$_GET['end_time'].'"' ?>><br>
                        Lieux de la tache (à la mission bretonne par défaut):<br>
                        <input type="text" name="places"<?php if(isset($_GET['places'])) echo 'value="'.str_replace('+',' ',$_GET['places']).'"' ?> ><br>
                        Nombre de bénévole:<br>
                        <input type="number" name="max_volunteers" required=""<?php if(isset($_GET['max_volunteers'])) echo 'value="'.$_GET['max_volunteers'].'"' ?>><br>
                        <input type="hidden" name="id_commission" value=<?php echo '"'.$_GET['id_commission'].'"'?>>
                        <input type="hidden" name="id_event" value=<?php echo '"'.$event['id_event'].'"'?>>
                        <input type="submit" name="create" value="Créer la tache">
                    </form>
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'date'){
                        echo "Attention, la atche se termine avant qu'elle ne commence";
                    }?>
                </div>
            </body>
        </html>
        <?php
    }
?>