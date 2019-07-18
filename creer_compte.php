<?php
    include("connection_bdd.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - inscription</title>
    </head>
 
    <body>
        <header></header>
        <div id="corps">
            <h1>Inscription au module de Gestion des Bénévoles Ou des Commissions</h1>
            
            <p>
                Pour vous inscrire au module de Gestion des bénévoles Ou des Commissions, veulliez communiquer les informations suivantes (les champs avec un * sont obligatoires) :<br />
            </p>

            <form method="post" action="compte_cree.php">
                Nom de famille* :<br>
                <input type="text" name="nom" required="" <?php if(isset($_GET['nom'])) echo 'value="'.str_replace('+',' ',$_GET['nom']).'"' ?> ><br>
                Prénom* :<br>
                <input type="text" name="prenom" required="" <?php if(isset($_GET['prenom']))echo 'value="'.$_GET['prenom'].'"' ?> ><br>
                Adresse E-mail* :<br>
                <input type="email" name="mail1" required="" <?php if(isset($_GET['mail1']))echo 'value="'.$_GET['mail1'].'"' ?> ><br>
                répétez l\'adresse E-mail* :<br>
                <input type="email" name="mail2" required=""><br>
                Numéro de telephone :<br>
                <input type="tel" name="tel" pattern="0[0-9]( [0-9]{2}){4}" <?php if(isset($_GET['tel']))echo 'value="'.str_replace('+',' ',$_GET['tel']).'"' ?> ><br>
                Date de naissance* :<br>
                <input type="date" name="ndate" required="" <?php if(isset($_GET['ndate']))echo 'value='.$_GET['ndate'] ?> ><br>
                Participation aux commissions :<br>
                <?php
                    $commissions = $bdd->query('SELECT * FROM commissions WHERE active');
                    while($donnees_comms = $commissions->fetch()){
                        echo '<input type="checkbox" name ="'.$donnees_comms['nom'].'">'.$donnees_comms['nom'].'<br>';
                    }
                ?>
                Mot de passe* :<br>
                <input type="password" name="password1" required=""><br>
                Répétez le mot de passe* :<br>
                <input type="password" name="password2" required=""><br>
                <input type="checkbox" name="charte" required=""> J'ai pris connaissance et j'accepte la <a href="charte.html" target="_blank">Charte d'utilisation</a><br>
                <input type="submit" value="S'inscrire">
            </form>
            <?php 
                if(isset($_GET['error'])){
                    if ($_GET['error'] == "notsame") {
                        echo "Oups, vous avez du faire une erreur en recopiant votre adresse mail ou votre mot de passe. Veuillez recommencer s'il vous plait";
                    }else{
                        echo "Oups, cette adresse mail est déjà enregistrée. Une erreur dans l'adresse? Compte déjà créé?";
                    }
                }
            ?>
            <a href="accueil.php">Retourner à la page de connexion</a><br>
        </div>
        <footer id="pied_de_page"></footer>
    </body>
</html>