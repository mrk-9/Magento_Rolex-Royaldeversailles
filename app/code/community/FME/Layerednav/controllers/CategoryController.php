<?php

/**
 * FME Layered Navigation 
 * 
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_CategoryController  
 */
class FME_Layerednav_CategoryController extends Mage_Core_Controller_Front_Action {

    public function viewAction() {

        // init category
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            $this->_forward('noRoute');
            return;
        }

        $category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($categoryId);
        Mage::register('current_category', $category);
        Mage::helper('layerednav')->bNeedClearAll();



        $this->getLayout()->createBlock('layerednav/catalog_layer_view');
        $this->loadLayout();
        $this->renderLayout();
    }

}
