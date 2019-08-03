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

        if(isset($_POST['add_commission'])){
            $moderator = explode(" ",$_POST['moderator']);
            $moderator = substr($moderator[count($moderator)-1],1,-1);
            $moderator = $db->query('SELECT id_volunteer FROM volunteers WHERE mail=\''.$moderator.'\'');
            $moderator = $moderator->fetch();
            $edit_role = $db->query('UPDATE volunteers SET role=\'MODERATOR\' WHERE role = \'VOLUNTEER\' AND id_volunteer=\''.$moderator['id_volunteer'].'\'');
            $uuid = uuid();
            $add_commission = $db->prepare('INSERT INTO commissions VALUES(:id_commission,:name_commission,:moderator)');
            $add_commission->execute(array(
                'id_commission' => $uuid,
                'name_commission' =>ucwords ($_POST['name']," -'_/"),
                'moderator' =>'{'.$moderator['id_volunteer'].'}'
            ));
        }

        if(isset($_POST['add_moderator'])){
            $moderator = explode(" ",$_POST['moderator']);
            $moderator = substr($moderator[count($moderator)-1],1,-1);
            $moderator = $db->query('SELECT id_volunteer FROM volunteers WHERE mail=\''.$moderator.'\'');
            $commission = $db->query('SELECT active FROM commissions WHERE id_commission=\''.$_POST['id_commission'].'\'');
            $commission = $commission->fetch();
            $moderator = $moderator->fetch();
            if($commission['active']) $edit_role = $db->query('UPDATE volunteers SET role=\'MODERATOR\' WHERE role = \'VOLUNTEER\' AND id_volunteer=\''.$moderator['id_volunteer'].'\'');
            $add_moderator = $db->query('UPDATE commissions SET moderators = array_append(moderators,\''.$moderator['id_volunteer'] .'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
        }

        if(isset($_POST['disable_commission'])){
            $moderators = $db->query('UPDATE volunteers SET role = \'VOLUNTEER\' WHERE id_volunteer IN (SELECT id_volunteer FROM volunteers WHERE role=\'MODERATOR\' EXCEPT SELECT id_volunteer FROM volunteers, commissions WHERE id_commission !=\''.$_POST['id_commission'].'\' AND id_volunteer = ANY (moderators))');
            $disable_commission = $db->query('UPDATE commissions SET volunteers = NULL, volunteers_waiting = NULL, active = FALSE WHERE id_commission =\''.$_POST['id_commission'].'\'');
        }

        if(isset($_POST['reactivate_commission'])){
            $moderators = $db->query('UPDATE volunteers SET role=\'MODERATOR\' WHERE role = \'VOLUNTEER\' AND id IN (SELECT id_volunteer FROM volunteers, commissions WHERE id_commission =\''.$_POST['id_commission'].'\' AND id_volunteer = ANY (moderators))');
            $reactivate_commission = $db->query('UPDATE commissions SET active = TRUE WHERE id_commission =\''.$_POST['id_commission'].'\'');
        }

        if(isset($_POST['remove_moderator'])){
            $remove_moderator = $db->query('UPDATE commissions SET moderators = array_remove(moderators,\''.$_POST['id_moderator'] .'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
            $edit_role = $db->query('UPDATE volunteers SET role=\'VOLUNTEER\' WHERE role = \'MODERATOR\' AND id_volunteer IN (SELECT id_volunteer FROM volunteers WHERE role=\'MODERATOR\' and id_volunteer=\''.$_POST['id_moderator'].'\' EXCEPT SELECT id_volunteer FROM volunteers, commissions WHERE id_commission !=\''.$_POST['id_commission'].'\' AND id_volunteer = ANY (moderators))');
        }
        header('location:'. $_SERVER['HTTP_REFERER']);
    }
?>