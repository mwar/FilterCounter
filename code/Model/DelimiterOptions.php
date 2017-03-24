<?php

/**
 * Created by PhpStorm.
 * User: mwarrink
 * Date: 24/03/2017
 * Time: 11:49
 */
class Hc_Filters_Model_DelimiterOptions
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => Mage::helper('filters')->__('Underscore ( _ ) (default)')
            ],
            [
                'value' => 1,
                'label' => Mage::helper('filters')->__('Pipe ( | )')
            ],
            [
                'value' => 2,
                'label' => Mage::helper('filters')->__('Comma ( , )')
            ],
            [
                'value' => 3,
                'label' => Mage::helper('filters')->__('Semicolon ( ; )')
            ],
            [
                'value' => 3,
                'label' => Mage::helper('filters')->__('Array (POST object)')
            ]
        ];

    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
       $return = [];
        foreach($this->toOptionArray() as $option)
        {
            $return[$option['value']] = $option['value'];
        }
        return $return;
    }

    /**
     * Get correct Delimiter
     * @param $level
     * @return mixed
     */
    public function getDelimiter($level) {
        $items = $this->toArray();
        return $items[$level];
    }

    /**
     * Return caontent as array
     * @param $value
     * @return array
     */
    public function arrayOfValues($value)
    {
        $delimeterNr = (Mage::getStoreConfig('catalog/hc/delimeter') > 3)? 0: Mage::getStoreConfig('catalog/hc/delimeter');

        if ($delimeterNr == 3) {
            if (!is_array($value)) {
                return [];
            }
            return (array) $value;
        }

        return explode($this->getDelimiter($delimeterNr), $value);
    }
}