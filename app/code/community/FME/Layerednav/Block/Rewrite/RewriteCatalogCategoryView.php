<?php
/**
* FME Layered Navigation 
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Block_Rewrite_RewriteCatalogCategoryView
*/ 

class FME_Layerednav_Block_Rewrite_RewriteCatalogCategoryView extends Mage_Catalog_Block_Category_View
{ 
    public function getProductListHtml()
    {
    	
        $html .= parent::getProductListHtml();
        if ($this->getCurrentCategory()->getIsAnchor()){
            $html = Mage::helper('layerednav')->wrapProducts($html);
        }
        return $html;
    }   
} 