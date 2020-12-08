<?php
if(!class_exists('Lib')){
    require_once( ABSPATH . 'wp-content/plugins/wp_list_table/Lib.php');
}

class WPListTable extends Lib{
    
   

    public function prepare_items(){

        $orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "" ;
        $order   = isset($_GET['order'])   ? trim($_GET['order'])   : "" ;

        $search_term = isset($_POST['s'])  ? trim($_POST['s']) : "";

        $this->items = $this->wp_list_table_data($orderby,$order,$search_term);

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns,$hidden,$sortable);

    }
    //
    public function wp_list_table_data($orderby='' , $order='', $search_term=''){

        global $wpdb;
        
        // $args = array(
        //     'numberposts' => -1,
        //     'suppress_filters' => false
        // );
        // $all_posts2 = get_post($args);
        if( !empty($search_term) ){
            echo 'buscar';
            $all_posts = $wpdb->get_results(
               'SELECT * FROM ' . $wpdb->posts . " WHERE post_type = 'post' AND post_status = 'publish' AND (post_title LIKE '%$search_term%' OR post_content LIKE '%$search_term%')"
            );
        }else{
            echo 'no buscar';
            if($orderby=='title' && $order=='desc'){
                $all_posts = $wpdb->get_results(
                    'SELECT * FROM '. $wpdb->posts . " WHERE post_type = 'post' AND post_status='publish' ORDER BY post_title DESC"
                );
            }else{
                $all_posts = $wpdb->get_results(
                    'SELECT * FROM '. $wpdb->posts . " WHERE post_type = 'post' AND post_status='publish' ORDER BY post_title ASC"
                );
            }
       
        }
        
        
        $post_array = array();
       

        if( !empty($all_posts)  ){
            foreach( $all_posts as $key => $post ){ 
                $post_array[]= array(
                    'id' => $post->ID,
                    'title'=> $post->post_title,
                    //'content'=> $post->post_content,
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
            'title' => array('title',true)
        );

    }

    //
    public function get_columns(){
        $columns = array(
            'id' => 'ID',
            'title' => 'Title',
            // 'content' => 'Content',
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
    
    echo "<form method='post' name='frm_search_post' action='".$_SERVER['PHP_SELF']."?page=list-table' >";
    $wp_table->search_box('Search post','search_post_id');
    echo '</form>';
    $wp_table->display();

}

 show_data_wp_list_table();