<?php
namespace Controller;

class Website
{

    public static function home(){
        \View::page('home');
    }

    public static function create(){
        $hash = md5( date("U").print_r($_SERVER,true).rand() );
        $json = json_encode(array(
            'hash'      =>  $hash,
            'created'   =>  date("U"),
            'data'      =>  array()
        ));
        file_put_contents('../data/'.$hash.'.json',$json);
        \View::redirect('/'.$hash);
    }

    public static function sessionJSON($arg){
        $hash = preg_replace('/([^a-f0-9])/','',$arg[1]);
        if( strlen($hash) === 32 ){
            if( file_exists('../data/'.$hash.'.json' ) ){
                $session = json_decode(file_get_contents('../data/'.$hash.'.json' ),true);
                \Output::success($session);
            }
        }
        \Output::error('Invalid Session ID',404);
    }

    public static function session($arg){
        $hash = preg_replace('/([^a-f0-9])/','',$arg[1]);
        if( strlen($hash) === 32 ){
            if( file_exists('../data/'.$hash.'.json' ) ){
                $session = json_decode(file_get_contents('../data/'.$hash.'.json' ),true);
                \View::page('session', $session );
                exit();
            }
        }
        \View::page('404');
    }

    public static function dns(){
        if( isset($_GET["domain"]) ){
            if( substr($_GET["domain"],(0 - strlen(Domain::get()) ),strlen(Domain::get())) === Domain::get() ){
                $hash = preg_replace('/([^a-f0-9])/','',substr( str_replace('.'.Domain::get() ,'',$_GET["domain"]),-32,32));
                if( file_exists('../data/'.$hash.'.json') ){
                    $session = json_decode( file_get_contents('../data/'.$hash.'.json'),true  );
                    $session["data"][] = array(
                        'type'          =>  'dns',
                        'domain'        =>  $_GET["domain"],
                        'request_type'  =>  $_GET["type"],
                        'ip'            =>  $_GET["ip"],
                        'created_at'    =>  date("U")
                    );
                    file_put_contents('../data/'.$hash.'.json',json_encode($session));
                }
            }
        }
    }



}


