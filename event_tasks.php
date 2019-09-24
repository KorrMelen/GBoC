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
                <title>GBoC - Liste des taches</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    <?php
                    $event = $db->query('SELECT * FROM events WHERE id_event = \''.$_GET['id'].'\'');
                    $event = $event->fetch();
                    $tasks = $db->query('SELECT * FROM tasks WHERE event = \''.$event['id_event'].'\'');
                    if($event['end_time_event'] >= date("Y-m-d H:i")){
                    ?>
	                    <form method="post" action="post_crud_event.php" id="create_event">
	                        Nom de l'événemnt:<br>
	                        <input type="text" name="name" required="" <?php echo 'value="'.$event['name_event'].'"' ?> ><br>
	                        Description de l'événement:<br>
	                        <textarea rows="4" cols="50" name="info" form="create_event"><?php echo $event['info_event']?></textarea><br>
	                        <table>
	                        	<tr>
	                        		<td>Date et heure de début:</td>
	                        		<td>Date et heure de fin:</td>
	                        	</tr>
	                        	<tr>
	                        		<td><input type="date" name="begin_date" required=""<?php echo 'value="'.date('Y-m-d', strtotime($event['begin_time_event'])).'"' ?>>
	                        			<input type="time" name="begin_time" required=""<?php echo 'value="'.date('H:i', strtotime($event['begin_time_event'])).'"' ?>></td>
	                        		<td><input type="date" name="end_date" required=""<?php echo 'value="'.date('Y-m-d', strtotime($event['end_time_event'])).'"' ?>>
	                        			<input type="time" name="end_time" required=""<?php echo 'value="'.date('H:i', strtotime($event['end_time_event'])).'"' ?>></td>
	                        	</tr>
	                        </table>
	                        Lieu(x) de l'événement (à la mission bretonne par défaut):<br>
	                        <input type="text" name="places"<?php echo 'value="'.$event['places_event'].'"' ?> ><br>
	                        Nombre de personne attendu:<br>
	                        <input type="number" name="expected"<?php echo 'value="'.$event['expected_people'].'"' ?>><br>
	                        Commissions participantes:<br>
	                        <?php
	                            $commissions = $db->query('SELECT * FROM commissions WHERE active');
	                            while($data_commission = $commissions->fetch()){
	                                echo '<input type="checkbox" name ="'.$data_commission['name_commission'].'" value="'.$data_commission['id_commission'].'"';
	                                if(in_array($data_commission['id_commission'], explode(",",substr($event['commissions'],1,-1)))) echo "checked=\"\"";
	                                echo '>'.$data_commission['name_commission'];
	                            }
	                        ?>
	                        <br>
	                        <input type="hidden" name="id" value= <?php echo '"'.$event['id_event'].'"'?>>
	                        <input type="submit" name="update_event" value="Modifier l'événement">
	                    </form>
	                <?php }else{ ?>
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
	                        <?php $event_commissions = str_replace('{', '(\'', $event['commissions']);
	                        $event_commissions = str_replace(',', '\',\'', $event_commissions);
	                        $event_commissions = str_replace('}', '\')', $event_commissions);
	                        $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$event_commissions);?>
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
	                <?php } ?>

                    <h3>Liste des taches</h3>
                    <?php $event['commissions'] = str_replace('{', '(\'', $event['commissions']);
		            $event['commissions'] = str_replace(',', '\',\'', $event['commissions']);
		            $event['commissions'] = str_replace('}', '\')', $event['commissions']);
		            $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$event['commissions']);
		            while($data_commmission = $commissions->fetch()){
		                $tasks = $db->query('SELECT id_task, name_task, info_task, begin_time_task, end_time_task, places_task, max_volunteers, array_length(registered_volunteers,1) AS volunteers FROM tasks WHERE event = \''.$event['id_event'].'\' AND commission = \''.$data_commmission['id_commission'].'\'')?>
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
		                            <td><form method="post" action=<?php echo "task.php?id_task=".$data_task['id_task'] ?>>
		                                <input type="submit" value="Voir la tache">
		                            </form></td>
		                        </tr>
		                    <?php } ?>
		                </table>
		            <?php } ?>
                </div>
            </body>
        </html>
        <?php
    }
?>