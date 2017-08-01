<?php
/**
* FME Layered Navigation 
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Model_System_Config_Source_Price   
*/ 

class FME_Layerednav_Model_System_Config_Source_Price extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();
        
        $options[] = array(
                'value'=> 'default',
                'label' => Mage::helper('layerednav')->__('Default')
        );
        $options[] = array(
                'value'=> 'slider',
                'label' => Mage::helper('layerednav')->__('Slider')
        );
        $options[] = array(
                'value'=> 'input',
                'label' => Mage::helper('layerednav')->__('Input')
        );
        
        return $options;
    }
} 