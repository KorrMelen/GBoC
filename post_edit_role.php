<?php session_start();
    if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        try{
            $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
        }catch (Exception $e){
            die('Erreur : ' . $e->getMessage());
        }
        if($_SESSION['role'] != 'ADMIN'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $reponse = $bdd->prepare('UPDATE benevoles SET role=:role WHERE id=:id');
            $reponse->execute(array('role'=>$_POST['role'],
                'id'=>$_POST['id']));
            header('location: liste_all_benevoles.php');
        }
    }
?>