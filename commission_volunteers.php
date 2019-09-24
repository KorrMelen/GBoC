<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
        if(!commission_verified($_GET['id'])){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $commission = $db->query('SELECT * FROM commissions WHERE id_commission=\''.$_GET['id'].'\'');
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
                    <h1><?php
                        $data_commission = $commission->fetch();
                        echo"Liste des Bénévoles de la commission ".$data_commission['name_commission']?>
                    </h1>
                    <h3>Bénévoles voulant participer à la commission</h3>
                    <table>
                        <tr>
                            <td>Nom</td>
                            <td>Préname</td>
                            <td>Mail</td>
                            <td>Numero de Téléphone</td>
                            <td>Date de Naissance</td>
                            <td>Commissions</td>
                        </tr>
                        <?php
                        $volunteers = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, birth_date, number_tel, mail, role FROM volunteers, commissions WHERE id_commission = \''.$data_commission['id_commission'].'\' AND id_volunteer = ANY (volunteers_waiting)');
                        while($data_volunteer = $volunteers->fetch()){
                            $commissions = $db->query('SELECT name_commission FROM commissions WHERE \''.$data_volunteer['id_volunteer'].'\' = ANY (volunteers)');?>
                            <tr>
                           		<td><?php echo $data_volunteer['name_volunteer']?></td>
                                <td><?php echo $data_volunteer['surname_volunteer']?></td>
                                <td><?php echo $data_volunteer['mail']?></td>
                                <td><?php echo $data_volunteer['number_tel']?></td>
                                <td><?php echo date("d/m/Y", strtotime($data_volunteer['birth_date']))?></td>
                                <td><?php $data_commissions = $commissions->fetch();
                                echo $data_commissions['name_commission'];
                                while($data_commissions = $commissions->fetch()) echo ', '.$data_commissions['name_commission']?></td>
                                <td><form method="post" action="post_commission_volunteers.php">
                                    <input type="hidden" name="id_volunteer" value=<?php echo'"'.$data_volunteer['id_volunteer'].'"'?>>
                                    <input type="hidden" name="id_commission" value=<?php echo'"'.$data_commission['id_commission'].'"'?>>
                                    <input type="submit" name="waiting" value="Accepter">
                                    <input type="submit" name="waiting" value="Refuser">
                                </form></td>
                            </tr><?php
                        } ?>
                    </table>
                    <h3>Bénévoles participant à la commission</h3>
                    <table>
                        <tr>
                            <td>Nom</td>
                            <td>Préname</td>
                            <td>Mail</td>
                            <td>Numero de Téléphone</td>
                            <td>Date de Naissance</td>
                            <td>Commissions</td>
                        </tr>
                        <?php
                        $volunteers = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, birth_date, number_tel, mail, role FROM volunteers, commissions WHERE id_commission = \''.$data_commission['id_commission'].'\' AND id_volunteer = ANY (volunteers)');
                        while($data_volunteer = $volunteers->fetch()){
                            $commissions = $db->query('SELECT name_commission FROM commissions WHERE \''.$data_volunteer['id_volunteer'].'\' = ANY (volunteers)');?>
                            <tr>
                                <td><?php echo $data_volunteer['name_volunteer']?></td>
                                <td><?php echo $data_volunteer['surname_volunteer']?></td>
                                <td><?php echo $data_volunteer['mail']?></td>
                                <td><?php echo $data_volunteer['number_tel']?></td>
                                <td><?php echo date("d/m/Y", strtotime($data_volunteer['birth_date']))?></td>
                                <td><?php $data_commissions = $commissions->fetch();
                                echo $data_commissions['name_commission'];
                                while($data_commissions = $commissions->fetch()) echo ', '.$data_commissions['name_commission']?></td>
                                <td><form method="post" action="post_commission_volunteers.php">
                                    <input type="hidden" name="id_volunteer" value=<?php echo'"'.$data_volunteer['id_volunteer'].'"'?>>
                                    <input type="hidden" name="id_commission" value=<?php echo'"'.$data_commission['id_commission'].'"'?>>
                                    <input type="submit" name="goodbye" value="Désinscrire de la commission">
                                </form></td>
                            </tr><?php
                        } ?>
                    </table>
                </div>
            </body>
        </html>
        <?php
    }
?>