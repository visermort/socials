<?php
/**
 * Created by PhpStorm.
 * User: Spartak
 * Date: 22.04.2016
 * Time: 11:19
 */
include 'simple_html_dom.php';

function getCurl($url) {
    $myCurl = curl_init();
    curl_setopt_array($myCurl,array(
        CURLOPT_URL => $url
        ,CURLOPT_RETURNTRANSFER => true
       // ,CURLOPT_POST => true
    ));
    return curl_exec($myCurl);
}

//echo getCurl('visermort.ru');
$curlOut = getCurl('https://www.gismeteo.ru/city/daily/3934/');
//
//$html = new simple_html_dom();
//
//$html -> load($curlOut);

//foreach($html->find('div[class=temp] dd[class=value m_temp c]') as $element) {
  //  $weather = strip_tags($element -> innertext);
//    $weather = $element -> plaintext;
//}
//$weather = $html -> find('div id="weather"');


//$html -> clear();
//print_r($weather);
echo $curlOut;



