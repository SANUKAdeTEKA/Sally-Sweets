<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2024
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Supplier;

sprintf( 'goods' ); // for translation
sprintf( 'supplier' ); // for translation


/**
 * Default implementation of supplier JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/** admin/jqadm/supplier/name
	 * Class name of the used account favorite client implementation
	 *
	 * Each default admin client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Admin\JQAdm\Supplier\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Admin\JQAdm\Supplier\Myfavorite
	 *
	 * then you have to set the this configuration option:
	 *
	 *  admin/jqadm/supplier/name = Myfavorite
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyFavorite"!
	 *
	 * @param string Last part of the class name
	 * @since 2017.10
	 */


	/**
	 * Adds the required data used in the template
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\Base\View\Iface View object with assigned parameters
	 */
	public function data( \Aimeos\Base\View\Iface $view ) : \Aimeos\Base\View\Iface
	{
		$codes = [];

		foreach( $this->context()->config()->get( 'common/countries', [] ) as $code ) {
			$codes[$code] = $view->translate( 'country', $code );
		}

		asort( $codes );

		$view->itemSubparts = $this->getSubClientNames();
		$view->countries = $codes;
		return $view;
	}


	/**
	 * Batch update of a resource
	 *
	 * @return string|null Output to display
	 */
	public function batch() : ?string
	{
		return $this->batchBase( 'supplier' );
	}


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output
	 */
	public function copy() : ?string
	{
		$view = $this->object()->data( $this->view() );

		try
		{
			if( ( $id = $view->param( 'id' ) ) === null )
			{
				$msg = $this->context()->translate( 'admin', 'Required parameter "%1$s" is missing' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, 'id' ) );
			}

			$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );
			$view->item = $manager->get( $id, $this->getDomains() );

			$view->itemData = $this->toArray( $view->item, true );
			$view->itemBody = parent::copy();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'copy' );
		}

		return $this->render( $view );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string|null HTML output
	 */
	public function create() : ?string
	{
		$view = $this->object()->data( $this->view() );

		try
		{
			$data = $view->param( 'item', [] );

			if( !isset( $view->item ) ) {
				$view->item = \Aimeos\MShop::create( $this->context(), 'supplier' )->create();
			}

			$data['supplier.siteid'] = $view->item->getSiteId();

			$view->itemData = array_replace_recursive( $this->toArray( $view->item ), $data );
			$view->itemBody = parent::create();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'create' );
		}

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output
	 */
	public function delete() : ?string
	{
		$view = $this->view();

		$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );
		$manager->begin();

		try
		{
			if( ( $ids = $view->param( 'id' ) ) === null )
			{
				$msg = $this->context()->translate( 'admin', 'Required parameter "%1$s" is missing' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, 'id' ) );
			}

			$search = $manager->filter()->add( 'supplier.id', '==', $ids )->slice( 0, count( (array) $ids ) );
			$items = $manager->search( $search, $this->getDomains() );

			foreach( $items as $item )
			{
				$view->item = $item;
				parent::delete();
			}

			$manager->delete( $items->toArray() );
			$manager->commit();

			return $this->redirect( 'supplier', 'search', null, 'delete' );
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			$this->report( $e, 'delete' );
		}


		return $this->search();
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output
	 */
	public function get() : ?string
	{
		$view = $this->object()->data( $this->view() );

		try
		{
			if( ( $id = $view->param( 'id' ) ) === null )
			{
				$msg = $this->context()->translate( 'admin', 'Required parameter "%1$s" is missing' );
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( $msg, 'id' ) );
			}

			$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );

			$view->item = $manager->get( $id, $this->getDomains() );
			$view->itemData = $this->toArray( $view->item );
			$view->itemBody = parent::get();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'get' );
		}

		return $this->render( $view );
	}


	/**
	 * Saves the data
	 *
	 * @return string|null HTML output
	 */
	public function save() : ?string
	{
		$view = $this->view();

		$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );
		$manager->begin();

		try
		{
			$item = $this->fromArray( $view->param( 'item', [] ) );
			$view->item = $item->getId() ? $item : $manager->save( $item );
			$view->itemBody = parent::save();

			$manager->save( clone $view->item );
			$manager->commit();

			return $this->redirect( 'supplier', $view->param( 'next' ), $view->item->getId(), 'save' );
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			$this->report( $e, 'save' );
		}

		return $this->create();
	}


	/**
	 * Returns a list of resource according to the conditions
	 *
	 * @return string|null HTML output
	 */
	public function search() : ?string
	{
		$view = $this->view();

		try
		{
			$total = 0;
			$params = $this->storeFilter( $view->param(), 'supplier' );
			$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );
			$search = $this->initCriteria( $manager->filter(), $params );

			$view->items = $manager->search( $search, $this->getDomains(), $total );
			$view->filterAttributes = $manager->getSearchAttributes( true );
			$view->filterOperators = $search->getOperators();
			$view->itemBody = parent::search();
			$view->total = $total;
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'search' );
		}

		/** admin/jqadm/supplier/template-list
		 * Relative path to the HTML body template for the supplier list.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in templates/admin/jqadm).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2016.04
		 */
		$tplconf = 'admin/jqadm/supplier/template-list';
		$default = 'supplier/list';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Admin\JQAdm\Iface
	{
		/** admin/jqadm/supplier/decorators/excludes
		 * Excludes decorators added by the "common" option from the supplier JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/supplier/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "client/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.10
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/supplier/decorators/global
		 * @see admin/jqadm/supplier/decorators/local
		 */

		/** admin/jqadm/supplier/decorators/global
		 * Adds a list of globally available decorators only to the supplier JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/supplier/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.10
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/supplier/decorators/excludes
		 * @see admin/jqadm/supplier/decorators/local
		 */

		/** admin/jqadm/supplier/decorators/local
		 * Adds a list of local decorators only to the supplier JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Supplier\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/supplier/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Supplier\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.10
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/supplier/decorators/excludes
		 * @see admin/jqadm/supplier/decorators/global
		 */
		return $this->createSubClient( 'supplier/' . $type, $name );
	}


	/**
	 * Returns the domain names whose items should be fetched too
	 *
	 * @return string[] List of domain names
	 */
	protected function getDomains() : array
	{
		/** admin/jqadm/supplier/domains
		 * List of domain items that should be fetched along with the supplier
		 *
		 * If you need to display additional content, you can configure your own
		 * list of domains (attribute, media, price, supplier, text, etc. are
		 * domains) whose items are fetched from the storage.
		 *
		 * @param array List of domain names
		 * @since 2017.10
		 */
		return $this->context()->config()->get( 'admin/jqadm/supplier/domains', [] );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames() : array
	{
		/** admin/jqadm/supplier/subparts
		 * List of JQAdm sub-clients rendered within the supplier section
		 *
		 * The output of the frontend is composed of the code generated by the JQAdm
		 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
		 * that are responsible for rendering certain sub-parts of the output. The
		 * sub-clients can contain JQAdm clients themselves and therefore a
		 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
		 * the output that is placed inside the container of its parent.
		 *
		 * At first, always the JQAdm code generated by the parent is printed, then
		 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
		 * determines the order of the output of these sub-clients inside the parent
		 * container. If the configured list of clients is
		 *
		 *  array( "subclient1", "subclient2" )
		 *
		 * you can easily change the order of the output by reordering the subparts:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
		 *
		 * You can also remove one or more parts if they shouldn't be rendered:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1" )
		 *
		 * As the clients only generates structural JQAdm, the layout defined via CSS
		 * should support adding, removing or reordering content by a fluid like
		 * design.
		 *
		 * @param array List of sub-client names
		 * @since 2017.10
		 */
		return $this->context()->config()->get( 'admin/jqadm/supplier/subparts', [] );
	}



	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param array $data Data array
	 * @return \Aimeos\MShop\Supplier\Item\Iface New supplier item object
	 */
	protected function fromArray( array $data ) : \Aimeos\MShop\Supplier\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );

		if( isset( $data['supplier.id'] ) && $data['supplier.id'] != '' ) {
			$item = $manager->get( $data['supplier.id'], $this->getDomains() );
		} else {
			$item = $manager->create();
		}

		$item->fromArray( $data, true );

		return $item;
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Supplier\Item\Iface $item Supplier item object
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Supplier\Item\Iface $item, bool $copy = false ) : array
	{
		$data = $item->toArray( true );

		if( $copy === true )
		{
			$data['supplier.siteid'] = $this->context()->locale()->getSiteId();
			$data['supplier.code'] = $data['supplier.code'] . '_' . substr( md5( microtime( true ) ), -5 );
			$data['supplier.id'] = '';
		}

		return $data;
	}


	/**
	 * Returns the rendered template including the view data
	 *
	 * @param \Aimeos\Base\View\Iface $view View object with data assigned
	 * @return string HTML output
	 */
	protected function render( \Aimeos\Base\View\Iface $view ) : string
	{
		/** admin/jqadm/supplier/template-item
		 * Relative path to the HTML body template for the supplier item.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in templates/admin/jqadm).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2016.04
		 */
		$tplconf = 'admin/jqadm/supplier/template-item';
		$default = 'supplier/item';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}