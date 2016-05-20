<?php
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols';
$vol = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id="'.$_GET['id'].'" ', OBJECT );

$helper = new HELPER;
$user = $helper->user();

/***************/

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vol_reserve';
$countPilotes = $wpdb->get_row( 'SELECT COUNT(*) as count FROM '.$table_name.' WHERE id_vol="'.$vol->id.'"', OBJECT );

/**********/
global $wpdb;
$table_name = $wpdb->prefix . 'fly_vol_reserve';
$pilotes_reserve = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id_vol="'.$vol->id.'" AND id_pilote="'.$helper->session().'" ', OBJECT );

$countPilote = $wpdb->get_row( 'SELECT COUNT(*) as count FROM '.$table_name.' WHERE id_vol="'.$vol->id.'" ', OBJECT );

/**********/

$html = '';

$heure_down = explode(':', $vol->heure_down);
$heure_up = explode(':', $vol->heure_up);

$count = $vol->number - $countPilote->count;

$html .= '<div class="clear"></div><br />';

/***/

$table_name = $wpdb->prefix . 'fly_ad_france';
$depart = $wpdb->get_row( 'SELECT nom FROM '.$table_name.' WHERE id="'.$vol->depart.'" ', OBJECT );
$arriver = $wpdb->get_row( 'SELECT nom FROM '.$table_name.' WHERE id="'.$vol->arriver.'" ', OBJECT );

/******/

$html .= '<input type="hidden" name="vol" value="'.$_GET['id'].'">';

$html .= '<div id="view_vol">';

$html .= '<div class="col-md-2 noPadding height40"></div>';
$html .= '<div class="col-md-3 noPadding height40 borderBottom1"><h3 class="white-text text-center">'.$depart->nom.'</h3></div>';
$html .= '<div class="col-md-2 noPadding height40 text-center"><i class="fa fa-plane white-text font4em"></i><br /><span class="color533847">'.strftime('%e %B %G', strtotime($vol->date)).'</span></div>';
$html .= '<div class="col-md-3 noPadding height40 borderBottom1"><h3 class="white-text text-center">'.$arriver->nom.'</h3></div>';
$html .= '<div class="col-md-2 noPadding height40"></div>';

$html .= '<div class="clear"></div><br /><br /><br /><br />';

$html .= '<div class="col-md-2 noPadding"></div>';
$html .= '<div class="col-md-2 noPadding"></div>';
$html .= '<div class="col-md-4 padding15-0 text-center borderTop1 borderBottom1"><h3 class="white-text">Escale: '.$vol->escale.'</h3></div>';
$html .= '<div class="col-md-2 noPadding"></div>';
$html .= '<div class="col-md-2 noPadding"></div>';

$html .= '<div class="clear"></div><br /><br />';

$html .= '<div class="col-md-2 noPadding"></div>';

$html .= '<div class="col-md-3 left">';
$html .= '<div class="col-md-4 text-center"><i class="fa fa-calendar font3em color533847"></i></div>';
$html .= '<div class="col-md-8 left"><span class="white-text">'.$heure_down[0].'H'.$heure_down[1].'<br />'.$heure_up[0].'H'.$heure_up[1].'</span></div>';
$html .= '</div>';

$html .= '<div class="col-md-2 text-center">';
$html .= '<div class="col-md-4 text-center"><i class="fa fa-users font3em color533847"></i></div>';
$html .= '<div class="col-md-8 left"><span class="color533847">PLACE<br /><span class="white-text">'.$count.'</span></div>';
$html .= '</div>';

$html .= '<div class="col-md-3 right">';
$html .= '<div class="col-md-8 right"><i class="fa fa-plane font3em color533847"></i></div>';
$html .= '<div class="col-md-4 left noPadding"><span class="color533847">AERONEF</span><br /><span class="white-text">'.$vol->aeronef.'</span></div>';
$html .= '</div>';

$html .= '<div class="col-md-2 noPadding"></div>';

$html .= '<div class="clear"></div><br /><br /><br />';

$html .= '<div class="contenu">';

$html .= '<div class="col-md-2 noPadding"></div>';
$html .= '<div class="col-md-8 noPadding">';
$html .= '<h4 class="font1-4em">Ce que vous attendez de votre Flymate</h4><hr />';
$html .= '<p>'.str_replace('\n','<br />', $vol->attente).'</p>';
/***/
$html .= '<br /><br />';
/***/
$html .= '<h4 class="font1-4em">Description</h4><hr />';
$html .= '<p>'.str_replace('\n','<br />', $vol->details).'</p>';
$html .= '</div>';
$html .= '<div class="col-md-2 noPadding"></div>';

$html .= '<div class="clear"></div>';

$html .= '<hr width="300px" />';

if($vol->id_pilote != $helper->session()):

	if($pilotes_reserve == NULL && $countPilotes->count < $count):
		$html .= '<br /><p class="text-center result"><button data-toggle="modal" data-target="#reservationPopup" class="btn btn-success btn-lg text-center">Rejoindre le vol</button></p>';
	elseif($countPilotes->count >= $vol->number):
		$html .= '<br /><p class="text-center result">Plus aucune place disponible</p>';
	elseif($pilotes_reserve->status == 0):
	    $html .= '<br /><p class="text-center alert alert-info">Votre réservation est réalisée, vous recevrez un email de confirmation quand l\'organisateur aura validé votre réservation.</p>';
	elseif($pilotes_reserve->status == 1):
		$html .= '<br /><p class="text-center alert alert-success">Votre réservation est validé</p>';
		$html .= '<p class="text-center"><button data-toggle="modal" data-target="#reservationAnnulerPopup" class="btn btn-danger text-center">Annuler ma réservation</button></p>';
	endif;

endif;

$html .= '</div>';

$html .= '</div>';


/***/
// POPUP Ajout message et confirm réservation

$html .= '<div class="modal fade" id="reservationPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
  	$html .= '<div class="modal-dialog" role="document">';
    	$html .= '<div class="modal-content">';
      		$html .= '<div class="modal-header">';        		
        		$html .= '<h4 class="modal-title" id="myModalLabel">Ma réservation</h4>';
        		$html .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        		$html .= '<div class="clear"></div>';
      		$html .= '</div>';
      		$html .= '<div class="modal-body">';

      			$html .= '<p>Voulez-vous envoyer un message au pilote ?</p>';

      			$html .= '<textarea class="textarea messagepilote" name="messagepilote"></textarea>';


      		$html .= '</div>';
	      	$html .= '<div class="modal-footer">';
	        	$html .= '<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>';
	        	$html .= '<button id="Reserve" data-loading-text="Réservation en cours..." autocomplete="off" class="btn btn-success text-center">Confirmer ma réservation</button>';
	      	$html .= '</div>';
    	$html .= '</div>';
  	$html .= '</div>';
$html .= '</div>';

/***/
// POPUP Annuler ma réservation

$html .= '<div class="modal fade" id="reservationAnnulerPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
  	$html .= '<div class="modal-dialog" role="document">';
    	$html .= '<div class="modal-content">';
      		$html .= '<div class="modal-header">';        		
        		$html .= '<h4 class="modal-title" id="myModalLabel">Annuler ma réservation</h4>';
        		$html .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        		$html .= '<div class="clear"></div>';
      		$html .= '</div>';

      		$html .= '<form method="POST" id="annulerResaForm">';

	      		$html .= '<div class="modal-body">';

	      			$html .= '<input type="hidden" name="reservation" value="'.$pilotes_reserve->id.'">';
	      			$html .= '<input type="hidden" name="vol" value="'.$vol->id.'">';
	      			$html .= '<input type="hidden" name="pilote" value="'.$user->id.'">';

	      			$html .= '<p>Pourquoi voulez-vous annuler votre réservation ?</p>';

	      			$html .= '<textarea required="required" class="textarea messagepilote" name="messageAnnulation"></textarea>';


	      		$html .= '</div>';
		      	$html .= '<div class="modal-footer">';
		        	$html .= '<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>';
		        	$html .= '<button type="submit" class="btn btn-danger text-center">Confirmer mon annulation</button>';
		      	$html .= '</div>';

		    $html .= '</form>';

    	$html .= '</div>';
  	$html .= '</div>';
$html .= '</div>';

echo $html;

?>

<style>
	.modal h4{
		float: left;
	}

	.container{
		min-height: auto !important;
	}

	article.hentry {
	    margin-bottom: 0px !important;
	    padding-bottom: 20px !important;
	    border-bottom: 0px !important;
	}

	.article.hentry{
		border: 0px !important;
		margin-bottom: 0px !important;
		padding-bottom: 0px !important;
	}

	.ami div, .ami a{
		width: 83.33333333% !important;
	}

	.ami .input-group{
		width: 100% !important;
	}
	.entry-header{
	    text-align: center;   
	  }

	  .entry-header .entry-title{
	    line-height: 35px;
	    font-size: 30px;
	  }

	  .entry-title::before{
	    content: none;
	  }

	  .entry-header .entry-title{
	  	color: #533847;
	  }

	  .color533847{
	  	color: #533847;
	  }
	  .BoxReserver .entry-title{
	    color: white !important;
	    line-height: 35px;
	    font-size: 30px;
	  }

	  .BoxReserver .entry-title::before{
	    content: none;
	  }

	  .boxComMessage{
	  	margin-bottom: 20px;
	  	max-height: 350px !important;
	  	overflow: auto;
	  }

	  .list-group-item{
	  	margin-bottom: 10px;
	  }
</style>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>

tinymce.init({ 
	selector:'textarea',
	language: 'fr_FR',
	language_url : '/wp-content/plugins/flymate/js/lang/fr_FR.js',
	setup: function(editor) {
	    editor.on('keyup', function(e) {
	      get_editor_textarea();
	    });
	  }
});

tinymce.init({ 
	mode : "specific_textareas",
	editor_selector: 'messageCom', 
	language: 'fr_FR',
	language_url : '/wp-content/plugins/flymate/js/lang/fr_FR.js',
	setup: function(editor) {
	    editor.on('keyup', function(e) {
	      get_editor_content();
	    });
	  }
});

function get_editor_content() {

	var message = tinyMCE.activeEditor.getContent();
	jQuery('.messageCom').html(message);	

}

function get_editor_textarea() {

	var message = tinyMCE.activeEditor.getContent();
	jQuery('.messagepilote').html(message);	

}

/**/

jQuery('#annulerResaForm').submit(function(event){

	event.preventDefault();

	var formData = jQuery(this).serialize();

	jQuery.ajax({
		method: 'POST',
      	dataType: 'json',
      	data: formData,
      	processData: true,
      	url: '/wp-content/plugins/flymate/front/vols/flymate-table_vol_reserve-pilotes_cancel.php',  
      	success: function(data){
      		jQuery('.result').html(data.html);
      		location.reload(); 
      	},
      	error: function(){
        	//alert('erreur');
      	}
	});
});

/**/

jQuery('#content').css('margin-top', '400px');
jQuery('#content .container').css('margin-top', '-350px');

  jQuery('.entry-header').html('<div class="text-center entry-title text-uppercase"><div class="absolutereturn"><a href="/?page_id=104" data-toggle="tooltip" data-placement="top" title="retour"><i class="return fa fa-chevron-left"></i></a></div> <?php echo $vol->title; ?></div>');

	jQuery('#Reserve').on('click', function () {

		var messagepilote = jQuery('.messagepilote').val();

		var btn = jQuery(this).button('loading');
	    jQuery.ajax({
			method: 'POST',
	      	dataType: 'json',
	      	data: {
	      		id_pilote  : "<?php echo $helper->session(); ?>",
	      		id_vol     : "<?php echo $vol->id; ?>",
	      		message    : messagepilote,
	      		status 	   : "0"
	      	},
	      	url: '/wp-content/plugins/flymate/front/vols/flymate-table_vol_reserve-pilotes_result.php',  
	      	success: function(data){
	      		jQuery('.result').html(data.html);
	        	showReserve();
			    btn.button('reset');
			    jQuery('#reservationPopup').modal('hide');
	      	},
	      	error: function(){
	        	//alert('erreur');
	      	}
		});
	});

	jQuery('#content').append('<div class="BoxReserver"><h1 class="entry-title text-center">ILS ONT DÉJÀ RÉSERVÉ</h1><br /><div class="container"><ul id="itemContainer"></ul></div><div class="clear"></div><div class="block-pagination"><ul class="pagination holder"></ul></div></div>');
	showReserve();

	/***/

	jQuery('.BoxReserver').append('<div id="Commentaires" class="BoxCom"><div class="container"><h2>COMMENTAIRES</h2> <div class="boxComMessage"></div><div class="boxComEdit"></div></div></div>');

	jQuery('.boxComEdit').html('<form id="formCom" method="POST" action="/wp-content/plugins/flymate/front/vols/flymate-coms_add.php"><input type="hidden" name="vol" value="<?php echo $_GET["id"]; ?>"><textarea class="messageCom" name="message"></textarea><br /><button type="submit" class="btn btn-danger SendCom" name="submit">Envoyer</button></form>');


	showCom();

	/**/

	/*jQuery('.SendCom').click(function(){

		if(jQuery('textarea[name="messageCom"]').val().length != 0){

			var vol = jQuery('input[name="vol"]').val();
			var message = jQuery('textarea[name="messageCom"]').val();

			jQuery.ajax({
				method: 'POST',
		      	dataType: 'json',
		      	data: {
		      		message:message,
		      		vol:vol,
		      	},
		      	url: '/wp-content/plugins/flymate/front/vols/flymate-coms_add.php',  
		      	success: function(data){
		      		showCom();
		      		tinyMCE.activeEditor.getContent(' ');
		      	}
			});

		}else{

			alert('Votre message est vide');

		}

	});*/

	/**/

	function showCom(){

	    var vol = jQuery('input[name="vol"]').val();

	    jQuery.ajax({
			method: 'POST',
	      	dataType: 'json',
	      	data: {
	      		vol:vol,
	      	},
	      	url: '/wp-content/plugins/flymate/front/vols/flymate-coms.php',  
	      	success: function(data){
	      		jQuery('.boxComMessage').html(data.html);
	      	}
		});

	}

	function get_editor_textareaDemandeur() {

	var message = tinyMCE.activeEditor.getContent();
	jQuery('.messageDemandeur').html(message);	

}


	function showReserve(){

		jQuery.ajax({
			method: 'POST',
	      	dataType: 'json',
	      	data: {
	      		pilote_vol : "<?php echo $vol->id_pilote; ?>",
	      		id_vol     : "<?php echo $vol->id; ?>",
	      		number 	   : "<?php echo $vol->number; ?>"
	      	},
	      	url: '/wp-content/plugins/flymate/front/vols/flymate-table_vol_reserve-pilotes.php',  
	      	success: function(data){
	        	jQuery('#itemContainer').html(data.html);

	        	tinymce.init({ 
					selector:'textarea',
					language: 'fr_FR',
					language_url : '/wp-content/plugins/flymate/js/lang/fr_FR.js',
					setup: function(editor) {
					    editor.on('keyup', function(e) {
					      get_editor_textareaDemandeur();
					    });
					  }
				});

	        	setTimeout(function(){
	        		editReserve();
	        	}, 200);	        	
	      	},
	      	error: function(){
	        	//alert('erreur');
	      	}
		});
	}

	/**/

	function editReserve(){

		$("ul.holder").jPages({
	      containerID  : "itemContainer",
	      perPage      : 3,
	      startPage    : 1,
	      previous    : "span.arrowPrev",
	      next        : "span.arrowNext",
	      startRange   : 1,
	      midRange     : 5,
	      endRange     : 1
	    });

	    /******/

	    jQuery('.AddFriend').submit(function(event){
	      event.preventDefault();

	      var from = jQuery(this).find('input[name=from]').val();
	      var to = jQuery(this).find('input[name=to]').val();
	      var status = 0;

	      jQuery.ajax({   
	        method: 'POST',
	        dataType: 'json', 
	        data: {
	          from: from,
	          to: to,
	          status: status
	        },
	        url: '/wp-content/plugins/flymate/front/pilotes/friends.php?type=add',  
	        success: function(data){
	          jQuery('.modal .result').html(data.html);
	          jQuery('.modal').modal('hide');
	          /**/
	          var select = jQuery('.aeroclub').val();
	          var search = jQuery('#pilotes .searchPilote').val();
	          showReserve();
	        },
	        error: function(){
	        }
	      });

	    });

	    jQuery('.AcceptFriend').click(function(){
	      var from = jQuery(this).data('from');
	      var to   = jQuery(this).data('to');

	      jQuery.ajax({   
	        method: 'POST',
	        dataType: 'json', 
	        data: {
	          from: from,
	          to: to
	        },
	        url: '/wp-content/plugins/flymate/front/pilotes/friends.php?type=accept',  
	        success: function(data){
	          alert(data.html);
	          /**/
	          var select = jQuery('.aeroclub').val();
	          var search = jQuery('#pilotes .searchPilote').val();
	          showReserve();
	        },
	        error: function(){
	          //alert('erreur');
	        }
	      });
	    });

	    /**/

	    jQuery('.RemoveFriend').click(function(){
	      var from = jQuery(this).data('from');
	      var to   = jQuery(this).data('to');

	      jQuery.ajax({   
	        method: 'POST',
	        dataType: 'json', 
	        data: {
	          from: from,
	          to: to
	        },
	        url: '/wp-content/plugins/flymate/front/pilotes/friends.php?type=remove',  
	        success: function(data){
	          alert(data.html);
	          /**/
	          var select = jQuery('.aeroclub').val();
	          var search = jQuery('#pilotes .searchPilote').val();
	          showReserve();
	        },
	        error: function(){
	          //alert('erreur');
	        }
	      });
	    });

	    /******/

	    jQuery('.ReponseReserve').submit(function(event){

	    	event.preventDefault();

	    	var formData = jQuery( this ).serialize();

	    	var btn = jQuery(this).button('loading');

	    	jQuery.ajax({
				method: 'POST',
		      	dataType: 'json',
		      	data: formData,
		      	processData: true,
		      	url: '/wp-content/plugins/flymate/front/vols/flymate-table_vol_reserve-pilotes_reponse.php',  
		      	success: function(data){
		      		jQuery('.result').html(data.html);
		        	showReserve();
				    btn.button('reset');
		      	},
		      	error: function(){
		        	//alert('erreur');
		      	}
			});
	    });

	    /******/

	    /*jQuery('#ReponseReserve').submit(function(event){

	    	event.preventDefault();

	    	var messageDemandeur = jQuery('.messageDemandeur').val();

	    	var btn = jQuery(this).button('loading');
	    	var vol = jQuery(this).data('vol');
	    	var pilote = jQuery(this).data('pilote');
	    	var reserve = jQuery(this).data('reserve');
	    	jQuery.ajax({
				method: 'POST',
		      	dataType: 'json',
		      	data: {
		      		vol 	 : vol,
		      		pilote 	 : pilote,
		      		reserve  : reserve,
		      		message  : messageDemandeur
		      	},
		      	url: '/wp-content/plugins/flymate/front/vols/flymate-table_vol_reserve-pilotes_refus.php',  
		      	success: function(data){
		      		jQuery('.result').html(data.html);
		        	showReserve();
				    btn.button('reset');
		      	},
		      	error: function(){
		        	//alert('erreur');
		      	}
			});
	    });*/

	    /***/	    
	}
</script>

<?php if( $_GET['com'] == 'valid'): ?>
<script>
	jQuery('#Commentaires').prepend('<div class="container"><p class="alert alert-success">Le commentaire à été ajouté</p></div>');
</script>
<?php endif; ?>