<?php

/**
 * With this controller you can check how many products ar available when you select (multiple) filters
 */
class Hc_Filters_CountController extends Mage_Core_Controller_Front_Action
{

    protected $_collection;
    protected $_availableAttributes;
    protected $_collectionModel;
    protected $_usedFilters = [];

    public function _construct()
    {
        /** @var Hc_Filters_Model_Collection _collectionModel */
        $this->_collectionModel =  Mage::getModel('filters/collection');
    }


    /**
     * Count product, based on filters;
     */
    public function countAction()
    {
        if (is_null($this->getRequest()->getParam('category'))) {
            $this->respondJson(['statuscode' => '401']);
        }

        $collection = $this->getCollectionModel()
            ->loadProductCollection(19/*$this->getRequest()->getParam('category')*/)
            ->getProductCollection();

        $this->addFiltersToCollection($collection);

        $jsonData = [
            'statuscode'    => '200',
            'category_id'   => $this->getRequest()->getParam('category'),
            'products_count'=>   count($collection),
            'filters_used'  => $this->_usedFilters,
        ];
        $this->respondJson($jsonData);
    }

    /**
     * Add the filters to the collection
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     */
    protected function addFiltersToCollection(Mage_Catalog_Model_Resource_Product_Collection &$collection)
    {

        $availableFilters = $this->getCollectionModel()->getAvailableFilters();
        foreach($this->getRequest()->getParams() as $attribute => $content) {
            if (!in_array($attribute, $availableFilters)) {
                continue;
            }
            $filters = [];
            $values = Mage::getModel('filters/delimiterOptions')->arrayOfValues($content);

            /** Price Can be a range, but also only a maximum or minimum */
            if ($attribute == 'price') {

                foreach($values as $value):
                    if (substr($value, 0, 1) == '-') {
                        $filters[] = ['lteq' => substr($value, 0, 1)];
                    } elseif(substr($value, -1) == '-') {
                        $filters[] = ['gteq' => substr($value, -1)];
                    } elseif (strpos($value, "-") !== false) {
                        $prices = explode("-", $value, 2);
                        $filters[] = [
                            'from'  => $prices[0],
                            'to'  => $prices[1],
                        ];
                    } else {
                        $filters[] = ['eq' => $value];
                    }

                endforeach;
            } else {
                $filters['eq'] = $values;
            }
            /* for return message */
            $this->usedFilters($attribute, $filters);
            $collection->addAttributeToFilter($attribute, $filters);
        }
    }


    /**
     * set filters for return message;
     * @param string $attribute
     * @param array|string $filters
     */
    protected function usedFilters($attribute, $filters)
    {
        $this->_usedFilters[$attribute] = $filters;
    }

    /**
     * @return Hc_Filters_Model_Collection
     */
    protected function getCollectionModel() {
        return $this->_collectionModel;
    }

    /**
     * Print JSON to screen
     * @param array $response
     */
    protected function respondJson(array $response) {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
    }
}