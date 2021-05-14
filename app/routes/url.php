<?php
use Controller\Domain;

if( substr($_SERVER["HTTP_HOST"],(0 - strlen(Domain::get()) ),strlen(Domain::get())) === Domain::get() ){
    $hash = preg_replace('/([^a-f0-9])/','',substr( str_replace('.'.Domain::get() ,'',$_SERVER["HTTP_HOST"]),-32,32));
    if( file_exists('../data/'.$hash.'.json') ){
        $session = json_decode( file_get_contents('../data/'.$hash.'.json'),true  );
        $request = array();
        $request[] = $_SERVER["REQUEST_METHOD"].' '.$_SERVER["REQUEST_URI"].' '.$_SERVER["SERVER_PROTOCOL"];
        $request[] = 'Host: '.$_SERVER["HTTP_HOST"];
        foreach( $_SERVER as $k=>$v ){
            if( substr($k,0,5) === 'HTTP_' ){
                $k = str_replace(' ','-',ucwords(str_replace('_',' ',substr(strtolower($k),5,1000))));
                if( $k != 'Host' ) {
                    $request[] = $k . ': '.$v;
                }
            }
        }
        $request[] = '';
        $request[] = file_get_contents('php://input');
        $session["data"][] = array(
            'type'  =>  'http',
            'domain'    =>  $_SERVER["HTTP_HOST"],
            'request'   =>  $request,
            'remote_ip' =>  $_SERVER["REMOTE_ADDR"],
            'created_at'    =>  date("U")
        );
        file_put_contents('../data/'.$hash.'.json',json_encode($session));
        echo "OK";
        exit();
    }
}


Route::add(array('GET', 'POST'), '/', 'Website@home');
Route::add(array('GET', 'POST'), '/create', 'Website@create');
Route::add(array('GET', 'POST'), '/[md5-hash]', 'Website@session');
Route::add(array('GET', 'POST'), '/[md5-hash].json', 'Website@sessionJSON');
Route::add(array('GET', 'POST'), '/api/dns-request', 'Website@dns');