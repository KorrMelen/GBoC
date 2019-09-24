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
        $waiting = $db->query('SELECT volunteers_waiting, name_commission FROM commissions WHERE id_commission=\''.$_POST['id_commission'].'\'');
        $waiting = $waiting->fetch();
        if(isset($_POST['waiting'])){
            if(!in_array($_POST['id_volunteer'], explode(",",substr($waiting['volunteers_waiting'],1,-1)))){
                header('location: commission_volunteers.php?id='.$_POST['id_commission'].'&error=nowait');
            }else{
                $volunteer = $db->query('SELECT mail FROM volunteers WHERE id_volunteer = \''.$_POST['id_volunteer'].'\'');
                $data_volunteer = $volunteer->fetch();
                if($_POST['waiting'] == "Accepter"){
                    mail_accept_volunteer($data_volunteer['mail'], $waiting['name_commission']);
                    $add_volunteer = $db->query('UPDATE commissions SET volunteers = array_append(volunteers,\''.$_POST['id_volunteer'].'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
                }else{
                    mail_reject_volunteer($data_volunteer['mail'], $waiting['name_commission']);
                }
                $remove_volunteer = $db->query('UPDATE commissions SET volunteers_waiting = array_remove(volunteers_waiting,\''.$_POST['id_volunteer'].'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
                header('location: commission_volunteers.php?id='.$_POST['id_commission']);
            }
        }
        if(isset($_POST['goodbye'])){
            $volunteer = $db->query('SELECT mail FROM volunteers WHERE id_volunteer = \''.$_POST['id_volunteer'].'\'');
            $data_volunteer = $volunteer->fetch();
            $remove_volunteer = $db->query('UPDATE commissions SET volunteers = array_remove(volunteers,\''.$_POST['id_volunteer'].'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
            $remove_task = $db->query('UPDATE tasks SET registered_volunteers = array_append(registered_volunteers, \''.$_POST['id_volunteer'].'\') WHERE comission = \''.$_POST['id_commission'].'\'');
            mail_volunter_dismiss($data_volunteer['mail'], $waiting['name_commission']);
            header('location: commission_volunteers.php?id='.$_POST['id_commission']);
        }
    }
?>