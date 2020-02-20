<?php
namespace DirectScale;
/**
 *	@description	
 */
class Products extends Model
{
	protected	$products;
	/**
	 *	@description	
	 */
	public	function get($sku = false)
	{
		$products	=	$this->doGet('products/items');
		
		if(empty($products)) {
			return  $this->products	=	[];
		}
		
		$this->products	=	\Nubersoft\ArrayWorks::organizeByKey($this->formatReturn($products), 'sku', [
			'multi' => false,
			'unset' => false
		]);
		
		if($sku)
			return (isset($this->products[$sku]))? $this->products[$sku] : false;
		
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
			$data	=	$this->doGet('products/item/sku/'.$sku, (empty($settings))? false : $settings);
		}
		catch(\DirectScale\Exception $e) {
			return $this->products	=	[];
		}
		$this->products	=	$this->formatReturn($data);
		return $this->products;
	}
}