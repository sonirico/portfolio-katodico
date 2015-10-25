<?php defined('ABSPATH') or die('Try harder.');
/**
* Plugin Name: Portfolio katodico
* Plugin URI: http://github.com/sonirico/portfolio-katodico/
* Description: Simple plugin to show every job this company has performed.
* Version: 0.0.1
* Author: Marcos Sánchez
* Author URI: http://katodia.com/author/msanchez
* License: GPL2
*/


add_action('wp_head', 'portfolio_style');

function portolio_style(){
    echo '<link rel="stylesheet" href="'. get_stylesheet_directory_uri().'/css/portfolio.css" type="text/css" />';
}

function create_job_post_type(){
    register_post_type('trabajo',
        [
            'labels' => ['name' => __( 'Trabajos' ), 'singular_name' => __( 'Trabajo' )],
            'public' => true,
            'has_archive' => true,
            'supports' => [ 'title', 'editor','thumbnail', ],
        ]
    );
}

add_action( 'init', 'create_job_post_type' , 0);


function create_portfolio_taxonomies () {
    // Add tag-like taxonomy. That means, NOT HIERARCHICAL
    $labels = [
        'name' => _x('Tecnologías', 'taxonomy general name'),
        'singular_name' => _x('Tecnología', 'taxonomy singular name'),
        'search_items' => __('Busca Tecnologías'),
        'popular_items' => __('Tecnologías más usadas'),
        'all_items' => __('Todas las tecnologías'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Editar tecnología'),
        'update_item' => __('Actualizar tecnología'),
        'add_new_item' => __('Añade una nueva tecnología'),
        'new_item_name' => __('Nombre de nueva tecnología'),
        'separate_items_with_commas' => __('Tecnologías. Separadas por comas.'),
        'add_or_remove_items' => __('Añadir o quitar tecnologías'),
        'choose_from_most_used' => __('Elige de las tecnologías más usadas.'),
        'not_found' => __('No se han encontrado tecnologías.'),
        'menu_name' => __('Tecnologías'),
    ];
    $args = [
        'hierarchical' => FALSE,
        'labels' => $labels,
        'show_ui' => TRUE,
        'show_admin_column' => TRUE,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => TRUE,
        'rewrite' => array( 'slug' => 'tecnologia' ),
    ];
    
    register_taxonomy( 'tecnologia', 'trabajo', $args );
}

add_action( 'init', 'create_portfolio_taxonomies', 0 );