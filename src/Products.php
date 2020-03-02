<?php
namespace DirectScale;
/**
 *	@description	
 */
class Products extends Model
{
	protected	$products;
	/**
	 *	@description	Fetch the product list
	 *	@param	$sku	[varchar|bool]	Use the sku to fetch just that sku. Use ::getBySku() to fetch 
	 *									the sku with non-usd and/or non-english language requirements
	 */
	public	function get($sku = false)
	{
		if($sku)
			return $this->getBySku($sky);
		
		$products	=	$this->getClient()->doGet('products/items');
		
		if(empty($products)) {
			return  $this->products	=	[];
		}
		
		$this->products	=	\Nubersoft\ArrayWorks::organizeByKey(...[
			$this->formatReturn($products),
			'sku',
			[
				'multi' => false,
				'unset' => false
			]
		]);
		
		return $this->products;
	}
	/**
	 *	@description	
	 */
	public	function getBySku($sku, $currency = 'usd', $lang = 'en')
	{
		$settings	=	array_filter([
			'CurrencyCode' => $currency,
			'LanguageCode' => $lang
		], function($v){
			return (!empty($v));
		});
		# Note this produces a fatal error if the currency and lang is empty despite
		# the docs saying they are optional
		try {
			$data	=	$this->getClient()->doGet('products/item/sku/'.$sku, (empty($settings))? false : $settings);
		}
		catch(\DirectScale\Exception $e) {
			return $this->products	=	[];
		}
		$this->products	=	$this->formatReturn($data);
		return $this->products;
	}
}