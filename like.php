<?php

define( 'FORUM_ROOT', '../../' );
define( 'FORUM_SKIP_CSRF_CONFIRM', true );

require FORUM_ROOT . 'include/common.php';

if( $forum_user['is_guest'] ) {
    message( $lang_common['No permission'] );
}

$cur_path = dirname( __FILE__ );
$cur_url = $base_url . '/' . basename( dirname( dirname( __FILE__ ) ) ) . '/' . basename( dirname( __FILE__ ) );

//load language file
if( file_exists( $cur_path . '/lang/' . $forum_user['language'] . '.php' ) ) {
    require $cur_path . '/lang/' . $forum_user['language'] . '.php';
} else {
    require $cur_path . '/lang/English.php';
}

function list_users( $user, $type ) {
    global $forum_db, $lang_pun_like;
    

    $res = array( );
    $l = '';

    if( $type == 'like' ) {
        $res['title'] = $lang_pun_like['who liked the post'];
    } else {
        $res['title'] = $lang_pun_like['who disliked the post'];
    }

    $user = implode( ', ', $user );

    if( $user ) {
        // Grab the users
        $query = array(
            'SELECT' => 'u.id, u.username',
            'FROM' => 'users AS u',
            'WHERE' => "u.id IN($user)",
        );

        $result = $forum_db->query_build( $query ) or error( __FILE__, __LINE__ );

        if( $forum_db->num_rows( $result ) ) {
            while ( $user_data = $forum_db->fetch_assoc( $result ) ) {
                $l .= '<a href="' . forum_link( 'profile.php?id=' . $user_data['id'] ) . '">' . forum_htmlencode( $user_data['username'] ) . '</a>, ';
            }

            $res['msg'] = $l;
        } else {
            $res['msg'] = $lang_pun_like['nothing to display'];
        }
    } else {
        $res['msg'] = $lang_pun_like['nothing to display'];
    }

    echo json_encode( $res );
}

//SELECT count(id) as number FROM `likes`  WHERE `post_id`=3 and `type`=1
//SELECT l.user_id, u.username FROM `likes` l left join users u on l.user_id=u.id WHERE `post_id`=3 and `type`=1
