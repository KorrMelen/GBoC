<?php session_start();
    if(isset($_SESSION['uuid'])){
        include("connection_bdd.php");
        $desincription = $bdd->query('DELETE FROM messages WHERE emissaire =\''.$_SESSION['uuid'].'\'');
        $desincription = $bdd->query('UPDATE taches SET beneattente = array_remove(beneattente, \''.$_SESSION['uuid'].'\'), beneinscrit = array_remove(beneinscrit, \''.$_SESSION['uuid'].'\')');
        $desincription = $bdd->query('UPDATE commissions SET benevolesattente = array_remove(benevolesattente, \''.$_SESSION['uuid'].'\'), listbenevoles = array_remove(listbenevoles, \''.$_SESSION['uuid'].'\'), moderateurs = array_remove(moderateurs, \''.$_SESSION['uuid'].'\')');
        $desincription = $bdd->query('DELETE FROM benevoles WHERE id =\''.$_SESSION['uuid'].'\'');
    }
	header('location: accueil.php');
?>