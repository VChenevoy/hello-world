<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
$helper = new HELPER;

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vol_reserve';

$date = date('Y-m-d');
$helper = new HELPER;

$html = '';

$item = array(
    'id_vol' => $_POST['id_vol'],
    'id_pilote' => $_POST['id_pilote'],
    'status' => $_POST['status'],
    'date' => $date
);

$result = $wpdb->insert($table_name, $item);
$reserve = $wpdb->insert_id;

/***********/

if($result):

  global $wpdb;
  $table_name = $wpdb->prefix . 'fly_vols';
  $vol = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$_POST['id_vol'].'" ', OBJECT );

  /****/

  $userVol = $helper->infos_profil($vol->id_pilote);
  $userReserve = $helper->infos_profil($_POST['id_pilote']);

  //Message pour l'organisateur
  $title = 'Nouvelle réservation : '.$vol->title;
  $link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['id_vol'];

  $text = 'Bonjour '.$userVol->prenom.' '.$userVol->nom.'<br /><br />';
  $text .= 'Vous avez une nouvelle réservation de '.$userReserve->prenom.' '.$userReserve->nom.'<br /><br />';
  
  if(!empty($_POST['message'])):
    $text .= 'Message du pilote demandeur : <br />';
    $text .= stripslashes($_POST['message']).'<br /><br />';
  endif;

  $text .= 'Il faut maintenant confirmer cette réservation ou l\'annuler.<br /><br />';
  $button = 'VOIR LA DEMANDE DE RÉSERVATION';

  $body = $helper->templateMail($title, $text, $link, $button);   

  $headers = array('Content-Type: text/html; charset=UTF-8');
  wp_mail($userVol->email,'Nouvelle réservation : '.$vol->title,$body,$headers);

  /**/

  //Message pour Flymates

  $title = 'Nouvelle réservation pour le vol '.$vol->title;
  $link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['id_vol'];

  $text = 'Bonjour l\'équipe Flymates<br /><br />';
  $text .= 'Nouvelle réservation de '.$userReserve->prenom.' '.$userReserve->nom.' pour le vol '.$vol->title.'<br /><br />';
  
  if(!empty($_POST['message'])):
    $text .= 'Message du pilote demandeur : <br />';
    $text .= stripslashes($_POST['message']).'<br /><br />';
  endif;

  $text .= 'Cordialement<br /><br />';

  $button = 'VISUALISER LE VOL';

  $body = $helper->templateMail($title, $text, $link, $button);   

  $headers = array('Content-Type: text/html; charset=UTF-8');
  wp_mail('contact@flymates.fr','Nouvelle réservation : '.$vol->title,$body,$headers);

  /**/
  //Message pour le pilote

  $title = 'Votre réservation : '.$vol->title;
  //$link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['id_vol'];

  $text = 'Bonjour '.$userReserve->prenom.' '.$userReserve->nom.'<br /><br />';
  $text .= 'Votre réservation à bien été prise en compte, un message a été envoyé à l\'organisateur. Vous recevrez prochainement la confirmation de votre partage de vol par l\'organisateur.<br /><br />';
  
  $text .= 'Cordialement';

  $body = $helper->templateMail($title, $text, '', '');   

  $headers = array('Content-Type: text/html; charset=UTF-8');
  wp_mail($userReserve->email, $title, $body, $headers);

  /****/

  $html .= '<div class="alert alert-info" role="alert">';
    $html .= 'Votre réservation est réalisée, vous recevrez un email de confirmation quand l\'organisateur aura validé votre réservation';
  $html .= '</div>';
else:
  $html .= '<div class="alert alert-danger" role="alert">';
    $html .= 'Erreur lors de votre réservation';
  $html .= '</div>';      
endif;

$return = array(
    'html' => $html
);

echo json_encode($return);
exit();

?>