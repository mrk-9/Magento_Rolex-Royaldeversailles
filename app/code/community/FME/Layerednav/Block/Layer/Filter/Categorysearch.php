<?php
/**
* FME Layered Navigation
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Block_Layer_Filter_Categorysearch
* @Overwrite    FME_Layerednav_Block_Layer_Filter_Category
*/ 

class FME_Layerednav_Block_Layer_Filter_Categorysearch extends FME_Layerednav_Block_Layer_Filter_Category
{
    public function __construct()
    {

        parent::__construct();
		//Load Custom PHTML of category search
        $this->setTemplate('layerednav/filter_category_search.phtml');
		//Set Filter Model Name
        $this->_filterModelName = 'layerednav/layer_filter_categorysearch'; 
    }
    
} 