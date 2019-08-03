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
            $uuid=uuid();
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
            $event = $db->prepare('INSERT INTO events VALUES(:id, :name, :info, :begin_date, :end_date, :places, :expected, :commissions_event)');
            $event->execute(array(
                'id'=>$uuid,
                'name'=>$_POST['name'],
                'info'=>$_POST['info'],
                'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                'places'=>$_POST['places'],
                'expected'=>$_POST['expected'],
                'commissions_event'=>$commissions_event));
            header('location: list_events.php');
        }
    }
?>