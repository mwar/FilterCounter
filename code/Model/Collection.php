<?php

/**
 * Created by PhpStorm.
 * User: mwarrink
 * Date: 24/03/2017
 * Time: 08:37
 */
class Hc_Filters_Model_Collection extends Mage_Core_Model_Abstract
{
    protected $_collection;
    protected $_availableAttributes;

    public function _construct()
    {
        $this->setAvailableFilters();
    }


    /**
     * Load Product Collection;
     * @param int $categoryId
     * @return mixed
     */
    public function LoadProductCollection($categoryId) {
        if (is_null($categoryId)) {
            return false;
        }


        $products = Mage::getModel('catalog/category')->load($categoryId)
            ->getProductCollection()
            ->addAttributeToSelect($this->getAvailableFilters()) // add all attributes - optional
            ->addAttributeToFilter('status', 1) // enabled
            ->addAttributeToFilter('visibility', 4);

        $this->_collection = $products;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection|Bool;
     */
    public function getProductCollection()
    {
        return empty($this->_collection)? false: $this->_collection;
    }

    /**
     * Setter for Available Filters
     */
    protected function setAvailableFilters()
    {
        /** @var Mage_Customer_Model_Resource_Attribute_Collection $attributeCollection */
        $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('is_filterable', true);


        $attributes = [];
        foreach($attributeCollection as $attribute) {
            $attributes[] = $attribute->getAttributeCode();
        }
        $this->_availableAttributes = $attributes;
    }

    /**
     * Get the attribute codes that can be used for filtering
     * @return array;
     */
    public function getAvailableFilters()
    {
        return $this->_availableAttributes;
    }

}