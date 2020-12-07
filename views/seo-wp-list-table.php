<?php
if(!class_exists('Lib')){
    require_once( ABSPATH . 'wp-content/plugins/wp-list-table/Lib.php');
}

class WPListTable extends Lib{
    
   

    public function prepare_items(){

        $orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "" ;
        $order   = isset($_GET['order'])   ? trim($_GET['order'])   : "" ;

        $this->items = $this->wp_list_table_data($orderby,$order);

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns,$hidden,$sortable);

    }
    //
    public function wp_list_table_data($orderby='' , $order=''){

        global $wpdb;
        
        $args = array(
            'numberposts' => -1,
            'suppress_filters' => false
        );
        // $all_posts = get_post($args);
        $all_posts = $wpdb->get_results(
            'SELECT * FROM '. $wpdb->posts
        );

        
        $post_array = array();
       

        if( !empty($all_posts)  ){
            foreach( $all_posts as $key => $post ){ 
                $post_array[]= array(
                    'id' => $post->ID,
                    'title'=> $post->post_title,
                    'content'=> $post->post_content,
                    'slug'=> $post->post_name
                );
            }
        }
        return $post_array;
    }

    public function get_hidden_columns(){

        return array();

    }
    public function get_sortable_columns(){
        
        return array(
            
        );

    }

    //
    public function get_columns(){
        $columns = array(
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'Post Slug',
        );

        return $columns;
        
    }
    public function column_default($item,$column_name){
        switch($column_name){
            case 'id':
            case 'title':
            case 'content':
            case 'slug':
                return $item[$column_name];
            default:
                return print_r($item , true);
        }
    }
   
}

function show_data_wp_list_table(){
    $wp_table = new WPListTable();

    $wp_table->prepare_items();
    echo  '<h3>Table list</h3>' ;
    $wp_table->display();

}

 show_data_wp_list_table();