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
?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>GBoC - Liste des commissions</title>
                </head>
                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1>Liste des commissions</h1>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Chargé de commission</td>
                                <td>Mail</td>
                                <td>Numero de Téléphone</td>
                            </tr>
                            <?php
                            $commissions = $bdd->query('SELECT id, nom, moderateurs FROM commissions');
                            while($donnees_comms = $commissions->fetch()){
                                $moderateurs = $bdd->query('SELECT b.id, b.nom, prenom, mail, numerotel FROM benevoles AS b, commissions AS C WHERE c.nom =\''.$donnees_comms['nom'].'\' AND b.id = ANY (moderateurs)');
                                $moderateurs = $moderateurs->fetchall();
                                ?>
                                <tr>
                                    <td><?php echo $donnees_comms['nom']?></td>
                                    <td><?php echo $moderateurs[0]['nom'].' '.$moderateurs[0]['prenom'];
                                    for($i=1; $i<sizeof($moderateurs); $i++) echo '<br>'.$moderateurs[$i]['nom'].' '.$moderateurs[$i]['prenom']?></td>
                                    <td><?php echo $moderateurs[0]['mail'];
                                    for($i=1; $i<sizeof($moderateurs); $i++) echo '<br>'.$moderateurs[$i]['mail']?></td>
                                    <td><?php echo $moderateurs[0]['numerotel'];
                                    for($i=1; $i<sizeof($moderateurs); $i++) echo '<br>'.$moderateurs[$i]['numerotel']?></td>
                                    <td><form method="post" action="liste_all_benevoles.php">
                                        <input type="hidden" name="id" value=<?php echo'"'.$donnees_comms['id'].'"'?>>
                                        <input type="submit" value="voir la liste des bénévoles participant">
                                    </form></td>
                                </tr><?php
                            }
                            $commissions->closeCursor(); ?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
        }
    }
?>