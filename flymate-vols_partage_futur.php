<?php
global $wpdb;
$helper = new HELPER;
$user = $helper->user();

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

if($_POST['lat'] && $_POST['long']):

	if($_POST['Rayon'] == 25):
		$rayon = 0.22;
	endif;

	if($_POST['Rayon'] == 50):
		$rayon = 0.45;
	endif;

	if($_POST['Rayon'] == 100):
		$rayon = 1;
	endif;

	if($_POST['Rayon'] == 150):
		$rayon = 1.36;
	endif;

	$lat  = $_POST['lat'];
	$long = $_POST['long'];

	/********************************************/

	$LatMoins = $lat - $rayon;
	$LatPlus = $lat + $rayon;
	$LongMoins = $long - $rayon;
	$LongPlus = $long + $rayon;

	$geolocalisation = ' AND aer.latdec >= "'.$LatMoins.'" AND aer.latdec <= "'.$LatPlus.'" ';

else:

	$geolocalisation = '';

endif;

/**/

if($_POST['search']):

	$search = ' AND (vol.title LIKE "%'.$_POST['search'].'%" 
				 OR vol.aeronef LIKE "%'.$_POST['search'].'%" 
				 OR vol.details LIKE "%'.$_POST['search'].'%" 
				 OR vol.attente LIKE "%'.$_POST['search'].'%"
				 OR vol.escale LIKE "%'.$_POST['search'].'%"
				 OR aer.OACI LIKE "%'.$_POST['search'].'%"
				 OR aer.nom LIKE "%'.$_POST['search'].'%"
				 OR aer.ville LIKE "%'.$_POST['search'].'%")';


else:

	$search = '';

endif;

/***/

if($_POST['contenu']):

	if($_POST['contenu'] == 1):

		$aerodrome = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix . 'fly_aeroclubs WHERE id = "'.$user->aerodrome.'" ', OBJECT);
		
		$contenu = ' AND aer.OACI = "'.$aerodrome->OACI.'"';

	endif;

	if($_POST['contenu'] == 2):

		$contenu = ' AND (vol.depart = "'.$user->aerodrome.'" OR vol.arriver = "'.$user->aerodrome.'")';

	endif;

else:

	$contenu = '';

endif;

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols as vol';
$vols = $wpdb->get_results( 'SELECT DISTINCT vol.* FROM '.$table_name.' 
							LEFT JOIN '.$wpdb->prefix . 'fly_ad_france as aer ON aer.id = vol.depart OR aer.id = vol.arriver
							WHERE vol.date >= "'.date('Y-m-d').'" 
							'.$geolocalisation . $search . $contenu . '
							ORDER BY vol.date ASC', OBJECT );





?>

<div class="row" id="vols">	

	<div class="clear"></div>

	<div class="col-md-12">

		<h4 class="color-white pull-left">Rechercher :</h4><br /><br />

		<form method="POST" class="form-inline thumbnail" id="formSearch" action="">

			<div class="form-group">
				<button type="button" class="btn btn-success text-uppercase" id="clickGeo">Géolocalisez moi</button>
			</div>

			<input type="hidden" name="lat" id="lat">
			<input type="hidden" name="long" id="long">

			<div class="form-group">
		    	<select name="Rayon" id="Rayon" class="form-control chosen select width100p contenuSelect">
		    		<option value="">Rayon</option>
		    		<option value="25" <?php if($_POST['Rayon'] == 25): echo 'selected'; endif; ?> >25km</option>
		    		<option value="50" <?php if($_POST['Rayon'] == 50): echo 'selected'; endif; ?> >50km</option>
		    		<option value="100" <?php if($_POST['Rayon'] == 100): echo 'selected'; endif; ?> >100km</option>
		    		<option value="150" <?php if($_POST['Rayon'] == 150): echo 'selected'; endif; ?> >150km</option>
		    	</select>
		    </div>

			<div class="form-group padding5">
				<h4 class="noMargin">OU</h4>
			</div>
		  	<div class="form-group width200">
		  		<div class="input-group">
  					<span style="border-right: 0px !important;" class="input-group-addon backWhite" id="basic-addon1"><i class="icon-search"></i></span>
		    		<input style="border-left: 0px !important;" type="text" class="form-control width100p" name="search" id="rechercher" placeholder="Rechercher" value="<?php echo $_POST['search']; ?>">
		    	</div>
		  	</div>

		  	<?php if($user): ?>

			  	<div class="form-group width200">
			    	<select name="contenu" id="contenu" class="form-control chosen select width100p contenuSelect">
			    		<option value="">Sélectionner</option>
			    		<option value="1" <?php if($_POST['contenu'] == 1): echo 'selected'; endif; ?> >Uniquement les vols de mon aéroclub</option>
			    		<option value="2" <?php if($_POST['contenu'] == 2): echo 'selected'; endif; ?> >Uniquement les vols de mon aérodrome</option>
			    	</select>
			  	</div>

			  <?php endif; ?>

		  	<div class="form-group">
		  		<button type="submit" class="btn btn-danger btn-red text-uppercase">Chercher</button>
			</div>
		</form>

	</div>

	<div class="clear"></div><br /><br />

<?php

$html = '';

if(empty($vols)):

	$html .= '<div class="col-md-12">';

		$html .= '<h3 class="pull-left">Nos vols en cours</h3>';

		if($helper->session()):

			global $wpdb;
			$table_name = $wpdb->prefix . 'fly_pilotes';
			$user = $wpdb->get_row( 'SELECT profil FROM '.$table_name.' WHERE id = "'.$helper->session().'" ', OBJECT );

			if($user->profil != 0):
				$html .= '<a href="/?page_id=104&page=addVol" class="pull-right btn btn-warning yellow-btn">Partager un vol</a>';
			endif;

		endif;

		$html .= '<br />';

	$html .= '</div>';

	$html .= '<div class="clear"></div><br />';

	$html .= '<p class="alert alert-danger text-center">Aucun vol trouvé</p>';

else:

	$html .= '<div class="col-md-12">';

		$html .= '<h3 class="pull-left">Nos vols en cours</h3>';

		if($helper->session()):

			global $wpdb;
			$table_name = $wpdb->prefix . 'fly_pilotes';
			$user = $wpdb->get_row( 'SELECT profil FROM '.$table_name.' WHERE id = "'.$helper->session().'" ', OBJECT );

			if($user->profil != 0):
				$html .= '<a href="/?page_id=104&page=addVol" class="pull-right btn btn-warning yellow-btn">Partager un vol</a>';
			endif;

		endif;

		$html .= '<br />';

	$html .= '</div>';

	$html .= '<div class="clear"></div><br />';

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
			
			$html .= $helper->showNavigation($vol, $photo, $user);

		endforeach;

	$html .= '</ul>';

	$html .= '<div class="clear"></div>';
	$html .= '<div class="block-pagination">';
		$html .= '<ul class="pagination holder"></ul>';
	$html .= '</div>';

endif;

/**************/
/**************/
/**************/

if($_POST['lat'] && $_POST['long']):

	$lat  = $_POST['lat'];
	$long = $_POST['long'];

	/***/

	$LatMoins = $lat - 1;
	$LongPlus = $long + 1;

	$geolocalisation = ' AND dro.latdec >= "'.$LatMoins.'" AND dro.latdec <= "'.$LatPlus.'" ';

else:

	$geolocalisation = '';

endif;

/**/

if($_POST['search']):

	$search = ' AND (vol.title LIKE "%'.$_POST['search'].'%" 
				 OR vol.aeronef LIKE "%'.$_POST['search'].'%" 
				 OR vol.details LIKE "%'.$_POST['search'].'%" 
				 OR vol.attente LIKE "%'.$_POST['search'].'%"
				 OR vol.escale LIKE "%'.$_POST['search'].'%"
				 OR aer.OACI LIKE "%'.$_POST['search'].'%"
				 OR aer.nom LIKE "%'.$_POST['search'].'%"
				 OR aer.ville LIKE "%'.$_POST['search'].'%")';


else:

	$search = '';

endif;

/**/

if($_POST['contenu']):

	if($_POST['contenu'] == 1):

		$aerodrome = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix . 'fly_aeroclubs WHERE id = "'.$user->aerodrome.'" ', OBJECT);
		
		$contenu = ' AND aer.OACI = "'.$aerodrome->OACI.'"';

	endif;

	if($_POST['contenu'] == 2):

		$contenu = ' AND (vol.depart = "'.$user->aerodrome.'" OR vol.arriver = "'.$user->aerodrome.'")';

	endif;

else:

	$contenu = '';

endif;

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols as vol';
$vols = $wpdb->get_results( 'SELECT DISTINCT vol.* FROM '.$table_name.' 
							LEFT JOIN '.$wpdb->prefix . 'fly_ad_france as aer ON aer.id = vol.depart OR aer.id = vol.arriver
							WHERE vol.date < "'.date('Y-m-d').'" 
							'.$geolocalisation . $search . $contenu . '
							ORDER BY vol.date DESC', OBJECT );
$helper = new HELPER;
$user = $helper->user();

if(empty($vols)):

	$html .= '<h3><br /><br />Nos vols passé<br /></h3>';

	$html .= '<p class="alert alert-danger text-center">Aucun vol trouvé</p>';

else:

	$html .= '<h3><br /><br />Nos vols passés<br /></h3>';

	$html .= '<ul id="itemContainer2">';

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
			
			$html .= $helper->showNavigation($vol, $photo, $user);

		endforeach;

	$html .= '</ul>';

	$html .= '<div class="clear"></div>';
	$html .= '<div class="block-pagination">';
		$html .= '<ul class="pagination holder2"></ul>';
	$html .= '</div>';

endif;

$html .= '</div>';



/****/

$html .= '<div class="modal fade" id="horsLigne" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
	$html .= '<div class="modal-dialog">';
		$html .= '<div class="modal-content">';
		    $html .= '<div class="modal-body">';
		    $html .= '<h3 class="text-center">Connectez-vous pour consulter le vol proposé</h3><br />';
		    $html .= '<h5 class="text-center"><a href="/login/">Connexion / Inscription</a></h5>';
		    $html .= '</div>';
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

	#vols h3{
		color: white;
	}

	#formSearch{
		padding: 20px 50px !important;
	}

	.width300{
		width: 300px !important;
	}

	.width200{
		width: 200px !important;
	}
</style>

<link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script type="text/javascript">

function maPosition(position) {

  document.getElementById("lat").value =  position.coords.latitude;
  document.getElementById("long").value = position.coords.longitude;

  setTimeout(function(){  
  	document.getElementById("formSearch").submit();
  }, 200);
}

$(document).ready(function(){

	$('#clickGeo').click(function(){

		if(navigator.geolocation)
		  navigator.geolocation.getCurrentPosition(maPosition);
	});

	/**/

	$( "#rechercher" ).autocomplete({
    	source: function( request, response ) {
	        $.ajax({
	          url: "/wp-content/plugins/flymate/front/search/flymate-autocomplete.php",
	          dataType: "json",
	          data: {
	            term: request.term
	          },
	          success: function( data ) {
	            response( $.map( data.myData, function( item ) {

	            	console.log(item);

	                return {
	                    label: item,
	                    value: item
	                }
	            }));
	          }
	        });
	    },
	    minLength: 2
    });
});

</script>

<script>
	(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.3"; fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));
</script>

<?php if($helper->session()): ?>
<script>	
	jQuery('.entry-title').html('');
	jQuery('#content').css('margin-top', '240px');
	jQuery('#content .container').css('margin-top', '-210px');
</script>
<?php else: ?>
<script>	
	jQuery('.entry-title').html('');
	jQuery('#content').css('margin-top', '240px');
	jQuery('#content .container').css('margin-top', '-210px');
</script>
<?php endif; ?>