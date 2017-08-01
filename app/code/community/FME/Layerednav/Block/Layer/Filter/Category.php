<?php

/**
 * FME Layered Navigation
 * 
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_Block_Layer_Filter_Category
 * @Overwrite    Mage_Catalog_Block_Layer_Filter_Category
 */
class FME_Layerednav_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category {

    public function __construct() {  
        parent::__construct();
        //Load Custom PHTML of category 
        $this->setTemplate('layerednav/filter_category.phtml');
        //Set Filter Model Name
        $this->_filterModelName = 'layerednav/layer_filter_category';
    }

    public function getVar() {
        //Get request variable name which is used for apply filter
        return $this->_filter->getRequestVar();
    }

    public function getClearUrl() {
        //Get URL and rewrite with SEO frieldly URL
        $_seoURL = '';
        //Get request filters with URL 
        $query = Mage::helper('layerednav')->getParams();
        if (!empty($query[$this->getVar()])) {
            $query[$this->getVar()] = null;
            $_seoURL = Mage::getUrl('*/*/*', array(
                        '_use_rewrite' => true,
                        '_query' => $query,
            ));
        }

        return $_seoURL;
    }

}
