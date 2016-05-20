<?php

class Fooman_GoogleAnalyticsPlus_Model_Observer
{
    public function addCheckoutStepTracking($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        if ($block instanceof Mage_Checkout_Block_Onepage_Billing) {
            $origBlockContent = $transport->getHtml();
            $trackingJs = $block->getLayout()->createBlock('googleanalyticsplus/ajax')->toHtml();
            $transport->setHtml($trackingJs . $origBlockContent);
        }
    }

    public function setOrder($observer)
    {
        Mage::register('googleanalyticsplus_order_ids', $observer->getEvent()->getOrderIds(), true);
    }

    public function checkoutCartAdd($observer)
    {
	   	$product = Mage::getModel('catalog/product')
	   	 ->load(Mage::app()->getRequest()->getParam('product', 0));

	   	if (!$product->getId()) {
	   		return;
	   	}

	   	$category_name = '';
	   	$categories = $product->getCategoryIds();

	   	if (count($categories)) {
			$count = 0;
	   		foreach ($categories as $k => $firstCategoryId):
	   			if($count>0) {
	   				$category_name .= ' > ';
	   			}
	   			$category_name .= Mage::getModel('catalog/category')->load($firstCategoryId)->getName();
	   			$count++;
	   		endforeach;
	   	}
	   	$price = $product->getData("special_price");
	   	if (!$price) {
	   		$price = $product->getPrice();
	   	}
	   	$price = number_format($price,2,".","");

	   	Mage::getModel('core/session')->setProductToShoppingCart(
	   		new Varien_Object(array(
	   			'id' => $product->getSku(),
	   			'qty' => Mage::app()->getRequest()->getParam('qty', 1),
	   			'name' => $product->getName(),
	   			'price' => $price,
	   			'category_name' => $category_name,
	   		))
	   );
   	}

   	public function addToNewsletter($observer)
   	{
		Mage::getModel('core/session')->setAddedToNewsletter(true);
   	}
}
