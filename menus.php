<?php
	include("connection_bdd.php");
	if($_SESSION['role'] != "BENEVOLES"){
		$commissions = $bdd->query('SELECT id, nom, moderateurs FROM commissions WHERE active');
	}
?>

<nav id="menu">        
    <div class="element_menu">
        <h3>Menu</h3>
        <ul>
        	<li><a href="donnees_perso.php">Mes informations</a></li>
            <li><a href="evenement_benevole.php">Mes événements à venir</a></li>
            <?php if($_SESSION['role']!="BENEVOLE"){
            	echo "<li> Commissions</li>";
            	echo "<ul>";
            	while($donnees_comms = $commissions->fetch()){
            		if($_SESSION['role'] == "ADMIN" || in_array($_SESSION['uuid'], explode(",",substr($donnees_comms['moderateurs'],1,-1)))){
            			echo "<li>Commisssion ".$donnees_comms['nom'];
            			echo '<ul><li><a href="commission_benevoles.php?id='.$donnees_comms['id'].'">Liste des bénévoles</a></li>';
            			echo "<li><a href='commission_evenements.php?id=".$donnees_comms['id']."'>Liste des événements</a></li></ul></li>";
            		}
            	}
            	echo "</ul>";
            }
            ?>
            <?php if($_SESSION['role'] == 'ADMIN'){
            	echo "<li>Administration du site</li>";
            	echo "<ul>";
            	echo "<li><a href='liste_all_benevoles.php'>Liste des bénévoles</a></li>";
            	echo "<li><a href='liste_commissions.php'>Liste des commissions</a></li>";
            	echo "<li><a href='liste_evenements.php'>Liste des événements</a></li>";
            	echo "</ul>";
        	}?>
            <li><a href="deco.php">Déconnexion</a></li>
        </ul>
    </div>    
</nav>