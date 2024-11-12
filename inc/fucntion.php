<?php

function plugin_scripts(){
    wp_enqueue_script('app',LIKE_ASSETS . 'js/app.js','[jquery]','1.0.0',true);
    wp_localize_script( 'app' ,'app_script' ,[
        'site_url' => admin_url( 'admin-ajax.php' )
    ]);
}

add_action('wp_enqueue_scripts','plugin_scripts');

add_action('wp_head',function(){
    ?>
    <style>
        .btn-like{
            border: 1px solid #eee;
            border-radius: 5px;
            background: transparent;
            outline: none !important;
            color: #555;
            display: inline-block;
            padding: 8px 14px;
            transition: all 300ms;
        }
        .btn-like:focus{
            outline: unset !important;
        }
        .btn-like.liked{
            color: #fff;
            background: #db0850;
        }
    </style>
<?php
});

add_filter('the_content',function($content){
    $postId = get_the_ID();
    $postCount = post_like_count($postId);
    $userLiked = check_current_user_liked_post($postId) ? ' liked': '';
    $btnLike = "<button class='btn-like $userLiked' data-id='{$postId}' >like
    <span class='post-count'>($postCount)</span>
    </button>";
    return $content . $btnLike;
});

function check_current_user_liked_post($postId){
    global $wpdb;
    $userId = get_current_user_id();
    $tblName = $wpdb->prefix.'liked';
    if(is_user_logged_in()){
        $where = $wpdb->prepare(" AND user_id = %d",$userId);
    }else{
        $where = $wpdb->prepare(" AND ip = %s",$_SERVER['REMOTE_ADDR']);
    }
    $result = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$tblName} WHERE post_id = %d" ,$postId,$userId)
    );
    return intval($result);
}

function post_like_count($postId){
    global $wpdb;
    $tblName = $wpdb->prefix.'liked';
    $result = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$tblName} WHERE post_id = %d",$postId)
    );
    return intval($result);
}


//ajax
add_action('wp_ajax_like_plugin','like_plugin_action');
add_action('wp_ajax_nopriv_like_plugin','like_plugin_action');

function like_plugin_action(){
    global $wpdb;
    $tblName = $wpdb->prefix.'liked';
    $postId = intval($_POST['postId']);
    $userLiked = check_current_user_liked_post($postId) ? 0 : 1 ;
    $msg = false;
    $result = false;
    if($userLiked){
        if(get_post_type( $postId )){
            $result = $wpdb->insert($tblName,[
                'post_id' => $postId,
                'user_id' => get_current_user_id(),
                'ip'      => $_SERVER['REMOTE_ADDR'],
                'like_post' => 1,
                'created_at' => current_time('mysql')
            ]);
        }
        if(!$result){
            $msg = 'خطا در لایک پست';
        }
    }else{
        $result = $wpdb->delete($tblName,[
            'post_id' => $postId,
            'user_id' => get_current_user_id(),
        ]);
        if(!$result){
            $msg = 'خطا در حذف لایک ';
        }
    }
    wp_send_json(['liked' => $userLiked,'postCount' => post_like_count($postId),'error' => $msg ,'dd'=>$result]);
}
