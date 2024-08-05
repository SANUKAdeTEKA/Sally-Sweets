<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Factory;


/**
 * Rule factory interface for dealing with run-time loadable extensions.
 *
 * @package MShop
 * @subpackage Rule
 */
interface Iface
{
	/**
	 * Initializes the rule object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 * @return null
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Rule\Item\Iface $item );
}
