<?php

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols';
$vols = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE date < "'.date('Y-m-d').'" ORDER BY id DESC', OBJECT );

$helper = new HELPER;
$user = $helper->user();

if(!$helper->session()):

	echo '<br /><br />';

endif;

$html = '<div class="row" id="vols">';

	if($helper->session()):

	else:
		$html .= '<p></p>';
	endif;

	$html .= '<ul id="itemContainer">';

		foreach($vols as $vol):

			$pilote = $helper->showPilote($vol->id_pilote);

			$table_name = $wpdb->prefix . 'fly_aeroclubs';
			$aeroclub = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$pilote->aeroclub.'" ', OBJECT );

			if(!empty($pilote->aeroclub_custom)):
				$aero = substr($pilote->aeroclub_custom, 0, 35);
			else:
				$aero = substr($aeroclub->nom, 0, 35);
			endif;

			if(!empty($pilote->naissance) AND $pilote->naissance != '0000-00-00' AND $pilote->naissance != '1970-01-01'):
				$date = date('d/m/Y', strtotime($pilote->naissance));
				$naissance = ', '.age($date).' ans';
			else:
				$naissance = '';
			endif;
			
			$html .= '<li>';
				$html .= '<div class="col-sm-12 col-md-10 noPaddingCell floatNone margin0auto">';

					$html .= '<div class="display-table">';

						if(!empty($vol->aeronef)):
							$html .= '<div class="col-sm-12 col-md-5 display-cell noPadding text-left SmallInfo">';
				        		$html .= '<span><i class="fa fa-plane"></i> '.$vol->aeronef.'</span>';
				        	$html .= '</div>';
			        	endif;

			        	$html .= '<div class="col-sm-12 col-md-8 display-cell noPadding text-right SmallInfo">';
			        		$html .= '<span><i class="fa fa-calendar"></i> '.strftime("%A %d %B",strtotime($vol->date)).' '.strftime("%Hh %M",strtotime($vol->heure_down)).'-'.strftime("%Hh %M",strtotime($vol->heure_up)).'</span>';
			        	$html .= '</div>';			        	

			        $html .= '</div>';

			    	$html .= '<div class="col-sm-12 col-md-12 noPaddingCell thumbnail">';
				      	$html .= '<div class="caption">';

				      		$html .= '<h1 class="back_ocre text-center text-uppercase padding10-0">';
				      			if($helper->session()):
				      				$html .= '<a class="color-black" href="/?page_id=104&page=viewVol&id='.$vol->id.'">'.$vol->title.'</a>';
				      			else:
				      				$html .= '<a class="color-black" href="" data-target="#horsLigne" data-toggle="modal">'.$vol->title.'</a>';
				      			endif;
				      		$html .= '</h1>';


				      		$table_name = $wpdb->prefix . 'fly_ad_france';
							$depart = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$vol->depart.'" ', OBJECT );
							$arriver = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$vol->arriver.'" ', OBJECT );

					      		$html .= '<div class="col-md-5 noPadding margin20-0">';
					        		$html .= '<h3 class="text-uppercase text-center">'.substr($depart->nom, 0, 25).' ('.$depart->OACI.')</h3>';
					        	$html .= '</div>';

					        	$html .= '<div class="col-md-2 noPadding margin20-0">';
					        		$html .= '<h3 class="text-uppercase text-center">-</h3>';
					        	$html .= '</div>';


					        	$html .= '<div class="col-md-5 noPadding margin20-0">';
					        		$html .= '<h3 class="text-uppercase text-center">'.substr($arriver->nom, 0, 25).' ('.$arriver->OACI.')</h3>';
					        	$html .= '</div>';

					        	$html .= '<div class="clear"></div>';

					        	$html .= '<p class="padding0-20">';

					        		$html .= str_replace('\n','<br />', $vol->details);

					        		$html .= '<br /><br />';

					        		$html .= '<p class="text-center">';

						        		if($helper->session()):
						      				$html .= '<a class="btn btn-danger marginRight5" href="/?page_id=104&page=viewVol&id='.$vol->id.'">Voir plus de détails</a>';
						      				if($vol->id_pilote == $user->id):
						      					$html .= '<a class="btn btn-warning marginRight5" href="/?page_id=104&page=addVol&id='.$vol->id.'">Modifier</a>';
						      					$html .= '<a class="btn btn-danger" href="/?page_id=104&page=dell&id='.$vol->id.'">Supprimer</a>';
						      				endif;
						      			else:
						      				$html .= '<a class="btn btn-danger" href="" data-target="#horsLigne" data-toggle="modal">Voir plus de détails</a>';
						      			endif;

					      			$html .= '</p>';

					        	$html .= '</p>';

				        $html .= '</div>';

				        /**********/

				        

					        $html .= '<div class="borderTop Triangle back_silver">';

					        	$html .= '<div class="clear marginBottom10"></div>';

					        	$html .= '<div class="col-sm-1 col-md-1">';

					        	if(file_exists($_SERVER['DOCUMENT_ROOT'].$pilote->photo) && !empty($pilote->photo)):
									$photo = $pilote->photo;
								else:
									$photo = '/wp-content/plugins/flymate/images/avatar.png';
								endif;

					      			$html .= '<img src="'.$photo.'" alt="..." class="lineHeight20 img-circle img-responsive">';
					      		$html .= '</div>';

					      		$html .= '<div class="col-sm-6 col-md-6 padding0-20">';
						      		
						      		$html .= '<a href="#" data-target="#Pilote_'.$pilote->id.'" data-toggle="modal" class="text-muted"><h3 class="text-left">';

						      		if($helper->session()):						      			
						      			$html .= ''.$pilote->prenom.' '.$pilote->nom.'<br />';
						      		else:
						      			$html .= ''.$pilote->prenom.'.'.substr($pilote->nom, 0, 1).'<br />';
						      		endif;

						      		$html .= '<small>'.$aero.'</small>';
						      		$html .= '</h3></a>';	      		
						      	$html .= '</div>';
						      	
						      	if($helper->session()):
							      	$html .= '<div class="col-sm-5 col-md-5 text-right paddingleft0 marginBottom10 actionPiloteVol">';
							      		$html .= $helper->friend($pilote->id, $vol->id);
						      			$html .= '<a target="blank" href="/?page_id=201&tab=add&id='.$pilote->id.'" class="btn btn-default"><span class="fa fa-envelope"></span> Envoyer un message</a>';
							      	$html .= '</div>';
							    endif;

						      	$html .= '<div class="clear paddingBottom10"></div>';

					      	$html .= '</div>';	      	

			      	$html .= '</div>';
			  $html .= '</div>';

			  $html .= '<div class="modal fade" id="Pilote_'.$pilote->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				  	<div class="modal-dialog">
					    <div class="modal-content">
					      	<div class="modal-header">';

					      		if($helper->session()):			      			
					      			$html .= '<h4 class="modal-title text-capitalize">'.$pilote->prenom.' '.$pilote->nom.''.$naissance.'</h4>';
					      		else:
					      			$html .= '<h4 class="modal-title text-capitalize">'.$pilote->prenom.'.'.substr($pilote->nom, 0, 1).'</h4>';
					      		endif;
					      		
					      	$html .= '</div>';

					      	if($helper->session()):		

					      	if($pilote->heure_total == '0'): $heure_total = ''; else: $heure_total = $pilote->heure_total.' heure(s)'; endif;
				      		if($pilote->heure_commandant == '0'): $heure_commandant = ''; else: $heure_commandant = $pilote->heure_commandant.' heure(s)'; endif;						      		
					      		
					      		$html .= '<div class="modal-body">
					      			<div class="col-md-4 text-center padding10-0">
							      	<img src="'.$photo.'" alt="..." class="img-thumbnail">
							    </div>
						      	<div class="col-md-8 caption padding5-0">
							        <p class="text-left">
							        	Aéroclub : '.$aero.'<br />
							        	Licence(s) : '.$pilote->licence.'</br />
							        	Aéronef(s) piloté(s) : '.$pilote->aeronef.'</br /></br />
							        	Heures de vol totales en CdB : '.$heure_total.' heure(s)<br />
							        	Heures de vol CdB dans les 12 derniers mois : '.$heure_commandant.' heure(s)<br />
							        </p>
						      	</div>

						      	<div class="clear"></div><br />
						      	Description personnelle :<br />

						      	'.str_replace('\n','<br />', $pilote->description).'

						      	</div>
						      	<div class="modal-footer">
						      		'.$helper->friend($pilote->id, $pilote->id).'
						      		<a target="blank" href="/?page_id=201&tab=add&id='.$pilote->id.'" class="btn btn-default text-center" role="button"><span class="fa fa-envelope"></span> Envoyer message</a>
						      		<a href="#" type="button" role="button" class="btn btn-danger" data-dismiss="modal">Fermer</a>
						    	</div>';
						    else:

						    	$html .= '<div class="modal-body"><br /><h3>Connectez vous pour consulter le profil souhaité</h3></div>';

						    endif;

					    $html .= '</div>
				  	</div>
				</div>';

			  $html .= '<div class="modal fade" id="PiloteFriend_'.$vol->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
					$html .= '<div class="modal-dialog">';
						$html .= '<div class="modal-content">';
							$html .= '<form method="POST" class="AddFriend" action="'.WP_PLUGIN_URL.'/flymate/friends.php?type=add">';
							    $html .= '<div class="modal-header"><h4 class="modal-title">Demander en ami ?</h4></div>';
							    $html .= '<div class="modal-body">';
							    	$html .= '<p class="alert alert-warning"><i class="fa fa-exclamation"></i> Demander les pilotes en amis permet d\'être notifié de leurs vols déposés</p>';
							    	$html .= '<div class="table">';
								      	$html .= '<div class="col-md-4 text-center text-capitalize">';
									        $html .= '<img src="'.$photo.'" alt="..." class="img-thumbnail"><br />';
									        $html .= $pilote->prenom.' '.$pilote->nom;
									        $html .= '<input type="hidden" name="to" value="'.$pilote->id.'">';
								        	$html .= '</div>';
								        	$html .= '<div class="col-md-4 text-center">';
								        		$html .= '<br /><br /><i class="fa fa-exchange font3em color-silver"></i><br /><br />';
								        	$html .= '</div>';
								        	$html .= '<div class="col-md-4 text-center text-capitalize">';							        	

									        if(file_exists($_SERVER['DOCUMENT_ROOT'].$user->photo)):
												$photoUser = $user->photo;
											else:
												$photoUser = '/wp-content/plugins/flymate/images/avatar.png';
											endif;

											$html .= '<img src="'.$photoUser.'" alt="..." class="img-thumbnail"><br />';
									        $html .= $user->prenom.' '.$user->nom;
									        	$html .= '<input type="hidden" name="from" value="'.$user->id.'">';
								        	$html .= '</div>';
								        	$html .= '<div class="clear"></div>';
								      	$html .= '</div>';
								      	$html .= '<div class="modal-footer">';
								      		$html .= '<button type="submit" class="btn btn-warning yellow-btn">Confirmer</button>';
									        $html .= '<a href="#" type="button" role="button" class="btn btn-danger" data-dismiss="modal">Fermer</a>';								        
								      	$html .= '</div>';
								    $html .= '</div>';
							    $html .= '</div>';
							$html .= '</form>';
					  	$html .= '</div>';
				$html .= '</div>';

				$html .= '<div class="clear"></div>';

		  $html .= '</li>';

		endforeach;

	$html .= '</ul>';

$html .= '</div>';

$html .= '<div class="clear"></div>';
$html .= '<div class="block-pagination">';
	$html .= '<ul class="pagination holder"></ul>';
$html .= '</div>';

/****/

$html .= '<div class="modal fade" id="horsLigne" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
	$html .= '<div class="modal-dialog">';
		$html .= '<div class="modal-content">';
		    $html .= '<div class="modal-body"><h3 class="text-center">Connectez-vous pour consulter le vol proposé</h3></div>';
	  	$html .= '</div>';
$html .= '</div>';

echo $html;

?>

<style>
	.entry-header{
		text-align: center;		
	}

	.entry-header .entry-title{
		color: white;
		line-height: 35px;
		font-size: 30px;
	}

	.entry-title::before{
		content: none;
	}
</style>

<script>
	(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.3"; fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));
</script>

<?php if($helper->session()): ?>
<script>	
	jQuery('.entry-title').html('Partagez vos vols avec d\'autres pilotes privés<br /><br />');
	jQuery('#content').css('margin-top', '240px');
	jQuery('#content .container').css('margin-top', '-210px');
</script>
<?php else: ?>
<script>	
	jQuery('.entry-title').html('Partagez vos vols avec d\'autres pilotes privés<br /><br />Connectez-vous pour partager votre passion');
	jQuery('#content').css('margin-top', '240px');
	jQuery('#content .container').css('margin-top', '-210px');
</script>
<?php endif; ?>