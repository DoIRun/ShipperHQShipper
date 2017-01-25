<?php


namespace DoIRun\ShipperHQShipper\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product\Configuration;

class Processor
{
    /**
     * @var \ShipperHQ\Shipper\Helper\LogAssist
     */
    private $shipperLogger;
    /**
     * @var Configuration
     */
    private $productConfiguration;
    /**
     * @var ProductRepositoryInterface
     */
    private $_productRepositoryInterface;

    public  function __construct(
        \ShipperHQ\Shipper\Helper\LogAssist $shipperLogger,
        Configuration $productConfiguration,
        ProductRepositoryInterface $productRepositoryInterface
    )
    {
        $this->shipperLogger = $shipperLogger;
        $this->productConfiguration = $productConfiguration;
        $this->_productRepositoryInterface = $productRepositoryInterface;
    }

    public function aroundPopulateAttributes(\ShipperHQ\Shipper\Model\Carrier\Processor\ShipperMapper $shipperMapper, callable $proceed, $reqAttributes, $item) {
        $results = $proceed($reqAttributes, $item);
        $results = $this->getCustomOptionsDimensions($results, $item);
        return $results;
    }
    /**
     * @param $reqAttributes
     * @param $item
     * @return array
     */
    protected function getCustomOptionsDimensions($reqAttributes, \Magento\Quote\Model\Quote\Item $item) {
        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $product */
        $product = $this->_productRepositoryInterface->getById($item->getProduct()->getId());
        if($product->getData('doirun_dim_use_options') && count($reqAttributes)>0) {
            $options = $this->productConfiguration->getCustomOptions($item);
            $optionvalues = [];
            foreach ($options as $customOption) {
                $optionvalues[$customOption['label']] = $customOption['value'];
            }
            $attribute['ship_height'] = $this->processOptionDimension($optionvalues,$product->getData('doirun_dim_options_height'));
            $attribute['ship_width'] = $this->processOptionDimension($optionvalues,$product->getData('doirun_dim_options_width'));
            $attribute['ship_length'] = $this->processOptionDimension($optionvalues,$product->getData('doirun_dim_options_length'));
            for ($i=0;$i<count($reqAttributes);++$i) {
                if(isset($attribute[$reqAttributes[$i]['name']]) && $attribute[$reqAttributes[$i]['name']]>0) {
                    $reqAttributes[$i]['value']=$attribute[$reqAttributes[$i]['name']];
                }
            }
        }
        return $reqAttributes;
    }

    /**
     * @param $optionvalues
     * @param $formula
     * @return float
     */
    protected function processOptionDimension($optionvalues, $formula) {
        preg_match_all('/\{([\w\h]+)\}/',$formula,$optiontitles);
        $optiontitles = array_unique($optiontitles[1]);
        $optiontitles = array_values($optiontitles);
        foreach ($optiontitles as $title) {
            if(isset($optionvalues[$title])) {
                if(preg_match('/[A-z]/',$optionvalues[$title])) {
                    $formula = preg_replace('/\{' . $title . '\}/', "'".$optionvalues[$title]."'", $formula);
                }
                $formula = preg_replace('/\{' . $title . '\}/', $optionvalues[$title], $formula);
            }
        }
        if(preg_match('/\{[\w\h]+\}/',$formula)) {
            $formula = preg_replace('/\{[\w\h]+\}/', '\'0\'', $formula);
        }
        $dim = 0;
        $this->shipperLogger->postDebug('DoIRun_ShipperHQShipper','Formula to Parse',$formula);
        $formulaFunc = create_function('&$dim', '$dim = ' . $formula . ';');
        $formulaFunc($dim);
        $dim = floatval($dim);
        $this->shipperLogger->postDebug('DoIRun_ShipperHQShipper','Returned Dim:',$dim);
        return $dim;
    }

}