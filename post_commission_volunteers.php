<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    if(!commission_verified($_POST['id_commission'])){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else{
        $waiting = $db->query('SELECT volunteers_waiting FROM commissions WHERE id_commission=\''.$_POST['id_commission'].'\'');
        $waiting = $waiting->fetch();
        if(isset($_POST['waiting'])){
            if(!in_array($_POST['id_volunteer'], explode(",",substr($waiting['volunteers_waiting'],1,-1)))){
                header('location: commission_volunteers.php?id='.$_POST['id_commission'].'&error=nowait');
            }else{
                if($_POST['waiting'] == "Accepter"){
                    $add_volunteer = $db->query('UPDATE commissions SET volunteers = array_append(volunteers,\''.$_POST['id_volunteer'].'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
                }
                $remove_volunteer = $db->query('UPDATE commissions SET volunteers_waiting = array_remove(volunteers_waiting,\''.$_POST['id_volunteer'].'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
                header('location: commission_volunteers.php?id='.$_POST['id_commission']);
            }
        }
        if(isset($_POST['goodbye'])){
            $remove_volunteer = $db->query('UPDATE commissions SET volunteers = array_remove(volunteers,\''.$_POST['id_volunteer'].'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
            header('location: commission_volunteers.php?id='.$_POST['id_commission']);
        }
    }
?>