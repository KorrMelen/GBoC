<?php
	try{
    	$bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
	}catch (Exception $e){
        die('Erreur : ' . $e->getMessage());
	}
    $check = $bdd->prepare('SELECT role FROM benevoles WHERE id=:id');
    $check->execute(array('id'=> $_SESSION['uuid']));
    $check = $check->fetch();
?>

<nav id="menu">        
    <div class="element_menu">
        <h3>Titre menu</h3>
        <ul>
            <li><a href="evenement_benevole.php">Mes événements à venir</a></li>
            <?php if($check['role'] == 'ADMIN'){
            	echo "<li><a href='liste_all_benevoles.php'>Liste des bénévoles</a></li>
            	<li><a href='liste_commissions.php'>Liste des commissions</a></li>";
        	}?>
            <li><a href="donnees_perso.php">mes informations</a></li>
            <li><a href="deco.php">Déconnexion</a></li>
        </ul>
    </div>    
</nav>