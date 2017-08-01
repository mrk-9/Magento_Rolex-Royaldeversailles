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

class FME_Layerednav_Model_System_Config_Source_Swatches extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();
        
        $options[] = array(
                'value'=> 'link',
                'label' => Mage::helper('layerednav')->__('Links Only')
        );
        $options[] = array(
                'value'=> 'icons',
                'label' => Mage::helper('layerednav')->__('Icons Only')
        );
        $options[] = array(
                'value'=> 'iconslinks',
                'label' => Mage::helper('layerednav')->__('Icons + Links')
        );
        
        return $options;
    }
} 