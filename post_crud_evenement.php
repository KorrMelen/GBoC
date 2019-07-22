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
            if($_POST['date_debut']>$_POST['date_fin'] || ($_POST['date_debut']==$_POST['date_fin'] && $_POST['heure_debut']>$_POST['heure_fin'])){
                header('location: liste_evenements.php?nom='.str_replace(' ', '+', $_POST['nom']).'&description='.str_replace(' ', '+', $_POST['description']).'&date_debut='.$_POST['date_debut'].'&heure_debut='.$_POST['heure_debut'].'&date_fin='.$_POST['date_fin'].'&heure_fin='.$_POST['heure_fin'].'&lieux='.str_replace(' ', '+', $_POST['lieux']).'&nbpersonne='.$_POST['nbpersonne'].'&error=date');
            }else{
                $uuid=uuid();
                $commissions = $bdd->query('SELECT * FROM commissions WHERE active');
                $comms='';
                while($donnees_comm = $commissions->fetch()){
                    if(isset($_POST[$donnees_comm['nom']])){
                        $comms.=','.$_POST[$donnees_comm['nom']];
                    }
                }
                $comms='{'.substr($comms, 1).'}';
                if($_POST['lieux']=='') $_POST['lieux']='mission bretonne';
                if($_POST['nbpersonne']=='') $_POST['nbpersonne']=10;
                $evenement = $bdd->prepare('INSERT INTO evenements VALUES(:id, :nom, :description, :datedebut, :datefin, :lieux, :nbpersonne, :comms)');
                $evenement->execute(array(
                    'id'=>$uuid,
                    'nom'=>$_POST['nom'],
                    'description'=>$_POST['description'],
                    'datedebut'=>$_POST['date_debut'].' '.$_POST['heure_debut'],
                    'datefin'=>$_POST['date_fin'].' '.$_POST['heure_fin'],
                    'lieux'=>$_POST['lieux'],
                    'nbpersonne'=>$_POST['nbpersonne'],
                    'comms'=>$comms));
                header('location: liste_evenements.php');
            }
        }
    }
?>