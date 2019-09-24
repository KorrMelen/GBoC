<?php
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "From: \"Mission Bretonne\"<gboc@missionbretonne.bzh>\n";
	$headers .= 'Content-Type:text/html; charset="utf-8"'."\n";
	$headers .= 'Content-Transfer-Encoding: 8bit';

	function mail_account_created($mail){
		$sujet = "Inscription au module de gestion des bénévols";
	    $message = "Votre inscriptionau module de gestion des bénévoles de la mission bretonne à bien été enregistré. Vous pouvez maintenant vous connecter avec votre addresse mail et votre mot de passe.\n
	    Un mail sera envoyé au différent chargé de commission auxquelle vous voulez participé et vous receverez un mail quand ils auront traité votre demande.";
	    $destinataire = $mail;
	    mail($destinataire,$sujet,$message,$GLOBALS['headers']);
	}

	function mail_volunteer_waiting($mail, $name_volunteer, $name_commission){
		$sujet = "Un bénévoles souhaite rejoindre une commission";
	    $message = $name_volunteer.' souhaite rejoindre la commission '.$name_commission.'. Pour valider sa demande, aller dans la liste des bénévoles de la commission sur le module de gestion des bénévoles.';
	    $destinataire = $mail;
	    mail($destinataire,$sujet,$message,$GLOBALS['headers']);
	}

	function mail_accept_volunteer($mail, $name_commission){
		$sujet = "Votre de demande de participation à une commission a été accepté";
	    $message = 'Votre demande pour participer à la commission '.$name_commission.' à été accepté. Vous pouvez maintenant vous engager comme bénévole pour les différente tâche que cette commission proposera.';
	    $destinataire = $mail;
	    mail($destinataire,$sujet,$message,$GLOBALS['headers']);
	}

	function mail_reject_volunteer($mail, $name_commission){
		$sujet = "Votre de demande de participation à une commission a été rejeté";
	    $message = 'Votre demande pour participer à la commission '.$name_commission.' à été rejeté.';
	    $destinataire = $mail;
	    mail($destinataire,$sujet,$message,$GLOBALS['headers']);
	}

	function mail_volunteer_quit($mail, $name_volunteer, $name_commission){
		$sujet = "Un bénévole à quitté votre commission";
	    $message = $name_volunteer.' ne souhaite plus participer à la commission '.$name_commission.'.';
	    $destinataire = $mail;
	    mail($destinataire,$sujet,$message,$GLOBALS['headers']);
	}

	function mail_volunter_dismiss($mail, $name_commission){
		$sujet = "Vous avez été retiré d'une commission";
	    $message = 'Vous n\'êtes plus inscrit à la commission '.$name_commission.'.';
	    $destinataire = $mail;
	    mail($destinataire,$sujet,$message,$GLOBALS['headers']);
	}
?>