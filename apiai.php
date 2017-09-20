<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/MyActionMapping.php';

use ApiAi\Client;
use ApiAi\Model\Query;
use ApiAi\Method\QueryApi;
/*use ApiAi\Dialog;
use ApiAi\MyActionMapping;*/


function make_seed(){
      list($usec, $sec) = explode(' ', microtime());
      return (float) $sec + ((float) $usec * 100000);
}

try {
    $client = new Client('3c74c58713224776881a94c5f220f368');
    $queryApi = new QueryApi($client);
    $sessionid = make_seed();
    $message = "오늘 날씨 어떠니?";
    $lang = "ko";


    $meaning = $queryApi->extractMeaning($message, [
        'sessionId' => $sessionid,
        'lang' => $lang,
    ]);
    $response = new Query($meaning);

    /*var_dump($response->getStatus(), $response->getResult());*/
    if ($response->getStatus()->getCode() >= 200 and $response->getStatus()->getCode() <= 300){
        echo "success<br /><br />";
        /*echo "getParameters".var_dump($response->getResult()->getParameters()["date"])."<br /><br />";*/
        echo "getContexts".var_dump($response->getResult()->getContexts()[0]->getParameters())."<br /><br />";
        echo "getResolvedQuery".var_dump($response->getResult()->getResolvedQuery())."<br /><br />";    //질문 쿼리
        echo "getAction".var_dump($response->getResult()->getFulfillment()->getSpeech() )."<br /><br />";

        $meaning = $queryApi->extractMeaning("오늘 날씨 어떠냐고!", [
            'sessionId' => $sessionid,
            'lang' => $lang,
            'contexts' =>$response->getResult()->getContexts()
        ]);

        $response = new Query($meaning);
        echo "=======================================================<br /><br />";
        echo "getContexts".var_dump($response->getResult()->getContexts())."<br /><br />";
        echo "getResolvedQuery".var_dump($response->getResult()->getResolvedQuery())."<br /><br />";    //질문 쿼리
        echo "getAction".var_dump($response->getResult()->getFulfillment()->getSpeech() )."<br /><br />";

    }else{
        echo "error".$response->getStatus()->getCode()."<br />";
        echo "ErrorId".$response->getStatus()->getErrorId()."<br />";
        echo "ErrorDetails".$response->getStatus()->getErrorDetails()."<br />";
    }


} catch (\Exception $error) {
    echo $error->getMessage();
}