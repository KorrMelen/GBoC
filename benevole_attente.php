<?php
	 if(!isset($_SESSION['uuid'])){
        header('location: accueil.php');
    }else{
        try{
            $bdd = new PDO('pgsql:host=localhost;port=5432;dbname=gboc;user=super_admin;password=super_admin');
        }catch (Exception $e){
            die('Erreur : ' . $e->getMessage());
        }
        if($_SESSION['role'] == 'BENEVOLE'){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
?>

Liste des benevoles voulant participer à la commission:
	<table>
        <tr>
            <td>Nom</td>
            <td>Prénom</td>
            <td>Mail</td>
            <td>Numero de Téléphone</td>
            <td>Date de Naissance</td>
            <td>Commissions</td>
        </tr>
        <?php
        	$reponse = $bdd->query('SELECT b.id, b.nom, prenom, datenaissance, numerotel, mail, role FROM benevoles AS b, commissions AS c WHERE c.id = \''.$_SESSION['commission'].'\' AND b.id = ANY (benevolesAttente)');
        	while($reponse = $reponse->fetch()){ ?>
        		<td><?php echo $repinse['nom']?></td>
                <td><?php echo $donnees['prenom']?></td>
                <td><?php echo $donnees['mail']?></td>
                <td><?php echo $donnees['numerotel']?></td>
                <td><?php echo $donnees['datenaissance']?></td>
                <td><?php $com = $commissions->fetch();
                echo $com['nom'];
                while($com = $commissions->fetch()) echo ', '.$com['nom']?></td>
        	}
        }
	}
?>