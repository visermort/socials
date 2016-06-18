<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои друзья ВКонтакте</title>
    <script src="//vk.com/js/api/openapi.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<div class="wrapper">
<?php

ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
include 'common/config.php';
include 'common/functions.php';
require 'common/getvkfriends.class.php';


$token = getAccessToken($vkApp['id'],$vkApp['secret'],$_SERVER['SCRIPT_URI']);

if (isset($token)) {
    $vkFriends = new VkFriends('https://api.vk.com/method/friends.get', $token);
    if (isset($_GET['page']) && $_GET['page']){
        $pageNum = $_GET['page'];
    } else {
        $pageNum = 0;
    }

    $friends = $vkFriends->getFriends('nickname,photo_100,status', $pageNum, 18, '', '');

//    echo '<br>результат<pre>' . print_r($friends, true) . '</pre>';

    echo '<h3>Мои друзья</h3>';
    foreach ( $friends['persons'] as $value) {
        $userId = $value['uid'];
        $linkProfile ='http://vk.com/id'.$userId;
        $fr ='<div class="inline-block friend-block" data-id="'.$userId.'"><p>'.$value['first_name'].'<br>'.$value['last_name'].'</p>';
        $fr .= '<a  href="'.$linkProfile.'" target="_blank">';
        $fr .= '<img src = "'.$value["photo_100"].'">';
        if (isset($value['status'])){
            $fr .= '<p>'.$value['status'].'</p>';
        }
        $fr .= '</a></div>';
        echo $fr;
    }
    //формируем пагинацию

    function createPageButton($page,$current) {
        if ($page == ($current)) {
            echo  'Страница' . ($page+1);
        } else {
            $hrefnext = 'page=' . $page;
            echo '<a href="vkfriends.php?' . $hrefnext . '" ><button>Страница' . ($page+1) . '</button></a>';
        }
    }
    echo '<div class="pagination">';
    for ($i=0; $i<=$friends['pages']['page']; $i++) {
        createPageButton($i,$friends['pages']['page']);
    }
    if ($friends['pages']['nextPage']) {
        createPageButton($friends['pages']['page']+1,$friends['pages']['page']);
    }
    echo '</div>';
}

?>

    </div>

<script src="js/vkfriends.js" type="text/javascript"></script>
</body>
</html>