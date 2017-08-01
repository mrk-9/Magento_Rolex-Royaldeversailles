<?php

/**
 * FME Layered Navigation 
 * 
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_Helper_Data  
 */
class FME_Layerednav_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_params = null;
    protected $_continueShoppingUrl = null;

    const XML_PATH_CAT_STYLE = 'layerednav/layerednav/cat_style';
    const XML_PATH_REMOVE_LINKS = 'layerednav/layerednav/remove_links';
    const XML_PATH_RESET_FILTERS = 'layerednav/layerednav/reset_filters';
    
    public function catStyle($store = null) {
        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }
        
        return Mage::getStoreConfig(self::XML_PATH_CAT_STYLE, $store);
    }
    
    public function removeLinks($store = null) {
        
        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }
        
        return Mage::getStoreConfig(self::XML_PATH_REMOVE_LINKS, $store);
    }
    
    public function resetFilters($store = null) {
        
        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }
        
        return Mage::getStoreConfig(self::XML_PATH_RESET_FILTERS, $store);
    }
    
    public function isSearch() {

        $mod = Mage::app()->getRequest()->getModuleName();
        if ('catalogsearch' === $mod) {
            return true;
        }

        if ('layerednav' === $mod && 'search' == Mage::app()->getRequest()->getActionName()) {
            return true;
        }

        return false;
    }

    public function getContinueShoppingUrl() {
        if (is_null($this->_continueShoppingUrl)) {
            $url = '';

            $allParams = $this->getParams();
            $keys = $this->getNonFilteringParamKeys();

            $query = array();
            foreach ($allParams as $k => $v) {
                if (in_array($k, $keys))
                    $query[$k] = $v;
            }

            if ($this->isSearch()) {

                $url = Mage::getModel('core/url')->getUrl('catalogsearch/result/index', array('_query' => $query));
            } else {
                $category = Mage::registry('current_category');
                $rootId = Mage::app()->getStore()->getRootCategoryId();
                if ($category && $category->getId() != $rootId) {
                    $url = $category->getUrl();
                } else {
                    $url = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                }
                $url .= $this->toQuery($query);
            }
            $this->_continueShoppingUrl = $url;
        }

        return $this->_continueShoppingUrl;
    }

    public function wrapProducts($html) {

        $html = str_replace('onchange="setLocation', 'onchange="catalog_toolbar_make_request', $html);

        $loaderHtml = '<div class="fme_loading_filters" style="display:none"><img id="loading-image" src="' . Mage::getDesign()->getSkinUrl('images/FME/ajax-loader.gif') . '" /></div>';
        $html .= $loaderHtml;

        if (Mage::app()->getRequest()->isXmlHttpRequest()) {

            
            $html = str_replace('?___SID=U&amp;', '?', $html);
            $html = str_replace('?___SID=U', '', $html);
            $html = str_replace('&amp;___SID=U', '', $html);

            $k = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
            $v = Mage::helper('core')->urlEncode($this->getContinueShoppingUrl());
            $html = preg_replace("#$k/[^/]+#", "$k/$v", $html);
            
        } else {//echo 'here';exit;
            $html = '<div id="fme_layered_container">'
                    . $html
                    . '</div>'
                    . '';
        }
        
        return $html;
    }

    public function getParam($k) {
        $p = $this->getParams();
       $v = isset($p[$k]) ? $p[$k] : null;
        return $v;
    }

    // currently we use $without only if $asString=true
    public function getParams($asString = false, $without = null) { 
        if (is_null($this->_params)) { 

            $sessionObject = Mage::getSingleton('catalog/session');
            $bNeedClearAll = false;

            if ($this->resetFilters() AND Mage::registry('new_category')) {

                $bNeedClearAll = true;
            }

            if ($this->isSearch()) {
                $sessionObject = Mage::getSingleton('catalogsearch/session');
                $query = Mage::app()->getRequest()->getQuery();
                if (isset($query['q'])) {
                    if ($sessionObject->getData('layquery') && $sessionObject->getData('layquery') != $query['q']) {
                        $bNeedClearAll = true;
                    }
                    $sessionObject->setData('layquery', $query['q']);
                }
            }

            $nSavedCurrencyRate = $sessionObject->getAdjNavCurrencyRate();

            $nCurrentCurrencyRate = Mage::app()->getStore()->convertPrice(1000000, false);
            $nCurrentCurrencyRate = $nCurrentCurrencyRate / 1000000;

            $nSavedPriceStyle = $sessionObject->getAdjNavPriceStyle();
            $nCurrentPriceStyle = Mage::getStoreConfig('layerednav/layerednav/price_style');

            $bNeedClearPriceFilter = false;

            if ($nSavedCurrencyRate AND $nSavedCurrencyRate != $nCurrentCurrencyRate) {
                $bNeedClearPriceFilter = true;
            }

            if ($nSavedPriceStyle != $nCurrentPriceStyle) {
                $bNeedClearPriceFilter = true;
            }

            if ($bNeedClearPriceFilter) {
                $sess = (array) $sessionObject->getAdjNav();

                if ($sess) {
                    $aNonFilteringParamKeys = $this->getNonFilteringParamKeys();

                    foreach ($sess as $sKey => $sVal) {
                        if (!in_array($sKey, $aNonFilteringParamKeys)) {
                            $attribute = Mage::getModel('eav/entity_attribute');

                            $attribute->load($sKey, 'attribute_code');

                            if ($attribute->getFrontendInput() == 'price') {
                                unset($sess[$sKey]);
                            }
                        }
                    }

                    $sessionObject->setAdjNav($sess);
                }
            }

            $sessionObject->setAdjNavCurrencyRate($nCurrentCurrencyRate);
            $sessionObject->setAdjNavPriceStyle($nCurrentPriceStyle);

          $query = Mage::app()->getRequest()->getQuery();
            $sess = (array) $sessionObject->getAdjNav();

            $this->_params = array_merge($sess, $query);

            if (!empty($query['clearall']) OR $bNeedClearAll) { 
                $this->_params = array();
                if ($this->isSearch() && isset($query['q']))
                    $this->_params['q'] = $query['q'];
            }
            $sess = array();
            foreach ($this->_params as $k => $v) { 
                if ($v && 'clear' != $v)
                    $sess[$k] = $v;
            }

            if (Mage::registry('new_category') AND isset($sess['p'])) {
                unset($sess['p']);
            }

            $sessionObject->setAdjNav($sess);
            $this->_params = $sess;

            Mage::register('current_session_params', $sess);
        }
        if(!Mage::app()->getRequest()->getParam('dir')){ 
            $this->_params = array();
        }
        if ($asString) {
            return $this->toQuery($this->_params, $without);
        }

        return $this->_params;
    }

    public function toQuery($params, $without = null) {
        if (!is_array($without))
            $without = array($without);

        $queryStr = '?';
        foreach ($params as $k => $v) { 
            if (!in_array($k, $without))
                 $queryStr .= $k . '=' . urlencode($v) . '&';
        }
        return substr($queryStr, 0, -1);
    }

    public function stripQuery($url) {
        $pos = strpos($url, '?');
        if (false !== $pos)
            $url = substr($url, 0, $pos);
        return $url;
    }

    public function getClearAllUrl($baseUrl) {
        $baseUrl .= '?clearall=true';
        if ($this->isSearch()) {
            $baseUrl .= '&q=' . urlencode($this->getParam('q'));
        }
        return $baseUrl;
    }

    public function bNeedClearAll() {
        if ($aParams = Mage::registry('current_session_params')) {
            $bNeedClearAll = false;

            $aNonFilteringParamKeys = $this->getNonFilteringParamKeys();

            foreach ($aParams as $sKey => $sVal) {
                if (!in_array($sKey, $aNonFilteringParamKeys)) {
                    $bNeedClearAll = true;
                }
            }

            return $bNeedClearAll;
        } else {
            return false;
        }

        return true;
    }

    public function getCacheKey($attrCode) {
        $keys = $this->getNonFilteringParamKeys();
        $keys[] = $attrCode;
        return md5($this->getParams(true, $keys));
    }

    protected function getNonFilteringParamKeys() {
        return array('x', 'y', 'mode', 'p', 'order', 'dir', 'limit', 'q', '___store', '___from_store', 'sns');
    }

    public function checkColor($attrColor) {
        $attrColor=str_replace(' ', '', $attrColor);
        $colorArray = array('AliceBlue' => '#F0F8FF',
            'AntiqueWhite' => '#FAEBD7',
            'Aqua' => '#00FFFF',
            'Aquamarine' => '#7FFFD4',
            'Azure' => '#F0FFFF',
            'Beige' => '#F5F5DC',
            'Bisque' => '#FFE4C4',
            'Black' => '#000000',
            'BlanchedAlmond' => '#FFEBCD',
            'Blue' => '#0000FF',
            'BlueViolet' => '#8A2BE2',
            'Brown' => '#A52A2A',
            'BurlyWood' => '#DEB887',
            'CadetBlue' => '#5F9EA0',
            'Chartreuse' => '#7FFF00',
            'Charcoal' => '#36454f',
            'Chocolate' => '#D2691E',
            'Coral' => '#A3481B',
            'Cognac' => '#FF7F50',
            'CornflowerBlue' => '#6495ED',
            'Cornsilk' => '#FFF8DC',
            'Crimson' => '#DC143C',
            'Cyan' => '#00FFFF',
            'DarkBlue' => '#00008B',
            'DarkCyan' => '#008B8B',
            'DarkGoldenRod' => '#B8860B',
            'DarkGray' => '#A9A9A9',
            'DarkGrey' => '#A9A9A9',
            'DarkGreen' => '#006400',
            'DarkKhaki' => '#BDB76B', 
            'DarkMagenta' => '#8B008B',
            'DarkOliveGreen' => '#556B2F',
            'Darkorange' => '#FF8C00',
            'DarkOrchid' => '#9932CC',
            'DarkRed' => '#8B0000',
            'DarkSalmon' => '#E9967A',
            'DarkSeaGreen' => '#8FBC8F',
            'DarkSlateBlue' => '#483D8B',
            'DarkSlateGray' => '#2F4F4F',
            'DarkSlateGrey' => '#2F4F4F',
            'DarkTurquoise' => '#00CED1',
            'DarkViolet' => '#9400D3',
            'DeepPink' => '#FF1493',
            'DeepSkyBlue' => '#00BFFF',
            'DimGray' => '#696969',
            'DimGrey' => '#696969',
            'DodgerBlue' => '#1E90FF',
            'FireBrick' => '#B22222',
            'FloralWhite' => '#FFFAF0',
            'ForestGreen' => '#228B22',
            'Fuchsia' => '#FF00FF',
            'Gainsboro' => '#DCDCDC',
            'GhostWhite' => '#F8F8FF',
            'Gold' => '#FFD700',
            'GoldenRod' => '#DAA520',
            'Gray' => '#808080',
            'Grey' => '#808080',
            'Green' => '#008000',
            'GreenYellow' => '#ADFF2F',
            'HoneyDew' => '#F0FFF0',
            'HotPink' => '#FF69B4',
            'IndianRed ' => '#CD5C5C',
            'Indigo' => '#4B0082',
            'Ivory' => '#FFFFF0',
            'Khaki' => '#F0E68C',
            'Lavender' => '#E6E6FA',
            'LavenderBlush' => '#FFF0F5',
            'LawnGreen' => '#7CFC00',
            'LemonChiffon' => '#FFFACD',
            'LightBlue' => '#ADD8E6',
            'LightCoral' => '#F08080',
            'LightCyan' => '#E0FFFF',
            'LightGoldenRodYellow' => '#FAFAD2',
            'LightGray' => '#D3D3D3',
            'LightGrey' => '#D3D3D3',
            'LightGreen' => '#90EE90',
            'LightPink' => '#FFB6C1',
            'LightSalmon' => '#FFA07A',
            'LightSeaGreen' => '#20B2AA',
            'LightSkyBlue' => '#87CEFA',
            'LightSlateGray' => '#778899',
            'LightSlateGrey' => '#778899',
            'LightSteelBlue' => '#B0C4DE',
            'LightYellow' => '#FFFFE0',
            'Lime' => '#00FF00',
            'LimeGreen' => '#32CD32',
            'Linen' => '#FAF0E6',
            'Magenta' => '#FF00FF',
            'Maroon' => '#800000',
            'MediumAquaMarine' => '#66CDAA',
            'MediumBlue' => '#0000CD',
            'MediumOrchid' => '#BA55D3',
            'MediumPurple' => '#9370D8',
            'MediumSeaGreen' => '#3CB371',
            'MediumSlateBlue' => '#7B68EE',
            'MediumSpringGreen' => '#00FA9A',
            'MediumTurquoise' => '#48D1CC',
            'MediumVioletRed' => '#C71585',
            'MidnightBlue' => '#191970',
            'MintCream' => '#F5FFFA',
            'MistyRose' => '#FFE4E1',
            'Moccasin' => '#FFE4B5',
            'NavajoWhite' => '#FFDEAD',
            'Navy' => '#000080',
            'Oatmeal' => '#E0DCC8',
            'OldLace' => '#FDF5E6',
            'Olive' => '#808000',
            'OliveDrab' => '#6B8E23',
            'Orange' => '#FFA500',
            'OrangeRed' => '#FF4500',
            'Orchid' => '#DA70D6',
            'PaleGoldenRod' => '#EEE8AA',
            'PaleGreen' => '#98FB98',
            'PaleTurquoise' => '#AFEEEE',
            'PaleVioletRed' => '#D87093',
            'PapayaWhip' => '#FFEFD5',
            'PeachPuff' => '#FFDAB9',
            'Peru' => '#CD853F',
            'Pink' => '#FFC0CB',
            'Plum' => '#DDA0DD',
            'PowderBlue' => '#B0E0E6',
            'Purple' => '#800080',
            'Red' => '#FF0000',
            'RosyBrown' => '#BC8F8F',
            'RoyalBlue' => '#4169E1',
            'SaddleBrown' => '#8B4513',
            'Salmon' => '#FA8072',
            'SandyBrown' => '#F4A460',
            'SeaGreen' => '#2E8B57',
            'SeaShell' => '#FFF5EE',
            'Sienna' => '#A0522D',
            'Silver' => '#C0C0C0',
            'SkyBlue' => '#87CEEB',
            'SlateBlue' => '#6A5ACD',
            'SlateGray' => '#708090',
            'SlateGrey' => '#708090',
            'Snow' => '#FFFAFA',
            'SpringGreen' => '#00FF7F',
            'SteelBlue' => '#4682B4',
            'Taupe' => '#483C32',
            'Tan' => '#D2B48C',
            'Teal' => '#008080',
            'Thistle' => '#D8BFD8',
            'Tomato' => '#FF6347',
            'Turquoise' => '#40E0D0',
            'Violet' => '#EE82EE',
            'Wheat' => '#F5DEB3',
            'White' => '#FFFFFF',
            'WhiteSmoke' => '#F5F5F5',
            'Yellow' => '#FFFF00',
            'YellowGreen' => '#9ACD32');

         $colorArr = unserialize(strtolower(serialize($colorArray)));

        if (array_key_exists(strtolower($attrColor), $colorArr)) {
            $key = $colorArr[strtolower($attrColor)];
        } else {
            $key = "";
        }

        return $key;
    }
    public function getcategorydate($category)
    {
      $name=$category->getName();
      return $name;
    }

}
