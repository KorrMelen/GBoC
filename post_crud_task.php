<?php 
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    if(isset($_POST['create'])){
        if(!commission_verified($_POST['id_commission'])){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            if($_POST['begin_date']>$_POST['end_date'] || ($_POST['begin_date']==$_POST['end_date'] && $_POST['begin_time']>$_POST['end_time'])){
                header('location: commission_taches.php?id_event='.$_POST['id_event'].'&id_commission='.$_POST['id_commission'].'&name='.str_replace(' ', '+', $_POST['name']).'&info='.str_replace(' ', '+', $_POST['info']).'&begin_date='.$_POST['begin_date'].'&begin_time='.$_POST['begin_time'].'&end_date='.$_POST['end_date'].'&end_time='.$_POST['end_time'].'&places='.str_replace(' ', '+', $_POST['places']).'&max_volunteers='.$_POST['max_volunteers'].'&error=date');
            }else{
                $uuid=uuid();
                if($_POST['places']=='') $_POST['places']='mission bretonne';
                $evenement = $db->prepare('INSERT INTO tasks VALUES(:id, :event, :commission, :name, :info, :begin_date, :end_date, :places, :max_volunteers,\'{}\')');
                $evenement->execute(array(
                    'id'=>$uuid,
                    'event' => $_POST['id_event'],
                    'commission' => $_POST['id_commission'],
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'max_volunteers'=>$_POST['max_volunteers']));
                header('location: commission_tasks.php?id_event='.$_POST['id_event'].'&id_commission='.$_POST['id_commission']);
            }
        }
    }
    if(isset($_POST['undertaking'])){
        echo "1";
        $add_volunteer = $db->query('UPDATE tasks SET registered_volunteers = array_append(registered_volunteers,\''.$_POST['id_volunteer'].'\') WHERE id_task=\''.$_POST['id_task'].'\'');
        header('location: volunteer_tasks.php?id_event='.$_POST['id_event']);
    }
?>