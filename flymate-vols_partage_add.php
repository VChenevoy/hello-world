<?php

$helper = new HELPER;
$user = $helper->user();
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols';
$vol = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id= "'.$_GET['id'].'" AND id_pilote = "'.$user->id.'" ', OBJECT );

?>

<link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/theme-default.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/wp-content/plugins/flymate/css/bootstrap-datepicker.css">
<link rel="stylesheet" href="/wp-content/plugins/flymate/css/bootstrap-chosen.css">
<link rel="stylesheet" href="/wp-content/plugins/flymate/css/clockpicker.css">

<script src="<?php echo WP_PLUGIN_URL.'/flymate/js/jquery.form-validator.min.js'; ?>"></script>
<script src="<?php echo WP_PLUGIN_URL.'/flymate/js/security.js'; ?>"></script>
<script src="<?php echo WP_PLUGIN_URL.'/flymate/js/lang/fr.js'; ?>"></script>

<script src="<?php echo WP_PLUGIN_URL.'/flymate/js/bootstrap-datepicker.js'; ?>"></script>
<script src="<?php echo WP_PLUGIN_URL.'/flymate/js/bootstrap-datepicker.fr.min.js'; ?>"></script>

<script src="<?php echo WP_PLUGIN_URL.'/flymate/js/clockpicker.js'; ?>"></script>
<script>
jQuery(document).ready(function(){
  	jQuery.validate({
  		modules : 'security',
	  	lang : 'fr'
	});

	/**/

	jQuery('.clockpicker').clockpicker({
		donetext: 'Valider'
	});

	/**/

	jQuery('#datetimepicker1 input').datepicker({
	    format: "dd-mm-yyyy",
      language: 'fr',
      orientation: 'bottom',
	    todayHighlight: true,
	    autoclose: true
	});
});
</script>


<div class="clear"></div>

<div class="contenu">

  <form id="form-flymate" method="POST" class="form-horizontal" role="form">
    
    <input type="hidden" name="id" value="<?php echo $vol->id; ?>">
    <input type="hidden" name="id_pilote" value="<?php echo $user->id; ?>">

    <div class="form-group">

      <label for="nom" class="col-sm-3 text-left control-label">Date</label>
      <div class="col-sm-2">

      	<div id="datetimepicker1" class="input-group date">
  	      	<input type="text" name="date" class="form-control" value="<?php if(!empty($vol->date)): echo date('d-m-Y', strtotime($vol->date)); endif; ?>" data-validation-error-msg-container="#date-error-dialog" data-validation="required">
  	    	<span class="input-group-addon">
  	      		<i class="fa fa-calendar"></i>
  	    	</span>
  	    </div>
        <div id="date-error-dialog"></div>

  	</div>

      <!---->
      <label class="col-sm-2"></label>
      <!---->

      <label for="number" class="col-sm-3 text-left control-label">Nombre de Flymates recherchés</label>
      <div class="col-sm-2">

      	<select name="number" class="chosen" id="number" data-validation="required">
  	    	<?php for($i=1; $i<=10; $i++): ?>
            <?php if($vol->number == $i): ?>
  	    		 <option value="<?php echo $i; ?>" selected><?php echo $i; ?></option>
            <?php else: ?>
              <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endif; ?>
  	  		<?php endfor; ?>
  	  	</select>
        	<!--
        	<input type="text" name="number" id="number" class="form-control" value="<?php if(isset($_POST['number'])): echo $_POST['number']; endif; ?>" data-validation="required">
      	-->
      </div>

    </div>
    <div class="form-group">
      
      <label for="heure_down" class="col-sm-3 text-left control-label">Heure estimée de départ</label>
      <div class="col-sm-2">    	

        <div class="input-group clockpicker">
    		    <input type="text" name="heure_down" id="heure_down" value="<?php if(!empty($vol->heure_down)): echo $vol->heure_down; endif; ?>" class="form-control" data-validation-error-msg-container="#depart-error-dialog" data-validation="required">
    		    <span class="input-group-addon">
    		        <span class="fa fa-clock-o"></span>
    		    </span>
    		</div>

        <div id="depart-error-dialog"></div>

      </div>

      <!---->
      <label class="col-sm-2"></label>
      <!---->

      <label for="heure_up" class="col-sm-3 text-left control-label">Heure estimée d'arrivée</label>
      <div class="col-sm-2">

        <div class="input-group clockpicker">
    		    <input type="text" name="heure_up" id="heure_up" class="form-control" value="<?php if(!empty($vol->heure_up)): echo $vol->heure_up; endif; ?>" data-validation-error-msg-container="#arriver-error-dialog" data-validation="required">
    		    <span class="input-group-addon">
    		        <span class="fa fa-clock-o"></span>
    		    </span>
    		</div>

        <div id="arriver-error-dialog"></div>

      </div>

    </div>

    <div class="form-group">
      <label for="depart" class="col-sm-3 text-left control-label">Aérodrome de départ</label>
      <div class="col-sm-4">
         <select class="aeroclub chosen" name="depart" id="depart" data-validation="required">
            <option value="">Sélectionner un aérodrome</option>
            <?php echo $helper->showAerodromes($vol->depart); ?>
          </select>
      </div>
    </div>
    <div class="form-group">
      <label for="arriver" class="col-sm-3 text-left control-label">Aérodrome d'arrivée</label>
      <div class="col-sm-4">
        <select class="aeroclub chosen" name="arriver" id="arriver" data-validation="required">
            <option value="">Sélectionner un aérodrome</option>
            <?php echo $helper->showAerodromes($vol->arriver); ?>
          </select>
      </div>
    </div>
    <div class="form-group">
      <label for="title" class="col-sm-3 text-left control-label">Titre du vol</label>
      <div class="col-sm-5">
        <input type="text" name="title" value="<?php if(!empty($vol->title)): echo $vol->title; endif; ?>" class="form-control" id="title" data-validation="required">
      </div>
    </div>

    <div class="form-group">
      <label for="rotation" class="col-sm-3 text-left control-label">Rotation du pilote prévue</label>
      <div class="col-sm-4">
        <input type="text" name="rotation" value="<?php if(!empty($vol->escale)): echo $vol->escale; endif; ?>" class="form-control" id="rotation" data-validation="required">
      </div>
    </div>

    <div class="form-group">
      <label for="aeronef" class="col-sm-3 text-left control-label">Aéronef</label>
      <div class="col-sm-4">
        <input type="text" name="aeronef" value="<?php if(!empty($vol->aeronef)): echo $vol->aeronef; endif; ?>" class="form-control" id="aeronef" data-validation="required">
      </div>
    </div>

    <div class="form-group">
      <label for="attente" class="col-sm-12 text-left control-label">Ce que vous attendez de votre Flymate</label>
      <div class="col-sm-12">
        <br />
        <textarea name="attente" class="form-control" id="attente" data-validation="required"><?php if(!empty($vol->attente)): echo addslashes($vol->attente); endif; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="description" class="col-sm-12 text-left control-label">Description du vol (longue)</label>
      <div class="col-sm-12">
        <br />
        <textarea name="description" class="form-control" id="description" data-validation="required"><?php if(!empty($vol->details)): echo addslashes($vol->details); endif; ?></textarea>
      </div>
    </div>  

    <!--<div class="form-group">
      <label for="conf" class="col-sm-3 text-left control-label">Confidentialité</label>
      <div class="col-sm-8">      
  		  <input type="text" name="conf" class="form-control" id="conf">
      </div>
    </div>-->

    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button type="submit" name="submit" class="btn btn-success btn-lg"><?php if($_GET['id']): echo 'Modifier mon vol'; else: echo 'Partager mon vol'; endif; ?></button>
      </div>
    </div>
  </form>
</div>

<?php echo $html; ?>

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
  jQuery('.entry-header').html('<div class="col-md-1"><a href="/?page_id=104" data-toggle="tooltip" data-placement="top" title="retour"><i class="return fa fa-chevron-left"></i></a></div><div class="col-md-11 entry-title">Pour déposer un projet de vol, remplissez le formulaire et validez pour qu\'il apparaisse dans les vols partagés</div><div class="clear"></div><br /><br />');
  jQuery('#content').css('margin-top', '180px');
  jQuery('#content .container').css('margin-top', '-180px')
</script>

 