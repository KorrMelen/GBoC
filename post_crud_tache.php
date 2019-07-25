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
        if($_SESSION['role'] == 'BENEVOLE'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            if(isset($_POST['create'])){
                if($_POST['date_debut']>$_POST['date_fin'] || ($_POST['date_debut']==$_POST['date_fin'] && $_POST['heure_debut']>$_POST['heure_fin'])){
                    header('location: commission_taches.php?id_event='.$_POST['id_event'].'&id_comm='.$_POST['id_comm'].'&nom='.str_replace(' ', '+', $_POST['nom']).'&description='.str_replace(' ', '+', $_POST['description']).'&date_debut='.$_POST['date_debut'].'&heure_debut='.$_POST['heure_debut'].'&date_fin='.$_POST['date_fin'].'&heure_fin='.$_POST['heure_fin'].'&lieux='.str_replace(' ', '+', $_POST['lieux']).'&nbpersonne='.$_POST['nbpersonne'].'&error=date');
                }else{
                    $uuid=uuid();
                    if($_POST['lieux']=='') $_POST['lieux']='mission bretonne';
                    $evenement = $bdd->prepare('INSERT INTO taches VALUES(:id, :evenement, :commission, :nom, :description, :datedebut, :datefin, :lieux, :nbpersonne)');
                    $evenement->execute(array(
                        'id'=>$uuid,
                        'evenement' => $_POST['id_event'],
                        'commission' => $_POST['id_comm'],
                        'nom'=>$_POST['nom'],
                        'description'=>$_POST['description'],
                        'datedebut'=>$_POST['date_debut'].' '.$_POST['heure_debut'],
                        'datefin'=>$_POST['date_fin'].' '.$_POST['heure_fin'],
                        'lieux'=>$_POST['lieux'],
                        'nbpersonne'=>$_POST['nbpersonne']));
                    header('location: commission_taches.php?id_event='.$_POST['id_event'].'&id_comm='.$_POST['id_comm']);
                }
            }
            if (isset($_POST['engagement'])){
                $addbene = $bdd->query('UPDATE taches SET beneinscrit = array_append(beneinscrit,\''.$_POST['id_bene'].'\') WHERE id=\''.$_POST['id_tache'].'\'');
                header('location: benevole_taches.php?id_event='.$_POST['id_event']);
            }
        }
    }
?>