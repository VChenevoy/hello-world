<?php

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

global $wpdb;
$table_name = $wpdb->prefix . 'fly_vols';

$where = array(
    'id' => $_GET['id'],
);

$result = $wpdb->delete($table_name, $where);

if($result):
    echo '<div class="alert alert-success" role="alert">';
    echo 'Le vol est supprimé.';
    echo '</div>';
    echo '<script>setTimeout(function(){ location.href="/?page_id=104" }, 500);</script>';
else:
    echo '<div class="alert alert-danger" role="alert">';
    echo 'Erreur lors de la suppression du vol';
    echo '<br />';
    echo '</div>';      
endif;
?>