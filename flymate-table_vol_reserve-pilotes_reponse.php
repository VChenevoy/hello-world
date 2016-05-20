<?php
require_once ('../../../../../wp-config.php');
$helper = new HELPER;

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vol_reserve';

$date = date('Y-m-d');
$helper = new HELPER;

$html = '';


if($_POST['status'] == '1'):

    $item = array(
        'status' => 1,
    );

    $where = array(
        'id' => $_POST['reservation'],
    );

    $result = $wpdb->update($table_name, $item, $where);
    $reserve = $wpdb->insert_id;

    /***********/

    if($result):

      global $wpdb;
      $table_name = $wpdb->prefix . 'fly_vols';
      $vol = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$_POST['vol'].'" ', OBJECT );

      /****/

      $userVol = $helper->infos_profil($vol->id_pilote);
      $userReserve = $helper->infos_profil($_POST['pilote']);

      $title = 'Réservation pour le vol ' . $vol->title.' validée';
      $link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['vol'];

      $message = 'Bonjour '.$userReserve->prenom.' '.$userReserve->nom.'<br /><br />';
      $message .= 'Votre réservation '.$vol->title.' est <strong>validée</strong><br /><br />';

      if(!empty($_POST['messageDemandeur'])):
        $message .= 'Message de l\'organisateur : <br />';
        $message .= stripslashes($_POST['messageDemandeur']).'<br /><br />';
      endif;

      $message .= 'Vous pouvez retrouvé le détail du vol ici <br /><br />';
      $message .= 'Cordialement';

      $button = 'Accéder au vol';

      $body = $helper->templateMail($title, $message, $link, $button);  

      $headers = array('Content-Type: text/html; charset=UTF-8');

      wp_mail($userReserve->email, $title , $body, $headers);

      $html .= '<div class="alert alert-info" role="alert">';
        $html .= 'La réservation est validé, le pilote va recevoir un email de confirmation.';
      $html .= '</div>';
    else:
      $html .= '<div class="alert alert-danger" role="alert">';
        $html .= 'Erreur de validation pour la réservation';
      $html .= '</div>';      
    endif;

endif;

if($_POST['status'] == '2'):

  $where = array(
      'id' => $_POST['reservation'],
  );

  $result = $wpdb->delete($table_name, $where);

  /***********/

  if($result):

    global $wpdb;
    $table_name = $wpdb->prefix . 'fly_vols';
    $vol = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$_POST['vol'].'" ', OBJECT );

    /****/

    $userVol = $helper->infos_profil($vol->id_pilote);
    $userReserve = $helper->infos_profil($_POST['pilote']);

    //$link = 'http://'.$_SERVER['HTTP_HOST'].'/?page_id=104&page=viewVol&id='.$_POST['vol'];
    $title = 'Réservation pour le vol ' . $vol->title.' refusée';

    $message = 'Bonjour '.$userReserve->prenom.' '.$userReserve->nom.'<br /><br />';
    $message .= 'Votre réservation '.$vol->title.' à été <strong>refusé</strong><br /><br />';

    if(!empty($_POST['messageDemandeur'])):
      $message .= 'Message de l\'organisateur : <br />';
      $message .= $_POST['messageDemandeur'].'<br /><br />';
    endif;

    $message .= 'Cordialement';

    $body = $helper->templateMail($title, $message, '', '');  

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($userReserve->email, $title , $body, $headers);

    $html .= '<div class="alert alert-info" role="alert">';
      $html .= 'La réservation est refusé, le pilote va recevoir un email de refus.';
    $html .= '</div>';
  else:
    $html .= '<div class="alert alert-danger" role="alert">';
      $html .= 'Erreur de refus pour la réservation';
    $html .= '</div>';      
  endif;

endif;

$return = array(
    'html' => $html
);

echo json_encode($return);
exit();

?>