<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
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
                    <form method="post" action="post_crud_event.php" id="create_event">
                        Nom de l'événemnt:<br>
                        <input type="text" name="name" required=""<?php if(isset($_GET['name'])) echo 'value="'.str_replace('+',' ',$_GET['name']).'"' ?> ><br>
                        Description de l'événement:<br>
                        <textarea rows="4" cols="50" name="info" form="create_event"><?php if(isset($_GET['info'])) echo str_replace('+',' ',$_GET['info'])?></textarea><br>
                        Date et heure de début:<br>
                        <input type="date" name="begin_date" required=""<?php if(isset($_GET['begin_date'])) echo 'value="'.$_GET['begin_date'].'"' ?>>
                        <input type="time" name="begin_time" required=""<?php if(isset($_GET['begin_time'])) echo 'value="'.$_GET['begin_time'].'"' ?>><br>
                        Date et heure de fin:<br>
                        <input type="date" name="end_date" required=""<?php if(isset($_GET['end_date'])) echo 'value="'.$_GET['end_date'].'"' ?>>
                        <input type="time" name="end_time" required=""<?php if(isset($_GET['end_time'])) echo 'value="'.$_GET['end_time'].'"' ?>><br>
                        Lieux de l'événement (à la mission bretonne par défaut):<br>
                        <input type="text" name="places"<?php if(isset($_GET['places'])) echo 'value="'.str_replace('+',' ',$_GET['places']).'"' ?> ><br>
                        Nombre de personne attendu:<br>
                        <input type="number" name="expected"<?php if(isset($_GET['expected'])) echo 'value="'.$_GET['expected'].'"' ?>><br>
                        Commissions participantes:<br>
                        <?php
                            $commissions = $db->query('SELECT * FROM commissions WHERE active');
                            while($data_commission = $commissions->fetch()){
                                echo '<input type="checkbox" name ="'.$data_commission['name_commission'].'" value="'.$data_commission['id_commission'].'">'.$data_commission['name_commission'].'<br>';
                            }
                        ?>
                        <input type="submit" name="create_event" value="Créer l'événement">
                    </form>
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'date'){
                        echo "Attention, l'événement se termine avant qu'il ne commence";
                    }?>

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
                        <?php $events=$db->query('SELECT * FROM events WHERE end_time_event >\''.date("Y-m-d H:i").'\'');
                        while($data_event = $events->fetch()){
                            $data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                            $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);?>
                            <tr>
                                <td><?php echo $data_event['name_event']?></td>
                                <td><?php echo $data_event['info_event']?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_time_event']))?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_time_event']))?></td>
                                <td><?php echo $data_event['places_event']?></td>
                                <td><?php echo $data_event['expected_people']?></td>
                                <td>
                                    <?php 
                                    $data_commission = $commissions->fetch();
                                    echo $data_commission['name_commission'];
                                    while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                                </td>
                                <td><form method="post" action=<?php echo '"event_tasks.php?id='.$data_event['id_event'].'"';?>>
                                    <input type="submit" value="Voire les tâches">
                                </form></td>
                            </tr>
                        <?php } ?>
                    </table>

                    <h3>Evénements passé</h3>
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
                        <?php $events=$db->query('SELECT * FROM events WHERE end_time_event <=\''.date("Y-m-d H:i").'\'');
                        while($data_event = $events->fetch()){
                            $data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                            $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);?>
                            <tr>
                                <td><?php echo $data_event['name_event']?></td>
                                <td><?php echo $data_event['info_event']?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_time_event']))?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_time_event']))?></td>
                                <td><?php echo $data_event['places_event']?></td>
                                <td><?php echo $data_event['expected_people']?></td>
                                <td>
                                    <?php
                                    $data_commission = $commissions->fetch();
                                    echo $data_commission['name_commission'];
                                    while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission'];?>
                                </td>
                                <td><form method="post" action=<?php echo '"event_tasks.php?id='.$data_event['id_event'].'"';?>>
                                    <input type="submit" value="Voire les tâches">
                                </form></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </body>
        </html>
        <?php
    }
?>