<?php
require_once ('../../../../../wp-config.php');
$helper = new HELPER;

$html = '';

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols_coms';

$item = array(
  'vol' => $_POST['vol'],
  'pilote' => $helper->session(),
  'date' => date('Y-m-d H:i:s'),
  'message' => $_POST['message'],
);

$result = $wpdb->insert($table_name, $item);

if($result):

	global $wpdb;

	$table_name = $wpdb->prefix . 'fly_vols';
	$vol = $wpdb->get_row( 'SELECT *  FROM '.$table_name.' WHERE id = "'.$_POST['vol'].'" ', OBJECT );

	/**/

	$table_name = $wpdb->prefix . 'fly_vols_coms';
	$coms = $wpdb->get_results( 'SELECT *  FROM '.$table_name.' WHERE vol = "'.$_POST['vol'].'" ORDER BY date DESC', OBJECT );

	/***/

	foreach($coms as $com):

		$profil = $helper->infos_profil($com->pilote);

		$title = 'Nouveau commentaire sur le vol ' . $vol->title;
	    $link = 'http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$_POST['vol'].'#Commentaires';

	    $text = 'Bonjour '.$profil->prenom.' '.$profil->nom.'<br /><br />';
	    $text .= 'Message :<br /> '.$_POST['message'].'<br />';
	    $button = 'Accéder au commentaire';

	    $body = $helper->templateMail($title, $text, $link, $button);   

	    $headers = array('Content-Type: text/html; charset=UTF-8');
	    wp_mail($profil->email, 'Nouveau commentaire ' . $vol->title, $body, $headers);

	endforeach;

    /***/

    $profil = $helper->user();

	$title = 'Nouveau commentaire';
    $link = 'http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$_POST['vol'].'#Commentaires';

    $text = 'Bonjour l\'équipe Flymates<br /><br />';
    $text .= 'Message :<br /> '.$_POST['message'].'<br />';
    $button = 'Accéder au commentaire';

    $body = $helper->templateMail($title, $text, $link, $button);   

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail('contact@flymates.fr','Nouveau commentaire',$body,$headers);

    /**/

    header('Location: http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$_POST['vol'].'&com=valid#Commentaires');

endif;

?>