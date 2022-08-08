<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;


class HomepagePresenter extends BasePresenter
{
    public function actionDefault()
    {
        echo "It works";
        die;
        $url = 'https://www.noviko-online.cz:8081/restapi/b2b/zbozi/';

//open connection
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERPWD, "16203/SEBELA" . ":" . "vichr");

//execute post
        $result = curl_exec($ch);
        //print_r($result);
        curl_close($ch);

        if (file_put_contents ('./sklad.xml', $result) !== false) {
            echo 'Success!';
        } else {
            echo 'Failed';
        }
    }
}
