<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}


function create_initial_theme_page() {
   // El slug de la página que quieres crear
   $page_slug = 'oficina';

   // Verifica si la página ya existe
   $existing_page = get_page_by_path($page_slug);

   // Si la página no existe, la crea
   if (is_null($existing_page)) {
       $page_data = array(
           'post_title'     => 'Mi Página Especial', // El título de la página
           'post_name'      => $page_slug,           // El slug de la página
           'post_content'   => 'Contenido inicial de Mi Página Especial.', // El contenido de la página
           'post_status'    => 'publish',            // El estado de la publicación
           'post_author'    => 1,                    // El ID del autor (usualmente 1 para el admin)
           'post_type'      => 'page',               // Tipo de post
           'comment_status' => 'closed',             // Estado de comentarios
           'ping_status'    => 'closed',             // Estado de pings
       );

       // Inserta la página en la base de datos
       $new_page_id = wp_insert_post($page_data);
   }
}

// Añadir la función al hook 'after_switch_theme' para que se ejecute cuando se active el tema
add_action('after_switch_theme', 'create_initial_theme_page');