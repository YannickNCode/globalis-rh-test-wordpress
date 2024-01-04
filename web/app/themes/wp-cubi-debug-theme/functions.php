<?php

require_once __DIR__ . '/src/schema.php';
require_once __DIR__ . '/src/registrations.php';

function redirect_custom_post_type_to_404() {
   global $wp_query;

   if ( isset( $wp_query->query['post_type'] ) && 'registrations' === $wp_query->query['post_type'] ) {
       status_header(404);

       // TODO : Create a new 404 page
       //  include( get_template_directory() . '/404.php' );
       exit;
   }
}
add_action( 'template_redirect', 'redirect_custom_post_type_to_404' );

