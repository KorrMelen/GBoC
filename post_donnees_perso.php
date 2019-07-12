<?php session_start();
    try{
        $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
    }catch (Exception $e){
        die('Erreur : ' . $e->getMessage());
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
                    $req = $bdd->prepare('UPDATE benevoles SET nom=:nom, prenom=:prenom, numerotel=:numeroTel, mail=:mail WHERE id=:id');
                    $req->execute(array(
                        'id' => $_SESSION['uuid'],
                        'nom' => $_POST['nom'],
                        'prenom' => $_POST['prenom'],
                        'numeroTel' => $_POST['tel'],
                        'mail' => $_POST['mail']
                    ));
                    header('location: donnees_perso.php?statut=reussi');
                }
                $mailexist->closeCursor();
            }else{
                $req = $bdd->prepare('UPDATE benevoles SET nom=:nom, prenom=:prenom, numerotel=:numeroTel, mail=:mail WHERE id=:id');
                $req->execute(array(
                    'id' => $_SESSION['uuid'],
                    'nom' => $_POST['nom'],
                    'prenom' => $_POST['prenom'],
                    'numeroTel' => $_POST['tel'],
                    'mail' => $_POST['mail']
                ));
                header('location: donnees_perso.php?statut=reussi');
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
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <header> 
        </header>
        <footer id="pied_de_page">
        </footer>
    </body>
</html>