<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>VK Databases</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<?php
ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
include "common/functions.php";
if (!$_GET) {
    //список всех стран
    $url = 'https://api.vk.com/method/database.getCountries?need_all=1&count=300';
    $countries = json_decode(file_get_contents($url), true)['response'];
    // $countries =
    // echo '<pre>'.print_r($countries,true).'</pre>';

    $arr = [];
    foreach ($countries as $country) {
        //формирум ссылки с запросами на регионы и на города
         $hrefRegion = 'vkdatabase.php?country='.$country['cid'].'&regionget=get';
         $hrefSity = 'vkdatabase.php?country='.$country['cid'].'&sityget=get';

         $arr[] = '<div><a class=""country_list__href" href="' . $hrefRegion . '">' . $country['title'] . ' Регионы</a></div>'.
                '<div><a class=""country_list__href" href="' . $hrefSity . '">' . $country['title'] . ' Города</a></div>';


    };
    echo displayList($arr,4);

}
//РЕГИОНЫ И Города для страны поиск по стране
if (isset($_GET['country']) && isset($_GET['regionget']) && $_GET['regionget']=='get') {

    $urlRegion = 'https://api.vk.com/method/database.getRegions?country_id=' . $_GET['country']. '&count=1000';
    $regions = json_decode(file_get_contents($urlRegion), true)['response'];
    $arr=[];
    foreach($regions as $region) {
        $hrefSity = 'vkdatabase.php?country='.$_GET['country'].'&region='.$region['region_id'].'&sityget=get';
        $arr[] = '<div><a class="country_list__href" href="' . $hrefSity . '">' . $region['title'] . ' Населённые пункты</a></div>';
    }
    //echo '<pre>'.print_r($regions,true).'</pre>';
    echo displayList($arr,4);

}
//поиск по стране, региону и городу получаем список населённых пунктов - если задан регион
if (isset($_GET['country']) && isset($_GET['sityget']) && $_GET['sityget']=='get' && isset($_GET['region']) && $_GET['region']) {
    //поиск регионов
    $urlSity = 'https://api.vk.com/method/database.getCities?country_id=' . $_GET['country']. '&region_id=' .$_GET['region']. '&count=1000';
    $sities = json_decode(file_get_contents($urlSity), true)['response'];
    $arr=[];
    foreach($sities as $sity) {
    //    $hrefSity = 'vkdatabase.php?country='.$_GET['country'].'&region='.$sity['cid'].'&sityget=get';
     //   $arr[] = '<div><a class="country_list__href" href="' . $hrefSity . '">' . $sity['title'] . ' Населённые пункты</a></div>';
        //print_r($sity);
        $hrefSchool= 'vkdatabase.php?schoolget=get&sity='.$sity['cid'];
        $arr[] = '<div>' . $sity['title'] . '</div><div><a class="country_list__href" href="' . $hrefSchool . '">'.$sity['title'].' школы</a></div>';

    }
    echo displayList($arr,4);
    // echo '<pre>'.print_r($sities,true).'</pre>';

    //иначе если регион не задан
    //поиск по стране и городу = получаем города федерального значения, некоторые из них сами имеют населённые пункты, поэтому по ним поиск
} elseif (isset($_GET['country']) && isset($_GET['sityget']) && $_GET['sityget']=='get') {
    //поиск регионов
    $urlSity = 'https://api.vk.com/method/database.getCities?country_id=' . $_GET['country']. '&count=1000';
    $sities = json_decode(file_get_contents($urlSity), true)['response'];
    $arr=[];
    foreach($sities as $sity) {
        $hrefSity = 'vkdatabase.php?country='.$_GET['country'].'&region='.$sity['cid'].'&sityget=get';
        $hrefSchool= 'vkdatabase.php?schoolget=get&sity='.$sity['cid'];
        $arr[] = '<div><a class="country_list__href" href="' . $hrefSity . '">' . $sity['title'] . ' Населённые пункты</a></div>'.
                               '<div><a class="country_list__href" href="' . $hrefSchool . '">'.$sity['title'].' школы</a></div>';
//        $arr[] = '<div>' . $sity['title'] . '</div>';
    }
    echo displayList($arr,4);
  //  echo '<pre>'.print_r($sities,true).'</pre>';
    //поиск школ для населённого пункта
} else if (isset($_GET['schoolget']) && $_GET['schoolget']=='get' && isset($_GET['sity'])) {
    //поиск школ для населённого пункта
    $urlSchool = 'https://api.vk.com/method/database.getSchools?city_id='.$_GET['sity'].'&count=10000';
    $schools=json_decode(file_get_contents($urlSchool),true)['response'];
    $arr=[];
    foreach ($schools as $key => $school) {
        if ($key==0){
            continue;
        }
        $arr[] = '<div>'.$school['title'] .'</div>';
    }
    echo displayList($arr,4);


}


?>


</body>
</html>