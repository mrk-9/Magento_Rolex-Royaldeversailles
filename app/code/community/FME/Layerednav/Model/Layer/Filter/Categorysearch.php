<?php
/**
* FME Layered Navigation 
* 
* @category     FME
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Model_Layer_Filter_Categorysearch  
*/ 

class FME_Layerednav_Model_Layer_Filter_Categorysearch extends Mage_Catalog_Model_Layer_Filter_Category
{
    protected function _getItemsData()
    {
        $key = $this->getLayer()->getStateKey().'_SEARCH_SUBCATEGORIES';
        $key .= Mage::helper('layerednav')->getCacheKey('cat');
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $category   = $this->getCategory();
            
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $category->getChildrenCategories();

            $data = array();
            $level = 0;
            if ($category->getLevel() > 1){ // current category is not root
                $parent = $category->getParentCategory();
                
                ++$level;
                if ($parent->getLevel()>1){
                    $data[] = array(
                        'label' => $parent->getName(),
                        'value' => $parent->getId(),
                        'count' => 0,
                        'level' => $level,
                        'uri'   => $queryStr,
                    );

                    
                }
                //always include current category
                ++$level;
                $data[] = array(
                    'label' => $category->getName(),
                    'value' => '',
                    'level' => $level,
                    'is_current' => true,
                    'uri'   => $queryStr,
                );
            }
             
            $this->getLayer()->getProductCollection()
                ->addCountToCategories($categories);
                
            if ($parentId){
                $data[0]['count'] = $parent->getProductCount();
                $categories->removeItemByKey($parentId);
            }    
            
            ++$level;
            foreach ($categories as $cat) {
                if ($cat->getIsActive() && $cat->getProductCount()) {
                     $data[] = array( 
                        'label'       => $cat->getName(),
                        'value'       => $cat->getId(), 
                        'count'       => $cat->getProductCount(),
                        'level'       => $level,
                        'category_id' => $cat->getId(),
                        'uri'         => $cat->getUrl(),
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
 if (Mage::getStoreConfig('layerednav/layerednav/reset_filters'))
{
    $queryStr = '';
}
    $pageKey  = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
    $queryStr =  Mage::helper('layerednav')->getParams(true, $pageKey);            
            for ($i=0, $n=sizeof($data); $i<$n; ++$i) {
                $url = $data[$i]['uri'];
                $pos = strpos($url, '?');
                if ($pos)
                    $url = substr($url, 0, $pos);
                 $data[$i]['uri'] = $url . $queryStr;
            }
        return $data;
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $obj = Mage::getModel('catalog/layer_filter_item');
            $obj->setData($itemData);
            $obj->setFilter($this);
            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }    
} 