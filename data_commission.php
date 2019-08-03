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
        $commission = $db->query('SELECT * FROM commissions WHERE id_commission=\''.$_GET['id_commission'].'\'');
        if($commission->rowCount() == 0){
            hearder('location: list_commission.php');
        }
        $commission = $commission->fetch();
?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>GBoC - Commission</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    <table>
                        <tr>
                            <td>Chargé de commission</td>
                            <td>Mail</td>
                            <td>Numero de Téléphone</td>
                        </tr>
                        <?php
                        $moderators = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, mail, number_tel FROM volunteers, commissions WHERE id_commission =\''.$commission['id_commission'].'\' AND id_volunteer = ANY (moderators)');
                        while($data_moderator = $moderators->fetch()){?>
                            <tr>
                                <td><?php echo $data_moderator['name_volunteer'].' '.$data_moderator['surname_volunteer']?></td>
                                <td><?php echo $data_moderator['mail']?></td>
                                <td><?php echo $data_moderator['number_tel']?></td>
                                <?php if($moderators->rowcount() > 1){?>
                                    <td><form method="post" action="post_crud_commission.php">
                                        <input type="hidden" name="id_moderator" value=<?php echo'"'.$data_moderator['id_volunteer'].'"'?>>
                                        <input type="hidden" name="id_commission" value=<?php echo'"'.$commission['id_commission'].'"'?>>
                                        <input type="submit" name="remove_moderator" value="Retirer moderateur">
                                    </form></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </table>

                    <form method="post" action="post_crud_commission.php">
                        <input type="text" list="volunteers" name="moderator" size="40" required=""><br>
                        <datalist id="volunteers">
                            <?php $volunteers = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, mail FROM volunteers, commissions WHERE id_commission =\''.$commission['id_commission'].'\' AND id_volunteer != ALL (moderators)');
                            while($data_volunteer=$volunteers->fetch()){
                                echo '<option value="'.$data_volunteer['name_volunteer'].' '.$data_volunteer['surname_volunteer'].' ('.$data_volunteer['mail'].')"></option>';
                            } ?>
                        </datalist>
                        <input type="hidden" name="id_commission" value=<?php echo '"'.$commission['id_commission'].'"'?>>
                        <input type="submit" name="add_moderator" value="Ajouter un moderateur">
                    </form>

                    <?php if($commission['active']){ ?>
                        <form method="post" action=<?php echo '"commission_volunteers.php?id='.$commission['id_commission'].'"'?>>
                            <input type="submit" value="Voir la liste des bénévoles participant">
                        </form>
                        <form method="post" action=<?php echo '"commission_events.php?id='.$commission['id_commission'].'"'?>>
                            <input type="submit" value="Voir la liste des taches créer">
                        </form>
                    <?php } ?>
                    <form method="post" action="post_crud_commission.php">
                        <input type="hidden" name="id_commission" value=<?php echo '"'.$commission['id_commission'].'"'?>>
                        <input type="submit" <?php if($commission['active']) echo 'name="disable_commission" value="Désactiver commission"'; else echo 'name="reactivate_commission" value="Réactiver commission"'?>>
                    </form>
                </div>
            </body>
        </html>
        <?php
    }
?>