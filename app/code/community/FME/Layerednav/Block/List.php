<?php

/**
 * FME Layered Navigation 
 * 
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_Block_List  
 */
class FME_Layerednav_Block_List extends Mage_Core_Block_Template {

    protected $_productCollection;
    protected $_module = 'catalog';

    /**
     * @return Mage_Catalog_Block_Product_List
     */
    public function getListBlock() {

        return $this->getChild('product_list');
    }

    public function setListOrders() {
        if ('catalogsearch' != $this->_module)
            return $this;

        $category = Mage::getSingleton('catalog/layer')
                ->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders = array_merge(array(
            'relevance' => $this->__('Relevance')
                ), $availableOrders);

        $this->getListBlock()
                ->setAvailableOrders($availableOrders)
                ->setDefaultDirection('desc')
                ->setSortBy('relevance');

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return AdjustWare_Nav_Block_List
    */ 
    public function setListModes() {

        $this->getListBlock()
                ->setModes(array(
                    'grid' => $this->__('Grid'),
                    'list' => $this->__('List'))
        );
        return $this;
    }

    public function setIsSearchMode() {
        $this->_module = 'catalogsearch';
        return $this;
    }

    /**
     * Set All products collection
     *
     * @return AdjustWare_Nav_Block_List
     */
    public function setListCollection() {
        $this->getListBlock()
                ->setCollection($this->_getProductCollection());
        return $this;
    }

    protected function _toHtml() {
        $this->setListOrders();
        $this->setListModes();
        $this->setListCollection();

        $html = $this->getChildHtml('product_list');
        $html = Mage::helper('layerednav')->wrapProducts($html);

        return $html;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection() {
        if (is_null($this->_productCollection)) {

            $this->_productCollection = Mage::getSingleton($this->_module . '/layer')
                    ->getProductCollection();
        }

        return $this->_productCollection;
    }

}
