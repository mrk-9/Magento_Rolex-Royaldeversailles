<?php
/**
* FME Layered Navigation 
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Model_Select   
*/ 

class FME_Layerednav_Model_Select extends Zend_Db_Select 
{
    public function __construct()
    {
    }

    public function setPart($part, $val){
        echo $this->_parts[$part] = $val;
    }   
} 