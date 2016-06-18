<?php

include 'config.php';

echo '<p>если ключ не пустой, то выводим токен. Код '.$_GET['code'].'</p>';

if (!empty($_GET['code'])){
    //код имеется, формируем url  на получение токен
    $url = 'https://oauth.vk.com/access_token?client_id='.$id.'&client_secret='.$secret.'&redirect_uri='.$redirect.'&code='.$_GET['code'];
    echo $url.'<br>';
    $json = file_get_contents($url);
    if (!empty($json)) {
        $ar = json_decode($json,true);
           echo 'Полученные ключи, среди них нужный токен для авторизации, используем его дальше для вызова функций <pre>';
           print_r($ar);
           echo '</pre>';
        $token = $ar['access_token'];
        echo 'Token '.$token.'<br>';
        $url='https://api.vk.com/method/friends.getOnline?&access_token='.$token;
        echo $url.'<br>';
        $json = file_get_contents($url);
        if (!empty($json)) {
            $ar = json_decode($json,true);
            echo 'Друзья онлайн <pre>';
            print_r($ar);
            echo '</pre>';
        }
        $url='https://api.vk.com/method/audio.get?&access_token='.$token;
        echo $url.'<br>';
        $json = file_get_contents($url);
        if (!empty($json)) {
            $ar = json_decode($json,true);
            echo 'Аудио <pre>';
            print_r($ar);
            echo '</pre>';
        }
}
}

