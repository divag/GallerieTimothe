<?php  

// nom du fichier contenant les informations
$file=$_GET['fileurl'];
if(empty($file) || !file_exists($file))
	return "ERREUR";

// on récupère le contenu du fichier 
$filecontent=file_get_contents($file);

// on parse le fichier
$params=explode("|||",$filecontent);

$mailAdmin = $params[0];
$mailDest=$params[1];
$mailTitle=$params[2];
$mailText=$params[3];
$mailHtml=$params[4];

  /**
	    * o------------------------------------------------------------------------------o
	    * | This package is licensed under the Phpguru license. A quick summary is       |
	    * | that for commercial use, there is a small one-time licensing fee to pay. For |
	    * | registered charities and educational institutes there is a reduced license   |
	    * | fee available. You can read more  at:                                        |
	    * |                                                                              |
	    * |                  http://www.phpguru.org/static/license.html                  |
	    * o------------------------------------------------------------------------------o
	    *
	    * � Copyright 2008,2009 Richard Heyes
	    */
	
	    require_once('Rmail/Rmail.php');
	    
	    
	    /*
	     * Initailisation des variables
	     */
	    $from='ThisIsRadioclash Contact <'.$mailAdmin.'>';
	    $addresses = array();
	    $addresses[]=$mail;
	    $sujet='[This Is Radioclash] : Acces "CHEF" !';
		
		$mail_text  = "Yo !\r\n\r\nEn tant que chef d'une équipe de RadioClash, vous avez le droit à un accès \"CHEF\" qui vous permet d'uploader directement sur le site vos fichier d'�mission :\r\n\r\n";
		$mail_text .= " - La pochette en JPG (taille : 346X346)\r\n";
		$mail_text .= " - La pochette en GIF anim� s'il y en a une\r\n";
		$mail_text .= " - Un MP3 de teaser de l'�mission\r\n";
		$mail_text .= " - LE MP3 DE L'EMISSION (150Mo maxi - pour 100Mo, il faut patienter environ 15 minutes pendant l'upload)\r\n\r\n";
		$mail_text .= "Pour cela, se connecter ici (Attention : Merci de ne pas partager cette adresse sur l'INTERNET !!) :\r\n\r\n";
		$mail_text .= " - http://www.thisisradioclash.org/admin/ (copier-coller le lien dans votre navigateur)\r\n\r\n";
		$mail_text .= "Voici vos identifiants de connexion � This Is Radioclash, acc�s \"CHEF\" :\r\n\r\n";
		$mail_text .= " - Login : \"".$login."\"\r\n";
		$mail_text .= " - Mot de passe : \"".$password."\"\r\n";
		$mail_text .= "\r\n";
		
	    $mail_html="Yo !<br /><br />
 En tant que chef d'une &eacute;quipe de RadioClash, vous avez le droit &agrave; un acc&egrave;s \"CHEF\" qui vous permet d'uploader directement sur le site vos fichier d'�mission :
 <ul>
	<li>La pochette en JPG (taille : 346X346)</li>
	<li>La pochette en GIF anim&eacute; s'il y en a une</li>
	<li>Un MP3 de teaser de l'&eacute;mission</li>
	<li>LE MP3 DE L'EMISSION (150Mo maxi - pour 100Mo, il faut patienter environ 15 minutes pendant l'upload)</li>
</ul>
<p><u>Pour cela, se connecter ici (<font style=\"color:red;\">Attention : Merci de ne pas partager cette adresse sur l'INTERNET !!</font>) :</u></p>
<ul>
	<li><a href=\"http://www.thisisradioclash.org/admin/\" target=\"blank\"><b>http://www.thisisradioclash.org/admin/</b></a></li>
</ul>
<p><u>Voici vos identifiants de connexion &agrave; This Is Radioclash, acc&egrave;s \"CHEF\" :</u></p>
 <ul>
   <li>Login : \"".$login."\"</li>
   <li>Mot de passe : \"".$password."\"</li>
 </ul>
</td></tr>
</table>";

	    $mail = new Rmail();
	
	    /**
	    * Set the from address of the email
	    */
	    $mail->setFrom($from);
	    
	    /**
	    * Set the subject of the email
	    */
	    
	    $mail->setSubject($sujet);
	    
	    /**
	    * Set high priority for the email. This can also be:
	    * high/normal/low/1/3/5
	    */
	    //$mail->setPriority('high');
	
	    /**
	    * Set the text of the Email
	    */
	    //$mail->setTextCharset("utf-8");
	    $mail->setText($mail_text);
	    
	    /**
	    * Set the HTML of the email. Any embedded images will be automatically found as long as you have added them
	    * using addEmbeddedImage() as below.
	    */
	    $mail->setHTML($mail_html);
	    	    
	    /**
	    * Set the delivery receipt of the email. This should be an email address that the receipt should be sent to.
	    * You are NOT guaranteed to receive this receipt - it is dependent on the receiver.
	    */
	    //$mail->setReceipt('webmaster@musicdestock.fr');
	    
	    /**
	    * Add an embedded image. The path is the file path to the image.
	    */
	    // $mail->addEmbeddedImage(new fileEmbeddedImage('background.gif'));
	    
	    /**
	    * Add an attachment to the email.
	    */
	    // $mail->addAttachment(new fileAttachment($fichier_xml));
	
	    /**
	    * Send the email. Pass the method an array of recipients.
	    */	  
	    $result  = $mail->send($addresses);	    	
		if($result) 
		{
			echo 'Votre mot de passe a �t� envoy� sur l\\\'adresse : \n\n - '; print_r($_GET['to']);        
		}

?>