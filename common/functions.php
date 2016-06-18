<?php

//коллекцию будем выводить в виде таблицы c заданным количеством колонок
function displayList($array,$cols)
{
    $rows = ceil(count($array) / $cols);
    //$ret = count($array).' '.$rows;
    $ret = '<table class="list-table">';
    for ($i=0; $i < $rows; $i++ ){
        $ret .= '<tr class="list-table__row">';
        for ($j=0; $j<$cols; $j++){
            $ret .='<td class="list-table__cell">';
        //    $ret .='aaa';
            $ind = (int)$i*$cols + $j;
            if ($ind < count($array)) {
                $ret .= (string)$array[$ind];
                //$ret .='item';
            }
            $ret .='</td>';
        }
        $ret .='</tr>';
    }
    $ret .='</table>';
    return $ret;
}


function getCurl($url,$params){
    $myCurl = curl_init();
    //echo $url.'<pre>'.print_r($params,true).'</pre>';
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => false,
        CURLOPT_POSTFIELDS => http_build_query($params) /*здесь массив параметров запроса*/
    ));
    $response = curl_exec($myCurl);
    //echo $response;
    curl_close($myCurl);
    return $response;
}

function getAccessToken($AppId,$AppSecret,$rurl) {
    session_start();
    if (!empty($_GET['code'])) {
        //код имеется, формируем url  на получение токен
        $url = 'https://oauth.vk.com/access_token?client_id='.$AppId.'&client_secret='.$AppSecret.'&redirect_uri='.$rurl.'&code='.$_GET['code'];
        // echo $url.'<br>';
        $json = file_get_contents($url);
        if (!empty($json)) {
            $ar = json_decode($json, true);
            //   echo 'Полученные ключи, среди них нужный токен для авторизации, используем его дальше для вызова функций ';
            //   echo '<pre>'.print_r($ar,true).'</pre>';
            $token = $ar['access_token'];
            //     echo 'Token to session ' . $token . '<br>';
            $_SESSION['vk_access_token'] = $token;
            header('Location:'.$rurl);
            exit;

        }
    }  elseif ($_SESSION['vk_access_token']) {
        $token = $_SESSION['vk_access_token'];
        // echo 'Token from session ' . $token . '<br>';
    }
    return $token;

}
//
//function getAccessTokenStandalone($url) {
//    session_start();
//    echo 'server '.print_r($_SERVER,true).' get '.print_r($_REQUEST,true);
//   // if (!empty($_GET['access_token'])) {
//      //  $token =  $_GET['access_token'];
//       // echo 'token in get '.$token;
//
//     //   $_SESSION['vk_access_token'] = $token;
//     //   header('Location:'.$url);
//     //   exit;
//   // }  elseif ($_SESSION['vk_access_token']) {
//   //     $token = $_SESSION['vk_access_token'];
//   //     echo 'token in session';
//        // echo 'Token from session ' . $token . '<br>';
//    //}
//   // return $token;
//}