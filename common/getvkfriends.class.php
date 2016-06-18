<?php



class VkFriends
{
    private $url;
    private $token;
    private $fields;
    private $pages = array(
        'limit' => 24,
        'page' => 0,
        'nextPage' => false
      //  'sort' => '',
      //  'order' => '',
      //  'pages' => 0,
      //  'count' => 0
    ) ;




    public function getFriends($fields,$page,$limit,$order,$dir)
    {
        $this->scope = $fields;
        $this->pages['page'] = ($page?  $page : 0 );
        $this->pages['limit'] = ($limit?  $limit : 24 );
        $this->pages['offset'] = $this->pages['page'] * $this->pages['limit'];//оффсет
        $newUrl = $this->url.'?'.($this->token ? 'access_token='.$this->token.'&': '' ).'offset='.$this->pages['offset'].
            '&count='.$this->pages['limit'];
        if ($fields) {
            $newUrl .= '&fields=' . $fields;
        }
        //echo $newUrl;
        $json = file_get_contents($newUrl);

        $response = json_decode($json, true);
        $persons = $response['response'];
       // echo count($persons).' '.$this->pages['limit'];
        $this->pages['nextPage'] = (count($persons) >= $this->pages['limit']);//есть ли следующая страница = (количество == лимит)

        return array (
            'persons' => $persons,
            'pages' => $this->pages
        );

    }

    public function __construct($url,$token) {
        $this->url = $url;
        $this->token =$token;
    }

}