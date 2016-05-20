<?php
require_once ('../../../../../wp-config.php');
$helper = new HELPER;

$html = '';

/**********/

global $wpdb;
$table_name = $wpdb->prefix . 'fly_pilotes';
$pilotes_orga = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE id="'.$_POST['pilote_vol'].'"ORDER BY date DESC', OBJECT );

$user = $helper->user();

/*********/


if(empty($pilotes_orga)):

	$html .= '<p class="alert alert-warning text-center">Aucun pilote n\'a réservé le vol</p>';

else:

	foreach($pilotes_orga as $pilote):

		if(!empty($pilote->naissance) AND $pilote->naissance != '0000-00-00' AND $pilote->naissance != '1970-01-01'):
			$date = date('d/m/Y', strtotime($pilote->naissance));
			$naissance = ', '.age($date).' ans';
		else:
			$naissance = '';
		endif;

		/***/

		$table_name = $wpdb->prefix . 'fly_aeroclubs';
		$aeroclub = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$pilote->aeroclub.'" ', OBJECT );

		/***/

		if(!empty($pilote->aeroclub_custom)):
			$aero = substr($pilote->aeroclub_custom, 0, 35);
		else:
			$aero = substr($aeroclub->nom, 0, 35);
		endif;

		/***/

		if(file_exists($_SERVER['DOCUMENT_ROOT'].$pilote->photo) && !empty($pilote->photo)):
			$photo = $pilote->photo;
		else:
			$photo = '/wp-content/plugins/flymate/images/avatar.png';
		endif;

		if($pilote->heure_total == '0'): $heure_total = ''; else: $heure_total = $pilote->heure_total.' heure(s)'; endif;
		if($pilote->heure_commandant == '0'): $heure_commandant = ''; else: $heure_commandant = $pilote->heure_commandant.' heure(s)'; endif;	

		$html .= $helper->showPilotes($pilote, $photo, $user);

		
	endforeach;

endif;

/**********/
/**********/
/**********/
/**********/

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vol_reserve';
$reserves = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE id_vol="'.$_POST['id_vol'].'"', OBJECT );

/***/

foreach($reserves as $reserve):

	global $wpdb;
	$table_name = $wpdb->prefix . 'fly_pilotes';
	$pilotes_reserve = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$reserve->id_pilote.'"', OBJECT );

	$html .= $helper->showPilotes($pilotes_reserve, $photo, $user, $reserve);

endforeach;

$return = array(
    'html' => $html
);

echo json_encode($return);
exit();

?>