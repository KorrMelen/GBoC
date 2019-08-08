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

        if($_POST['begin_date']>$_POST['end_date'] || ($_POST['begin_date']==$_POST['end_date'] && $_POST['begin_time']>$_POST['end_time'])){
            header('location: liste_evenements.php?name='.str_replace(' ', '+', $_POST['name']).'&info='.str_replace(' ', '+', $_POST['info']).'&begin_date='.$_POST['begin_date'].'&begin_time='.$_POST['begin_time'].'&end_date='.$_POST['end_date'].'&end_time='.$_POST['end_time'].'&places='.str_replace(' ', '+', $_POST['places']).'&expected='.$_POST['expected'].'&error=date');
        }else{
            $commissions = $db->query('SELECT * FROM commissions WHERE active');
            $commissions_event='';
            while($data_commission = $commissions->fetch()){
                if(isset($_POST[$data_commission['name_commission']])){
                    $commissions_event.=','.$_POST[$data_commission['name_commission']];
                }
            }
            $commissions_event='{'.substr($commissions_event, 1).'}';
            if($_POST['places']=='') $_POST['places']='mission bretonne';
            if($_POST['expected']=='') $_POST['expected']=10;

            if(isset($_POST['create_event'])){
                $uuid=uuid();
                $event = $db->prepare('INSERT INTO events VALUES(:id, :name, :info, :begin_date, :end_date, :places, :expected, :commissions)');
                $event->execute(array(
                    'id'=>$uuid,
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'expected'=>$_POST['expected'],
                    'commissions'=>$commissions_event));
                header('location: list_events.php');
            }

            if(isset($_POST['update_event'])){
                $event = $db->prepare('UPDATE events SET name_event = :name, info_event = :info, begin_time_event = :begin_date, end_time_event = :end_date, places_event = :places, expected_people = :expected, commissions = :commissions WHERE id_event = :id');
                $event->execute(array(
                    'id'=>$_POST['id'],
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'expected'=>$_POST['expected'],
                    'commissions'=>$commissions_event));
                header('location: event_tasks.php?id='.$_POST['id']);
            }
        }
    }
?>