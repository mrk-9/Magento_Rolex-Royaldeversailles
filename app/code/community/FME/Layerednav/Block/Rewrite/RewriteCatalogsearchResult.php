<?php
/**
* FME Layered Navigation 
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Block_Rewrite_RewriteCatalogsearchResult
*/ 
 
class FME_Layerednav_Block_Rewrite_RewriteCatalogsearchResult extends Mage_CatalogSearch_Block_Result
{   
    /**
     * Retrieve Search result list HTML output, wrapped with <div>
     * @return string
     */
    public function getProductListHtml()
    {
        $html = parent::getProductListHtml();
        $html = Mage::helper('layerednav')->wrapProducts($html);
        return $html;
    }
	
	/**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection()
    {
        //benz001  un-comment these two
        $this->getListBlock()
           ->setCollection($this->_getProductCollection());
       return $this;
    }
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            //$this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        }
        
        return $this->_productCollection;
    }
	
} 