<?php
require_once ('../../../../../wp-config.php');
$helper = new HELPER;

$html = '';

$vol = $_POST['vol'];

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols_coms';
$coms = $wpdb->get_results( 'SELECT *  FROM '.$table_name.' WHERE vol = "'.$vol.'" ORDER BY date DESC', OBJECT );

foreach ($coms as $com):

	$user = $helper->infos_profil($com->pilote);
	$html .= '<li class="list-group-item">';
		$html .= '<span class="pull-left"><strong>'.$user->prenom.' '. $user->nom .'</strong></span>';
		$html .= '<span class="pull-right"><strong>'.date('d-m-Y H:i:s', strtotime($com->date)).'</strong></span>';
		$html .= '<div class="clear"></div><br />';
		$html .= '<span class="pull-left">'.$com->message.'</span>';
		$html .= '<div class="clear"></div>';
	$html .= '</li>';

endforeach;

$return = array(
    'html' => $html
);

echo json_encode($return);
exit();

?>