<?php
error_reporting(1);
define( 'FORUM_ROOT', '../../' );
define( 'FORUM_SKIP_CSRF_CONFIRM', true );

//extension specific status code
define( 'PUN_LIKE_LIKES', 1 );
define( 'PUN_LIKE_DISLIKES', 2 );
define( 'PUN_LIKE_NEUTRAL', 0 );

require_once FORUM_ROOT . 'include/common.php';

/**
 * Creates a new entry to the like table
 * 
 * @author Tareq Hasan
 */
function pl_new_entry( $post_id, $user_id, $type ) {
    global $forum_db;
    
    $time = time();
    $query = array(
        'INSERT' => 'post_id, type, user_id, updated',
        'INTO' => 'likes',
        'VALUES' => "$post_id, $type, $user_id, $time"
    );
    
    $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );
    
    return $result;
}

/**
 * Update the type of a row
 * 
 * @author Tareq Hasan
 */
function pl_update_type( $post_id, $user_id, $type ) {
    global $forum_db;
    
    $query = array(
        'UPDATE' => 'likes',
        'SET' => "type={$type}",
        'WHERE' => "post_id={$post_id} AND user_id={$user_id}"
    );
    
    $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );
    
    return $result;
}

/**
 * Check if a row in the table already exists
 * 
 * @author Tareq Hasan
 */
function pl_entry_exists( $post_id, $user_id ) {
    global $forum_db;

    $query = array(
        'SELECT' => 'id',
        'FROM' => 'likes',
        'WHERE' => "post_id={$post_id} AND user_id={$user_id}"
    );
    
    $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );

    //if anything found, return true
    if( $forum_db->num_rows( $result ) != false ) {
        return true;
    }
    
    return false;
}

/**
 * Checks if the user already voted
 * 
 * @author Tareq Hasan
 */
function pl_user_voted( $post_id, $user_id ) {
    global $forum_db;

    $query = array(
        'SELECT' => 'type',
        'FROM' => 'likes',
        'WHERE' => "post_id={$post_id} AND user_id={$user_id} AND type!=" . PUN_LIKE_NEUTRAL
    );
    
    $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );

    //if anything found, return the type
    if( $forum_db->num_rows( $result ) != false ) {
        
        $data = $forum_db->fetch_assoc( $result );
        
        return $data['type'];
    }
    
    return false;
}

/**
 * Get the user lists of a specific like type
 * 
 * @author Tareq Hasan
 */
function pl_user_list( $post_id, $type ) {
    global $forum_db;

    $query = array(
        'SELECT' => 'l.user_id, u.username',
        'FROM' => 'likes AS l',
        'JOINS' => array(
            array(
                'LEFT JOIN' => 'users AS u',
                'ON' => 'u.id=l.user_id'
            ),
        ),
        'WHERE' => "post_id={$post_id} AND type={$type}"
    );
    
    $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );
    
    $users = array();
    if( $forum_db->num_rows( $result ) ) {
        while ( $user_data = $forum_db->fetch_assoc( $result ) ) {
            $users[] = sprintf( '<a href="%s">%s</a>', forum_link( 'profile.php?id=' . $user_data['user_id'] ), $user_data['username'] );
        }
    }
    
    if( count( $users ) ) {
        return implode( ', ', $users );
    }
    
    return false;
}


function pl_get_count( $post_id, $type = 1 ) {
    global $forum_db, $lang_pun_like;
    
    $query = array(
        'SELECT' => 'count(id) as count',
        'FROM' => 'likes',
        'WHERE' => "post_id={$post_id} AND type={$type}",
    );

    $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );
    
    if( $forum_db->num_rows( $result ) != false ) {
        
        $data = $forum_db->fetch_assoc( $result );
        
        return $data['count'];
    }
    
    return 0;
}

//$likes = pl_user_list( 3, PUN_LIKE_LIKES );
//var_dump( $likes );

function tareq_make_bangla_number( $str ) {
	$engNumber = array(1,2,3,4,5,6,7,8,9,0);
	$bangNumber = array('১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০');
	
	$converted = str_replace($engNumber, $bangNumber, $str);
	return $converted;
}
