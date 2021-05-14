<?php


namespace Model;


class ExampleModel
{

    private $data;

    private function __construct($arr){
        $this->data = $arr;
    }

    /**
     * @return int
     */
    public function getId(){
        return (int)$this->data["id"];
    }

    /**
     * @return string
     */
    public function getUserName(){
        return $this->data["username"];
    }


    /**
     * @param $id
     * @return ExampleModel
     */
    public static function get($id){
        return new ExampleModel(array(
           'id'         =>  1,
           'username'   =>  'adamtlangley'
        ));
    }

}