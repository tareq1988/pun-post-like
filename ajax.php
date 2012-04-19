<?php

define( 'FORUM_ROOT', '../../' );
define( 'FORUM_SKIP_CSRF_CONFIRM', true );


require FORUM_ROOT . 'include/common.php';

error_reporting(E_ALL);

if( $forum_user['is_guest'] ) {
    message( $lang_common['No permission'] );
}

$cur_path = dirname( __FILE__ );
$cur_url = $base_url . '/' . basename( dirname( dirname( __FILE__ ) ) ) . '/' . basename( dirname( __FILE__ ) );

//load language file
if( file_exists( $cur_path . '/lang/' . $forum_user['language'] . '.php' ) )
    require $cur_path . '/lang/' . $forum_user['language'] . '.php';
else
    require $cur_path . '/lang/English.php';
    
require_once $cur_path . '/functions.php';


if( isset( $_POST ) && $_POST['action'] == 'do_like' ) {
    $post_id = isset( $_POST['pid'] ) ? intval( $_POST['pid'] ) : 0;
    $user_id = $forum_user['id'];
    
    //$post = print_r( $_POST, true );
    //echo json_encode( $post );exit;

    $response = array();
    
    switch( $_POST['type'] ) {
        case 'like':
            
            //if entry exists, update it. Otherwise insert a new
            if( pl_entry_exists( $post_id, $user_id ) ) {
                
                pl_update_type( $post_id, $user_id, PUN_LIKE_LIKES );
                
            } else {
                
                pl_new_entry( $post_id, $user_id, PUN_LIKE_LIKES );
            }
            
            $response['title'] = $lang_pun_like['dislike this'];
            $response['text'] = $lang_pun_like['dislike'];
            $response['msg'] = $lang_pun_like['you liked'];
            $response['action'] = 'nutral';
            
            break;

        case 'nutral':
        
            //if entry exists, update it. Otherwise insert a new
            if( pl_entry_exists( $post_id, $user_id ) ) {
                
                pl_update_type( $post_id, $user_id, PUN_LIKE_NEUTRAL );
                
            } else {
                
                pl_new_entry( $post_id, $user_id, PUN_LIKE_NEUTRAL );
            }
            
            $response['title'] = $lang_pun_like['like this'];
            $response['text'] = $lang_pun_like['like'];
            $response['msg'] = $lang_pun_like['you disliked'];
            $response['action'] = 'like';
            
            break;

        default:
            
    }
    
    $response['count'] = pl_get_count( $post_id );
    $response['status'] = 'success';
    
    //make bangla number
    if( $forum_user['language'] == 'Bangla' ) {
        $response['count'] = tareq_make_bangla_number( $response['count'] );
    }
    
    echo json_encode( $response );
    exit;
}

//serve the user count
if( isset( $_GET ) && $_GET['action'] == 'count' ) {
    $pid = isset( $_GET['pid'] ) ? intval( $_GET['pid'] ) : 0;
    
    //$get = print_r( $_GET, true );
    //echo json_encode( $get );exit;
    
    if( $pid ) {
        
        $users = pl_user_list( $pid, PUN_LIKE_LIKES );
        
        if( $users ) {
            $response = array(
                'status' => 'success',
                'data' => $users,
                'title' => $lang_pun_like['who liked']
            );
        } else {
            $response = array(
                'status' => 'success',
                'data' => $lang_pun_like['no user found'],
                'title' => $lang_pun_like['who liked']
            );
        }

        echo json_encode( $response );
        exit;
    }
}
