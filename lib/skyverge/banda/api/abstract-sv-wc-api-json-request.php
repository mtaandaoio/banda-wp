<?php
/**
 * Banda Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/Banda/API/Request
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_API_JSON_Request' ) ) :

/**
 * Base JSON API request class.
 *
 * @since 4.3.0
 */
abstract class SV_WC_API_JSON_Request implements SV_WC_API_Request {


	/** @var string The request method, one of HEAD, GET, PUT, PATCH, POST, DELETE */
	protected $method;

	/** @var string The request path */
	protected $path;

	/** @var array The request parameters, if any */
	protected $params = array();


	/**
	 * Get the request method.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::get_method()
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}


	/**
	 * Get the request path.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::get_path()
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 * Get the request parameters.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}


	/** API Helper Methods ******************************************************/


	/**
	 * Get the string representation of this request.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::to_string()
	 * @return string
	 */
	public function to_string() {

		return json_encode( $this->get_params() );
	}


	/**
	 * Get the string representation of this request with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::to_string_safe()
	 * @return string
	 */
	public function to_string_safe() {

		return $this->to_string();
	}


}

endif; // class exists check
