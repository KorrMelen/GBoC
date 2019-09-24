<?php
    include("functions.php");
    $db = connecting_db();

    if($_POST['mail'] != $_POST['mail_repeated'] || $_POST['password'] != $_POST['password_repeated']){
        header('location: create_account.php?name='.str_replace(' ','+',$_POST['name']).'&surname='.$_POST['surname'].'&mail='.$_POST['mail'].'&tel='.str_replace(' ','+',$_POST['tel']).'&birth_date='.$_POST['birth_date'].'&error=notsame');
    }else{
        $volunteers = $db->prepare('SELECT * FROM volunteers WHERE mail=:mail');
        $volunteers->execute(array('mail'=>$_POST['mail']));
        if($volunteers->rowCount() > 0){
            header('location: create_account.php?name='.str_replace(' ','+',$_POST['name']).'&surname='.$_POST['surname'].'&mail='.$_POST['mail'].'&tel='.str_replace(' ','+',$_POST['tel']).'&birth_date='.$_POST['birth_date'].'&error=mailexist');
        }else{
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $uuid = uuid();
            $addvolunteer = $db->prepare('INSERT INTO volunteers VALUES(:id,:name,:surname,:birth_date,:tel,:mail,:password,\'VOLUNTEER\')');
            $addvolunteer->execute(array(
                'id' => $uuid,
                'name' => strtoupper($_POST['name']),
                'surname' =>ucwords ($_POST['surname']," -'_/"),
                'birth_date' => $_POST['birth_date'],
                'tel' => $_POST['tel'],
                'mail' => $_POST['mail'],
                'password' => $password
            ));

            $commissions = $db->query('SELECT * FROM commissions');
            $addcom = $db->prepare('UPDATE commissions SET volunteers_waiting = array_append(volunteers_waiting, :uuid) WHERE id_commission=:id');
            while($data_commission = $commissions->fetch()){
                if(isset($_POST[$data_commission['name_commission']])){
                    $addcom->execute(array(
                        'uuid' =>$uuid,
                        'id' => $data_commission['id_commission']
                    ));
                    $moderators = $db->query('SELECT mail FROM volunteers, commissions WHERE id_commission =\''.$data_commission['id_commission'].'\' AND id_volunteer = ANY(moderators)');
                    $data_moderator = $moderators->fetch();
                    $mail = $data_moderator['mail'];
                    while ($data_moderator = $moderators->fetch()){
                        $mail .= ', '.$data_moderator['mail'];
                    }
                    mail_volunteer_waiting($mail, strtoupper($_POST['name']).' '.ucwords ($_POST['surname']," -'_/"), $data_commission['name_commission']);
                }
            }
            mail_account_created($_POST['mail']);
            header('location: reception.php?error=account_created');
            $commissions->closeCursor();
        }
        $volunteers->closeCursor();
    }
?>