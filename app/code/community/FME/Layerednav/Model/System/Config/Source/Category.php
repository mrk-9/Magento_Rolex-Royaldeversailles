<?php
/**
* FME Layered Navigation 
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Model_System_Config_Source_Category   
*/ 

class FME_Layerednav_Model_System_Config_Source_Category extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();
        
        $options[] = array(
                'value'=> 'breadcrumbs',
                'label' => Mage::helper('layerednav')->__('Breadcrumbs')
        );
        $options[] = array(
                'value'=> 'none',
                'label' => Mage::helper('layerednav')->__('None')
        );
        
        return $options;
    }
} 