<?php session_start();
    try{
        $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
    }catch (Exception $e){
        die('Erreur : ' . $e->getMessage());
    }
    $reponse = $bdd->prepare('SELECT * FROM benevoles WHERE mail=:mail');
    $reponse->execute(array('mail'=>$_POST['mail']));
    if($reponse->rowCount() == 0){
        header('location: accueil.php?error=mailnotexist');
    }else{
        $donne = $reponse->fetch();
        if(!password_verify($_POST['password'],$donne['password'])){
            header('location: accueil.php?mail='.$_POST['mail'].'&error=password');
        }else{
            $_SESSION['uuid'] = $donne['id'];
            header('location: evenement_benevole.php');
            setcookie('mail', '', time(), null, null, false, true);
            setcookie('password', '', time(), null, null, false, true);
            setcookie('save', '', time(), null, null, false, true);
            if(isset($_POST['save'])){
                setcookie('mail', $_POST['mail'], time() + 365*24*3600, null, null, false, true);
                setcookie('password', $_POST['password'], time() + 365*24*3600, null, null, false, true);
                setcookie('save', $_POST['save'], time() + 365*24*3600, null, null, false, true);
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