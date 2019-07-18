<?php session_start();
    include("connection_bdd.php");

    function modif($bdd){
        $req = $bdd->prepare('UPDATE benevoles SET nom=:nom, prenom=:prenom, numerotel=:numeroTel, mail=:mail WHERE id=:id');
        $req->execute(array(
            'id' => $_SESSION['uuid'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'numeroTel' => $_POST['tel'],
            'mail' => $_POST['mail']
        ));
        //$commissions = $bdd->query('SELECT nom FROM commissions WHERE \''.$_SESSION['uuid'].'\' = ANY (listbenevoles) OR \''.$_SESSION['uuid'].'\' = ANY (benevoles_attente)');
        $allcommissions = $bdd->query('SELECT * FROM commissions');
        $addcom = $bdd->prepare('UPDATE commissions SET benevolesattente = array_append(benevolesattente, :uuid) WHERE id=:id');
        $removecom = $bdd->prepare('UPDATE commissions SET benevolesattente = array_remove(benevolesattente, :uuid), listbenevoles = array_remove(listbenevoles, :uuid) WHERE id=:id');
        while($comm = $allcommissions->fetch()){
            if(isset($_POST[$comm['nom']])){
                if(!in_array($_SESSION['uuid'], explode(",",substr($comm['listbenevoles'],1,-1))) && !in_array($_SESSION['uuid'], explode(",",substr($comm['benevolesattente'],1,-1)))){
                    $addcom->execute(array(
                        'uuid' =>$_SESSION['uuid'],
                        'id' => $comm['id']
                    ));
                }
            }else{
                if(in_array($_SESSION['uuid'], explode(",",substr($comm['listbenevoles'],1,-1))) || in_array($_SESSION['uuid'], explode(",",substr($comm['benevolesattente'],1,-1)))){
                    $removecom->execute(array(
                        'uuid' =>$_SESSION['uuid'],
                        'id' => $comm['id']
                    ));
                }
            }
        }
        header('location: donnees_perso.php?statut=reussi');
    }

    $reponse = $bdd->query('SELECT * FROM benevoles WHERE id=\''.$_SESSION['uuid'].'\'');
    if($reponse->rowCount() == 0){
        header('location: accueil.php');
    }else{
        $donne = $reponse->fetch();
        if(isset($_POST['nom'])){
            if($_POST['mail'] != $donne['mail']){
                $mailexist = $bdd->prepare('SELECT * FROM benevoles WHERE mail=:mail');
                $mailexist->execute(array('mail'=>$_POST['mail']));
                if($mailexist->rowCount() > 0){
                    if (isset($_POST['tel'])) {
                        header('location: donnees_perso.php?nom='.str_replace(' ','+',$_POST['nom']).'&prenom='.$_POST['prenom'].'&tel='.str_replace(' ','+',$_POST['tel']).'&ndate='.$_POST['ndate'].'&error=mailexist');
                    }else{
                        header('location: donnees_perso.php?nom='.str_replace(' ','+',$_POST['nom']).'&prenom='.$_POST['prenom'].'&ndate='.$_POST['ndate'].'&error=mailexsit');
                    }
                }else{
                   modif($bdd);
                }
                $mailexist->closeCursor();
            }else{
                modif($bdd);
            }
        }else{
            if($_POST['newmp'] != $_POST['newmp2']){
                header('location: donnees_perso.php?&error=samepassword');
            }else{
                if(!password_verify($_POST['oldmp'],$donne['password'])){
                    header('location: donnees_perso.php?&error=password');
                }else{
                    $_POST['newmp'] = password_hash($_POST['newmp'], PASSWORD_BCRYPT);
                    $req = $bdd->prepare('UPDATE benevoles SET password=:password WHERE id=:id');
                    $req->execute(array(
                        'id' => $_SESSION['uuid'],
                        'password' => $_POST['newmp']
                    ));
                    header('location: donnees_perso.php?statut=reussi');
                }
            }
        }
    }
    $reponse->closeCursor();
?>