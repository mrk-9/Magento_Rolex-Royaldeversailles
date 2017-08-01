<?php

/**
 * FME Layered Navigation
 *  
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_Block_Layer_Filter_Attribute
 * @Overwrite    Mage_Catalog_Block_Layer_Filter_Attribute
 */
class FME_Layerednav_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute {

    public function __construct() {
        parent::__construct();
        //Load Custom PHTML of attributes 
        $this->setTemplate('layerednav/filter_attribute.phtml');
        //Set Filter Model Name
        $this->_filterModelName = 'layerednav/layer_filter_attribute';
    }

    public function getVar() {
        //Get request variable name which is used for apply filter
        return $this->_filter->getRequestVar();
    }

    public function getClearUrl() {
        //Get URL and rewrite with SEO frieldly URL
        $_seoURL = '';
        $this->getVar();
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

    public function getHtmlId($item) {
        //Make HTMLID with requested filter + value of param
        return $this->getVar() . '-' . $item->getValueString();
    }

    public function Selectedfilter($item) {
        //Set Selected filters 
        $ids = (array) $this->_filter->getActiveState();
        return in_array($item->getValueString(), $ids);
    }

    public function getFiltersArray() {

        $_filtersArray = array();
          $hideLinks = Mage::getStoreConfig('layerednav/layerednav/remove_links');
        //Get all filter items  ( use getItems method of Mage_Catalog_Model_Layer_Filter_Abstract )
        foreach ($this->getItems() as $_item) { 


            $showSwatches = Mage::getStoreConfig('layerednav/layerednav/show_swatches');
            $_htmlFilters = 'id="' . $this->getHtmlId($_item) . '" ';
            $var_href = "#";

            //Create URL
            $var_href = html_entity_decode($currentUrl = Mage::app()->getRequest()->getBaseUrl() . Mage::getSingleton('core/session')->getRequestPath());
            $_htmlFilters .= 'href="' . $var_href . '" ';

            $_htmlFilters .= 'class="fme_layered_attribute '
                    . ($this->Selectedfilter($_item) ? 'fme_layered_attribute_selected' : '') . '" ';

            //Check the number of products against filter
            $qty =    $_item->getCount() ;
            if (!$this->getHideQty())
                $qty =    '('.$_item->getCount().')' ;


            if ($this->getName() == "Color") {

                if ($showSwatches == "iconslinks") {

                    $iconCode = Mage::helper('layerednav')->checkColor($_item->getLabel());

                    $_html = "";
                    $_html .= '<div class="color">
                                        <a ' . $_htmlFilters . '><div class="color_box" style="background-color:' . $iconCode . ';"></div>
                                        ' . $_item->getLabel() . '</a><span>' . $qty . '</span>
                                </div>';
                } elseif ($showSwatches == "icons") {

                    $iconCode = Mage::helper('layerednav')->checkColor($_item->getLabel());

                    $_html = "";
                    $_html .= '<div class="color">
                                        <a ' . $_htmlFilters . '><div class="color_box" style="background-color:' . $iconCode . ';"></div>
                                        </a><span>' . $qty . '</span>
                                </div>';
                } else {

                    $_html = "";
                    $_html .= '<div class="color"><a ' . $_htmlFilters . '>' . $_item->getLabel() . '</a><span>' . $qty . '</span></div>';
                }
            }



            if ($this->getName() == "Color") {
                $_filtersArray[] = $_html;
            } else {
                $_filtersArray[] = '<div class="color"><a ' . $_htmlFilters . '>' . $_item->getLabel() . '</a><span>' . $qty.'</span></div>';
            }
        }

        return $_filtersArray;
    }

}
