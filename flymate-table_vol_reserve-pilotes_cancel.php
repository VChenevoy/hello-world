<?php
require_once ('../../../../../wp-config.php');
$helper = new HELPER;

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vol_reserve';

$date = date('Y-m-d');
$helper = new HELPER;

$html = '';

if(!empty($_POST['reservation'])):

	$where = array(
	  'id' => $_POST['reservation'],
	);

	$result = $wpdb->delete($table_name, $where);


	/**/

	global $wpdb;
    $table_name = $wpdb->prefix . 'fly_vols';
    $vol = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$_POST['vol'].'" ', OBJECT );

    /****/

    $userVol = $helper->infos_profil($vol->id_pilote);
  	$userReserve = $helper->infos_profil($_POST['pilote']);

  	// Message pour le pilote qui à annulé
	$title = 'Réservation pour le vol ' . $vol->title.' annulé';
	$link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['vol'];

  	$message = 'Bonjour '.$userReserve->prenom.' '.$userReserve->nom.'<br /><br />';
  	$message .= 'Votre réservation '.$vol->title.' est <strong>annulé</strong><br /><br />';

  	/*if(!empty($_POST['messageAnnulation'])):
    	$message .= 'Message du pilote : <br />';
    	$message .= $_POST['messageAnnulation'].'<br /><br />';
  	endif;*/

  	$message .= 'Cordialement';

  	$button = 'Accéder au vol';

  	$body = $helper->templateMail($title, $message, '', '');  

  	$headers = array('Content-Type: text/html; charset=UTF-8');

  	wp_mail($userReserve->email, $title , $body, $headers);

  	/**/

  	// Message pour l'organisateur
	$title = 'Une réservation pour le vol ' . $vol->title.' à été annulé';
	$link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['vol'];

  	$message = 'Bonjour '.$userVol->prenom.' '.$userVol->nom.'<br /><br />';
  	$message .= 'La réservation de '.$userReserve->prenom.' '.$userReserve->nom.' pour le vol '.$vol->title.' est <strong>annulé</strong><br /><br />';

  	if(!empty($_POST['messageAnnulation'])):
    	$message .= 'Message du pilote : <br />';
    	$message .= stripslashes($_POST['messageAnnulation']).'<br /><br />';
  	endif;

  	$message .= 'Cordialement';

  	$button = 'Accéder au vol';

  	$body = $helper->templateMail($title, $message, $link, $button);  

  	$headers = array('Content-Type: text/html; charset=UTF-8');

  	wp_mail($userVol->email, $title , $body, $headers);

endif;

$return = array(
    'html' => $html
);

echo json_encode($return);
exit();