<?php

/**
 * FME Layered Navigation 
 * 
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_Block_Search_Layer 
 */
class FME_Layerednav_Block_Search_Layer extends FME_Layerednav_Block_Layer_View {

    public function getLayer() {
        return Mage::getSingleton('catalogsearch/layer');
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock() {

        $availableResCount = (int) Mage::app()->getStore()
                        ->getConfig(Mage_CatalogSearch_Model_Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount || ($availableResCount >= $this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }
        return false;
    }

    protected function createCategoriesBlock() {

        $categoryBlock = $this->getLayout()
                ->createBlock('layerednav/layer_filter_categorysearch')
                ->setLayer($this->getLayer())
                ->init();
        $this->setChild('category_filter', $categoryBlock);
    }

}
