<?php
/*
Plugin Name: Wp list table
Description: test online test
Author: Seo Contenidos
Version: 0.0.1
Author URI: https://seocontenidos.net/
License: GPLv2 or later
Text Domain: wpListTable
*/
add_action( 'init' , 'menu' );


function wpListTable(){
    add_menu_page(
        'List table',
        'List table',
        'manage_options',
        'list-table',
        'list_table_fn'
    );
}
function menu(){
    add_action( 'admin_menu' , 'wpListTable');
}
function list_table_fn(){
    
    include_once plugin_dir_path(__FILE__).'views/seo-wp-list-table.php';
    $template = ob_get_contents();
    
    echo $template;
}