<?php
if(!class_exists('Lib')){
    require_once( ABSPATH . 'wp-content/plugins/wp-list-table/Lib.php');
}

class WPListTable extends Lib{
    public function prepare_items(){
        $orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "" ;
        $order   = isset($_GET['order'])   ? trim($_GET['order'])   : "" ;

        $search_term = isset($_POST['s'])  ? trim($_POST['s']) : "";
        $datas = $this->wp_list_table_data($orderby,$order,$search_term);
        
        $per_page = 4;
       

        $current_page = $this->get_pagenum();
        $total_items = count($datas);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $this->items = array_slice($datas , (($current_page - 1) * $per_page),$per_page);

        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns,$hidden,$sortable);

    }
    //
    public function wp_list_table_data($orderby='' , $order='', $search_term=''){

        global $wpdb;
 
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
            'action' => 'Action'
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
            case 'action':
                return '<a href="?page='.$_GET['page'].'&action=list-edit&post_id='.$item['id'].'" >Edit</a> | <a href="?page='.$_GET['page'].'&action=list-delete&post_id='.$item['id'].'"a>Delete</a>';
            default:
                 return print_r($item , true);
        }
    }

    public function column_title($item){
        // $action= array(
        //     "edit" => sprintf('<a href ="?page=%s$&action=%s&post_id=%s">Edit</a>', $_GET['page'],'list-edit',$item['id']),
        //     "delete" => sprintf('<a href ="?page=%s$&action=%s&post_id=%s">Delete</a>', $_GET['page'],'list-delete',$item['id'])
        // );
        $action= array(
            "edit" => "<a href='?page=".$_GET['page']."&action=list-edit&post_id=".$item['id'] . "'>Edit</a>",
            "delete" => "<a href='?page=".$_GET['page']."&action=list-delete&post_id=".$item['id'] . "'>delete</a>"
        );
        return sprintf('%1$s %2$s', $item['title'],$this->row_actions($action));
    }
   
}//end class

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