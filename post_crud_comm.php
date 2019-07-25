<?php session_start();
    function uuid(){
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] != 'ADMIN'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            if(isset($_POST['add_comm'])){
                $moderateur = explode(" ",$_POST['moderateur']);
                $moderateur = substr($moderateur[count($moderateur)-1],1,-1);
                $moderateur = $bdd->query('SELECT id FROM benevoles WHERE mail=\''.$moderateur.'\'');
                $moderateur = $moderateur->fetch();
                $edit_role = $bdd->query('UPDATE benevoles SET role=\'MODERATEUR\' WHERE id=\''.$moderateur['id'].'\'');
                $uuid = uuid();
                $addbenevoles = $bdd->prepare('INSERT INTO commissions VALUES(:id,:nom,:moderateur)');
                $addbenevoles->execute(array(
                    'id' => $uuid,
                    'nom' =>ucwords ($_POST['nom']," -'_/"),
                    'moderateur' =>'{'.$moderateur['id'].'}'
                ));
            }
            if(isset($_POST['add_modo'])){
                $moderateur = explode(" ",$_POST['moderateur']);
                $moderateur = substr($moderateur[count($moderateur)-1],1,-1);
                $moderateur = $bdd->query('SELECT id FROM benevoles WHERE mail=\''.$moderateur.'\'');
                $commission = $bdd->query('SELECT active FROM commissions WHERE id=\''.$_POST['id_comm'].'\'');
                $commission = $commission->fetch();
                $moderateur = $moderateur->fetch();
                if($commission['active']) $edit_role = $bdd->query('UPDATE benevoles SET role=\'MODERATEUR\' WHERE id=\''.$moderateur['id'].'\'');
                $addmodo = $bdd->query('UPDATE commissions SET moderateurs = array_append(moderateurs,\''.$moderateur['id'] .'\') WHERE id=\''.$_POST['id_comm'].'\'');
            }
            if(isset($_POST['desactive_comm'])){
                $moderateurs = $bdd->query('UPDATE benevoles SET role = \'BENEVOLE\' WHERE id IN (SELECT id FROM benevoles WHERE role=\'MODERATEUR\' EXCEPT SELECT b.id FROM benevoles AS b, commissions AS c WHERE c.id !=\''.$_POST['id_comm'].'\' AND b.id = ANY (moderateurs))');
                $desactive_comm = $bdd->query('UPDATE commissions SET listbenevoles = NULL, benevolesattente = NULL, active = FALSE WHERE id =\''.$_POST['id_comm'].'\'');
            }
            if(isset($_POST['reactive_comm'])){
                $moderateurs = $bdd->query('UPDATE benevoles SET role=\'MODERATEUR\' WHERE id IN (SELECT b.id FROM benevoles AS b, commissions AS C WHERE c.id =\''.$_POST['id_comm'].'\' AND b.id = ANY (moderateurs))');
                $reactive_comm = $bdd->query('UPDATE commissions SET active = TRUE WHERE id =\''.$_POST['id_comm'].'\'');
            }
            if(isset($_POST['remove_modo'])){
                print_r($_POST['id_modo']);
                $removemodo = $bdd->query('UPDATE commissions SET moderateurs = array_remove(moderateurs,\''.$_POST['id_modo'] .'\') WHERE id=\''.$_POST['id_comm'].'\'');
                $edit_role = $bdd->query('UPDATE benevoles SET role=\'BENEVOLE\' WHERE id IN (SELECT id FROM benevoles WHERE role=\'MODERATEUR\' and id=\''.$_POST['id_modo'].'\' EXCEPT SELECT b.id FROM benevoles AS b, commissions AS c WHERE c.id !=\''.$_POST['id_comm'].'\' AND b.id = ANY (moderateurs))');
            }
            header('location:'. $_SERVER['HTTP_REFERER']);
        }
    }
?>