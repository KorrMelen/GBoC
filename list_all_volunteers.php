<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    if($_SESSION['role'] != 'ADMIN'){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else{
?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>GBoC - Liste des bénévoles</title>
                </head>
             
                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1>Liste des bénévoles</h1>
                        <table>
                            <tr>
                                <td>Nom</td>
                                <td>Prénom</td>
                                <td>Mail</td>
                                <td>Numero de Téléphone</td>
                                <td>Date de Naissance</td>
                                <td>Commissions</td>
                                <td>rôle</td>
                            </tr>
                            <?php
                                $volunteers = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, birth_date, number_tel, mail, role FROM volunteers');
                                while($data_volunteer = $volunteers->fetch()){
                                    $commissions = $db->query('SELECT name_commission FROM commissions WHERE \''.$data_volunteer['id_volunteer'].'\' = ANY (volunteers)');?>
                                    <tr>
                                        <td><?php echo $data_volunteer['name_volunteer']?></td>
                                        <td><?php echo $data_volunteer['surname_volunteer']?></td>
                                        <td><?php echo $data_volunteer['mail']?></td>
                                        <td><?php echo $data_volunteer['number_tel']?></td>
                                        <td><?php echo date("d/m/Y", strtotime($data_volunteer['birth_date']))?></td>
                                        <td><?php $data_commission = $commissions->fetch();
                                        echo $data_commission['name_commission'];
                                        while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?></td>
                                        <td><?php
                                        echo $data_volunteer['role'].'<br>';
                                        if($data_volunteer['role'] == 'MODERATOR'){
                                            $moderator = $db->query('SELECT name_commission FROM commissions WHERE \''.$data_volunteer['id_volunteer'].'\' = ANY(moderators)');
                                            $data_moderator = $moderator->fetch();
                                            echo "(".$data_moderator['name_commission'];
                                            while($data_moderator = $moderator->fetch()) echo ', '.$data_moderator['name_commission'];
                                            echo ')';
                                            $moderator->closeCursor();
                                        } ?>
                                        </td>
                                    </tr><?php
                                    $commissions->closeCursor();
                                }
                                $volunteers->closeCursor();
                            ?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
    }
?>