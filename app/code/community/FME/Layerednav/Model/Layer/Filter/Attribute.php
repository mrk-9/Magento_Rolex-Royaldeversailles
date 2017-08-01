<?php
/**
* FME Layered Navigation 
* 
* @category     FME 
* @package      FME_Layerednav 
* @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
* @author       FME (Kamran Rafiq Malik)  
* @version      Release: 1.0.0
* @Class        FME_Layerednav_Model_Layer_Filter_Attribute  
*/ 
class FME_Layerednav_Model_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
		
        $filter = Mage::helper('layerednav')->getParam($this->_requestVar);
        $filter = explode('-', $filter);
        
        $ids = array();    
        foreach ($filter as $id){
            $id = intVal($id);
            if ($id)
                $ids[] = $id;    
        } 
        if ($ids){
            $this->applyMultipleValuesFilter($ids);     
        }
        
        $this->setActiveState($ids);
        return $this;
    }

    protected function applyMultipleValuesFilter($ids)
    {
        $collection = $this->getLayer()->getProductCollection();
        $attribute  = $this->getAttributeModel();
        $table = Mage::getSingleton('core/resource')->getTableName('catalogindex/eav'); 
        
        $alias = 'attr_index_'.$attribute->getId();
        $collection->getSelect()->join(
            array($alias => $table),
            $alias.'.entity_id=e.entity_id',
            array()
        )
        ->where($alias.'.store_id = ?', Mage::app()->getStore()->getId())
        ->where($alias.'.attribute_id = ?', $attribute->getId())
        ->where($alias.'.value IN (?)', $ids);
        if (count($ids)>1){
            $collection->getSelect()->distinct(true); 
        }
        
        return $this;
    }   
    
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey();
        $key .= Mage::helper('layerednav')->getCacheKey($this->_requestVar);
        
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $data = array();
            
            $options = $attribute->getFrontend()->getSelectOptions();
            
            $optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount(
                $attribute,
                $this->_getBaseCollectionSql()
            );
            
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($attribute->getIsFilterable() == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    }
                    else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }

            
             $currentIds = Mage::helper('layerednav')->getParam($attribute->getAttributeCode());
            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $currentIds,
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
    
    protected function _getBaseCollectionSql()
    {
         $alias = 'attr_index_' . $this->getAttributeModel()->getId(); 
        // Varien_Db_Select
        $baseSelect = clone parent::_getBaseCollectionSql();
        
        // 1) remove from conditions
        $oldWhere = $baseSelect->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();

        foreach ($oldWhere as $cond){
           if (!strpos($cond, $alias))
               $newWhere[] = $cond;
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
           $newWhere[0] = substr($newWhere[0],3);        
        
        $baseSelect->setPart(Varien_Db_Select::WHERE, $newWhere);
        
        // 2) remove from joins
        $oldFrom = $baseSelect->getPart(Varien_Db_Select::FROM);
        $newFrom = array();
        
        foreach ($oldFrom as $name=>$val){
           if ($name != $alias)
               $newFrom[$name] = $val;
        }
        //it assumes we have at least one table 
        $baseSelect->setPart(Varien_Db_Select::FROM, $newFrom);

        return $baseSelect;
    }
    
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('catalog/layer_filter_attribute');
        }
        return $this->_resource;
    }
    
} 