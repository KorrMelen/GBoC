<?php session_start();
    if(isset($_SESSION['uuid'])){
        try{
            $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
        }catch (Exception $e){
            die('Erreur : ' . $e->getMessage());
        }
        $desincription = $bdd->query('DELETE FROM messages WHERE emissaire =\''.$_SESSION['uuid'].'\'');
        $desincription = $bdd->query('UPDATE taches SET beneattente = array_remove(beneattente, \''.$_SESSION['uuid'].'\'), beneinscrit = array_remove(beneinscrit, \''.$_SESSION['uuid'].'\')');
        $desincription = $bdd->query('UPDATE commissions SET benevolesattente = array_remove(benevolesattente, \''.$_SESSION['uuid'].'\'), listbenevoles = array_remove(listbenevoles, \''.$_SESSION['uuid'].'\'), moderateurs = array_remove(moderateurs, \''.$_SESSION['uuid'].'\')');
        $desincription = $bdd->query('DELETE FROM benevoles WHERE id =\''.$_SESSION['uuid'].'\'');
    }
	header('location: accueil.php');
?>