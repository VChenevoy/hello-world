<?php

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols';

$date = date('d-m-Y');
$helper = new HELPER;

if($_POST['id'] == '0' || empty($_POST['id'])):

  $item = array(
      'id_pilote' => $_POST['id_pilote'],
      'title' => $_POST['title'],
      'depart' => $_POST['depart'],
      'arriver' => $_POST['arriver'],
      'escale' => $_POST['rotation'],
      'number' => $_POST['number'],
      'date' => date('Y-m-d', strtotime($_POST['date'])),      
      'heure_down' => $_POST['heure_down'],
      'heure_up' => $_POST['heure_up'],
      'aeronef' => $_POST['aeronef'], 
      'attente' => str_replace("\n", '\n', stripslashes($_POST['attente'])),
      'details' => str_replace("\n", '\n', stripslashes($_POST['description'])),
  );

  $result = $wpdb->insert($table_name, $item);
  $id_vol = $wpdb->insert_id;

  $titleMail = 'Nouveau vol';
  $textMail = 'Un nouveau vol à été partagé';

else:

  $item = array(
      'title' => $_POST['title'],
      'depart' => $_POST['depart'],
      'arriver' => $_POST['arriver'],
      'escale' => $_POST['rotation'],
      'number' => $_POST['number'],
      'date' => date('Y-m-d', strtotime($_POST['date'])),      
      'heure_down' => $_POST['heure_down'],
      'heure_up' => $_POST['heure_up'],
      'aeronef' => $_POST['aeronef'], 
      'attente' => str_replace("\n", '\n', stripslashes($_POST['attente'])),
      'details' => str_replace("\n", '\n', stripslashes($_POST['description'])),
  );

/**/

  $where = array(
      'id' => $_POST['id'],
  );

  $result = $wpdb->update($table_name, $item, $where);

  $titleMail = 'Modification de vol';
  $textMail = 'Un vol à été modifié';

endif;

/***********/

if($result):

  $table_name = $wpdb->prefix . 'fly_ad_france';
  $depart = $wpdb->get_row( 'SELECT nom FROM '.$table_name.' WHERE id="'.$_POST['depart'].'" ', OBJECT );
  $arriver = $wpdb->get_row( 'SELECT nom FROM '.$table_name.' WHERE id="'.$_POST['arriver'].'" ', OBJECT );

  /***/

  // Email notif contact

  /*$title = $titleMail.' : '.$_POST['title'];
  $link = 'http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$id_vol;

  $text = 'Bonjour L\'équipe Flymates<br />';
  $text .= $textMail.' : '.$_POST['title'].'.<br />';
  $text .= 'Date : '.strftime('%e %B %G', strtotime($_POST['date'])).'<br />';
  $text .= 'Départ : '.$depart->nom.'<br />';
  $text .= 'Arrivée : '.$arriver->nom.'<br />';
  $text .= 'Place disponible : '.$_POST['number'].'<br /><br />';
  $button = 'Accéder au vol';

  $body = $helper->templateMail($title, $text, $link, $button);   

  $headers = array('Content-Type: text/html; charset=UTF-8');
  wp_mail('contact@flymates.fr', $titleMail.' : '.$_POST['title'],$body,$headers);*/

  /****/

  $table_name = $wpdb->prefix . 'fly_pilotes';

  /*************************************************/

  // Notification All pilote

  $pilotes = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE notif_all = "1" AND id != "'.$helper->session().'" ', OBJECT );

  /**********/

  $piloteDeja = array();

  foreach ($pilotes as $pilote):  

    /*$title = $titleMail.' : '.$_POST['title'];
    $link = 'http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$id_vol;

    $text = 'Bonjour '.$pilote->prenom.' '.$pilote->nom.'<br />';
    $text .= $textMail.' : '.$_POST['title'].'.<br />';
    $text .= 'Date : '.strftime('%e %B %G', strtotime($_POST['date'])).'<br />';
    $text .= 'Départ : '.$depart->nom.'<br />';
    $text .= 'Arrivée : '.$arriver->nom.'<br />';
    $text .= 'Place disponible : '.$_POST['number'].'<br /><br />';
    $button = 'Accéder au vol';

    $body = $helper->templateMail($title, $text, $link, $button);   

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail($pilote->email, $titleMail.' : '.$_POST['title'],$body,$headers);*/

    /****/

    $piloteDeja[$pilote->id] = $pilote->id; 

  endforeach;

  /*************************************************/

  // Notification aero pilote

  if(!empty($piloteDeja)):
    $sqlPiloteDeja = ' AND id NOT IN('.implode(',', $piloteDeja).')';
  else:
    $sqlPiloteDeja = '';
  endif;

  /***/

  $profil = $helper->infos_profil();
  $pilotesAero = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE aeroclub = "'.$profil->aeroclub.'" AND notif_aero = "1" AND id != "'.$helper->session().'" '.$sqlPiloteDeja.' ', OBJECT );

  /**********/

  foreach ($pilotesAero as $pilote):  

    /*$title = $titleMail.' : '.$_POST['title'];
    $link = 'http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$id_vol;

    $text = 'Bonjour '.$pilote->prenom.' '.$pilote->nom.'<br />';
    $text .= $textMail.' : '.$_POST['title'].'.<br />';
    $text .= 'Date : '.strftime('%e %B %G', strtotime($_POST['date'])).'<br />';
    $text .= 'Départ : '.$depart->nom.'<br />';
    $text .= 'Arrivée : '.$arriver->nom.'<br />';
    $text .= 'Place disponible : '.$_POST['number'].'<br /><br />';
    $button = 'Accéder au vol';

    $body = $helper->templateMail($title, $text, $link, $button);   

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail($pilote->email, $titleMail.' : '.$_POST['title'],$body,$headers);*/

    /****/

    $piloteDeja[$pilote->id] = $pilote->id; 

  endforeach;

  /*************************************************/

  // Notification friend pilote

  $table_name = $wpdb->prefix . 'fly_friends';
  $friends = $wpdb->get_results( 'SELECT fr.f_to, fr.f_from
                     FROM '.$table_name.' as fr
                     WHERE fr.status = "0" AND (fr.f_to = '.$helper->session().' OR fr.f_from = '.$helper->session().') ', OBJECT );
  $fries = array();

  foreach($friends as $friend):

    if($friend->f_to != $helper->session()):
      $fries[] = $friend->f_to;
    endif;

    if($friend->f_from != $helper->session()):
      $fries[] = $friend->f_from;
    endif;

  endforeach;

  /***/

  if(!empty($piloteDeja)):
    $sqlPiloteDeja = ' AND id NOT IN('.implode(',', $piloteDeja).')';
  else:
    $sqlPiloteDeja = '';
  endif;

  /***/

  $table_name = $wpdb->prefix . 'fly_pilotes';
  $pilotesFriend = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE id != "'.$helper->session().'" AND notif_friend = "1" AND id IN('.implode(',', $fries).') '.$sqlPiloteDeja.' ', OBJECT );

  /**********/

  foreach ($pilotesFriend as $pilote):  

    /*$title = $titleMail.' : '.$_POST['title'];
    $link = 'http://'.$_SERVER['HTTP_HOST'].'/les-vols-proposes/?page=viewVol&id='.$id_vol;

    $text = 'Bonjour '.$pilote->prenom.' '.$pilote->nom.'<br />';
    $text .= $textMail.' : '.$_POST['title'].'.<br />';
    $text .= 'Date : '.strftime('%e %B %G', strtotime($_POST['date'])).'<br />';
    $text .= 'Départ : '.$depart->nom.'<br />';
    $text .= 'Arrivée : '.$arriver->nom.'<br />';
    $text .= 'Place disponible : '.$_POST['number'].'<br /><br />';
    $button = 'Accéder au vol';

    $body = $helper->templateMail($title, $text, $link, $button);   

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail($pilote->email,'Nouveau vol : '.$_POST['title'],$body,$headers);*/

  endforeach;

  echo '<div class="alert alert-success" role="alert">';
  echo 'Votre vol est partagé.';
  echo '</div>';
  echo '<script>setTimeout(function(){ location.href="/?page_id=104" }, 500);</script>';
else:
  echo '<div class="alert alert-danger" role="alert">';
  echo 'Erreur lors du partage de votre vol';
  echo '<br />';
  echo '<a href="/?page_id=149">Recommencer mon inscription</a>';
  echo '</div>';      
endif;
?>