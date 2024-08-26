<?php
class Fragment extends Woreda {

    public $fragment;

    public function render ($fragment) {
        echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/fragment/'. $fragment . '.html');
    }
} 
?>