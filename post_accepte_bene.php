<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        include("connection_bdd.php");
        if($_SESSION['role'] == 'BENEVOLE'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $en_attente = $bdd->query('SELECT benevolesattente FROM commissions WHERE id=\''.$_POST['id_comm'].'\'');
            $en_attente = $en_attente->fetch();
            if(isset($_POST['attente'])){
                if(!in_array($_POST['id_bene'], explode(",",substr($en_attente['benevolesattente'],1,-1)))){
                    header('location: commission_benevoles.php?id='.$_POST['id_comm'].'&error=nowait');
                }else{
                    if($_POST['attente'] == "Accepter"){
                        $addcom = $bdd->query('UPDATE commissions SET listbenevoles = array_append(listbenevoles,\''.$_POST['id_bene'].'\') WHERE id=\''.$_POST['id_comm'].'\'');
                    }
                    $removecom = $bdd->query('UPDATE commissions SET benevolesattente = array_remove(benevolesattente,\''.$_POST['id_bene'].'\') WHERE id=\''.$_POST['id_comm'].'\'');
                    header('location: commission_benevoles.php?id='.$_POST['id_comm']);
                }
            }else{
                $removecom = $bdd->query('UPDATE commissions SET listbenevoles = array_remove(listbenevoles,\''.$_POST['id_bene'].'\') WHERE id=\''.$_POST['id_comm'].'\'');
                header('location: commission_benevoles.php?id='.$_POST['id_comm']);
            }
        }
    }
?>