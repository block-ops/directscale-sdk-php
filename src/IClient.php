<?php
namespace DirectScale;
/**
 *	@description	
 */
interface IClient
{
	/**
	 *	@description	
	 */
	public	function doService();
	/**
	 *	@description	
	 */
	public	function getErrors();
	/**
	 *	@description	
	 */
	public	function getResponseHeaders();
	/**
	 *	@description	
	 */
	public	function doPost($path, $attr = false, $func = false);
	/**
	 *	@description	
	 */
	public	function doGet($path, $attr = false, $func = false);
	/**
	 *	@description	
	 */
	public	function doDelete($path, $attr = false, $func = false);
	/**
	 *	@description	
	 */
	public	function doPut($path, $attr = false, $func = false);
	/**
	 *	@description	
	 */
	public	function doPatch($path, $attr = false, $func = false);
}