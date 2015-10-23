<?php defined('ABSPATH') or die('Try harder.');
/**
* Plugin Name: Portfolio katodico
* Plugin URI: http://katodia.com/portfolio/
* Description: Simple plugin to show every job this company has performed.
* Version: 1.0.0
* Author: Marcos Sánchez
* Author URI: http://katodia.com/author/msanchez
* License: GPL2
*/

add_action('wp_head', 'portfolio_style');

function portolio_style(){
    echo '<link rel="stylesheet" href="'.get_pluginpath().'/css/portfolio.css" type="text/css" />';
}

function proyectos_katodicos_options_install() {
   	global $wpdb;
  	$db = $wpdb->prefix . 'proyectos_katodicos_plugin';

 
	$sql = "CREATE TABLE " . $db . " (
	`id` mediumint(9) NOT NULL AUTO_INCREMENT,
	`nombre` mediumtext NOT NULL,
	`enlace` tinytext NOT NULL,
	`imagen` tinytext NOT NULL,
	`descripcion` tinytext NOT NULL,
	UNIQUE KEY id (id)
	);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
 
}

register_activation_hook(__FILE__,'proyectos_katodicos_options_install');

add_action('admin_action_proyectos-katodicos', '_handle_form_action'); 

function _handle_form_action(){
  global $wpdb;

  if (!isset($_POST['nombreproyecto']) or !isset($_POST['enlace']) or !isset($_POST['image_url'])){
  	wp_redirect( $_SERVER['HTTP_REFERER'] );
  	exit();
  }

  if (isset($_POST['descripcion'])) $descripcion = sanitize_text_field( $_POST['descripcion']);
  	else $descripcion = '';

  $nombre = sanitize_text_field( $_POST['nombreproyecto']);
  $imagen = sanitize_text_field( $_POST['image_url']);
  $enlace = sanitize_text_field( $_POST['enlace']);

  if (isset($_POST['id-proyecto'])){

  		
  		$id = sanitize_text_field( $_POST['id-proyecto']);

  		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix .
  		"proyectos_katodicos_plugin SET nombre='".$nombre.
  		"', imagen='".$imagen."', enlace='".$enlace."', descripcion='".$descripcion."' WHERE id=".$id));

  }

  else {
  $wpdb->insert( 
		$wpdb->prefix .'proyectos_katodicos_plugin', 
		array( 
			'nombre' => $nombre, 
			'enlace' => $enlace,
			'imagen' => $imagen,
			'descripcion' => $descripcion
		), 
		array( 
			'%s', 
			'%s',
			'%s',
			'%s' 
		) 
	);
 }

  wp_redirect( $_SERVER['HTTP_REFERER'] );
  exit();

}

function _listar_proyectos(){
	global $wpdb;
	$results = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'proyectos_katodicos_plugin', OBJECT );
	return $results;
}

function _borrar_proyecto ($id){
	global $wpdb;
	$wpdb->delete( $wpdb->prefix.'proyectos_katodicos_plugin', array( 'id' => $id ) );
}

function _get_proyecto($id){
	global $wpdb;
	$result = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'proyectos_katodicos_plugin WHERE id='.$id, OBJECT );
	return $result;
}

function _check_for_id(){
	if (isset ($_GET['action']) && isset ($_GET['id'])){

		$action = sanitize_text_field( $_GET['action'] );
		$id = sanitize_text_field( $_GET['id'] );
		unset ($_GET['action']);
		unset ($_GET['id']);
		
		if ($action == 'editar'){
			$q=_get_proyecto($id);
			return $q;
		}

		else return NULL;
	}
}

add_action( 'admin_menu', 'proyectos_katodicos_menu' );

function proyectos_katodicos_menu() {

	add_menu_page( 'Proyectos katodicos', 'Proyectos katodicos', 'edit_posts', 'proyectos-katodicos', 'proyectos_katodicos_options' );

}

function gallery (){


echo'
	<script type="text/javascript">
	jQuery(document).ready(function($){
	    $("#upload-btn").click(function(e) {
	        e.preventDefault();
	        var image = wp.media({ 
	            title: "Subir imagen",
	            // mutiple: true if you want to upload multiple files at once
	            multiple: false
	        }).open()
	        .on("select", function(e){
	            // This will return the selected image from the Media Uploader, the result is an object
	            var uploaded_image = image.state().get("selection").first();
	            // We convert uploaded_image to a JSON object to make accessing it easier
	            // Output to the console uploaded_image
	            console.log(uploaded_image);
	            var image_url = uploaded_image.toJSON().url;
	            // Lets assign the url value to the input field
	            $("#image_url").val(image_url);
	        });
	    });
	});
	</script>';
}


function proyectos_katodicos_options() {
	
	if (!current_user_can('edit_posts'))  {
		wp_die( __('No tiene suficientes permisos para acceder a esta p�gina.') );
	} 

	if (isset ($_GET['action']) && isset ($_GET['id'])){

		$action = sanitize_text_field( $_GET['action'] );
		$id = sanitize_text_field( $_GET['id'] );
		
		if ($action == 'borrar') _borrar_proyecto($id);
	}

		$q=_check_for_id();
		 // jQuery
		wp_enqueue_script('jquery');
		// This will enqueue the Media Uploader script
		wp_enqueue_media();

	?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
			    $("#upload-btn").click(function(e) {
			        e.preventDefault();
			        var image = wp.media({ 
			            title: "Subir imagen",
			            // mutiple: true if you want to upload multiple files at once
			            multiple: false
			        }).open()
			        .on("select", function(e){
			            // This will return the selected image from the Media Uploader, the result is an object
			            var uploaded_image = image.state().get("selection").first();
			            // We convert uploaded_image to a JSON object to make accessing it easier
			            // Output to the console uploaded_image
			            console.log(uploaded_image);
			            var image_url = uploaded_image.toJSON().url;
			            // Lets assign the url value to the input field
			            $("#image_url").val(image_url);
			        });
			    });
			});
		</script>
		<script>
			jQuery(document).ready(function($){
					$("#crear").click(function(e){
						jQuery('.crear-tab').show();
						jQuery('.listar-tab').hide();
					});
					$("#listar").click(function(e){
						jQuery('.crear-tab').hide();
						jQuery('.listar-tab').show();
						<?php $proyectos=_listar_proyectos() ?>;
					});
				});

			jQuery(document).ready(function($){
					$("#configreset").click(function(e){
						$( "[name='nombreproyecto']").val('');
						$( "[name='enlace']").val('');
						$( "[name='image_url']").val('');
						$( "[name='descripcion']").val('');
						$( "[name='id-proyecto']" ).remove();
					});
				});
		</script>

		<div class="wrap">
	        <h1>Proyectos katodicos</h1>
	        <h2 class="nav-tab-wrapper">
			    <a href="#" id="crear" class="nav-tab nav-tab-active">Crear</a>
			    <a href="#" id="listar" class="nav-tab">Listar</a>
			</h2>
			<div class="crear-tab">
		        <form name="proyecto-form" method="post" action="<?php echo admin_url( 'admin.php' ); ?>">
			        <?php
			        	if ($q!=NULL) echo '<input type="hidden" name="id-proyecto" value="'.$q[0]->id.'" />'; 
			        ?>
			        <input type="hidden" name="action" value="proyectos-katodicos" />
		        	<div>
		        		<label for="nombre_proyecto">Nombre de proyecto *</label>
		        		<input type="text" <?php if ($q!=NULL) echo 'value="'.$q[0]->nombre.'"'; ?> name="nombreproyecto">
		        	</div>
		        	<div>
		        		<label for="enlace_a_paginas">Enlace a paginas *</label>
		        		<input type="text" <?php if ($q!=NULL) echo 'value="'.$q[0]->enlace.'"'; ?> name="enlace">
		        	</div>
		        	<div>
					    <label for="image_url">Imagen asociada al proyecto *</label>
					    <input type="text" name="image_url" <?php if ($q!=NULL) echo 'value="'.$q[0]->imagen.'"'; ?> id="image_url" class="regular-text">
					    <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Subir imagen">
					</div>
					<div>
					    <label for="breve_descripcion">Breve descripcion</label>
					    <p>
					    <textarea rows="10" cols="60" name="descripcion">
					    	<?php if ($q!=NULL) echo $q[0]->descripcion; ?>
					    </textarea>
					    </p>
					</div>					
			        <p class="submit">
			        	<input type="button" class="button-primary" id="configreset" value="Nuevo Proyecto">
						<input type="submit" name="Guardar" class="button-primary" value="<?php esc_attr_e('Guardar cambios') ?>" />
					</p>
		        </form>
		    </div>
		    <div class="listar-tab" style="display:none">
		    	<?php 
		    		if (isset($proyectos)){
		    			$plugin_path = 'admin.php?page=proyectos-katodicos';
		    			echo'
		    			<table class="widefat">
						<thead>
						    <tr>
						        <th>Id</th>
						        <th>Nombre</th>       
						        <th>Imagen</th>
						        <th>Acciones</th>
						    </tr>
						</thead>
						<tfoot>
						    <tr>
						    <th>Id</th>
						    <th>Nombre</th>
						    <th>Imagen</th>
						    <th>Acciones</th>
						    </tr>
						</tfoot>
						<tbody>';
						foreach ($proyectos as $p){
							echo '
						   <tr>
						     <td>'.$p->id.'</td>
						     <td>'.$p->nombre.'</td>
						     <td><img src ="'.$p->imagen.'" width="50px"></td>
						     <td><a href="'.get_admin_url().$plugin_path.'&id='.$p->id.'&action=editar">Editar</a>|
						     	<a href="'.get_admin_url().$plugin_path.'&id='.$p->id.'&action=borrar">Borrar</a></td>
						   </tr>';
						};
						echo'
						</tbody>
						</table>
						';
		    		} 
		    	?>
		    </div>     
		</div>

<?php
	}

?>
