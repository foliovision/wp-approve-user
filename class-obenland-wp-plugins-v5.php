<?php
/**
 * Obenland plugin base class.
 *
 * @author  Konstantin Obenland
 * @version 5
 * @package Obenland Plugins
 */

/**
 * Class Obenland_Wp_Plugins_V5
 */
class Obenland_Wp_Plugins_V5 {

	/**
	 * The plugins' text domain.
	 *
	 * @author Konstantin Obenland
	 * @since  1.1 - 03.04.2011
	 * @access protected
	 *
	 * @var    string
	 */
	protected $textdomain;

	/**
	 * The name of the calling plugin.
	 *
	 * @author Konstantin Obenland
	 * @since  1.0 - 23.03.2011
	 * @access protected
	 *
	 * @var    string
	 */
	protected $plugin_name;

	/**
	 * The donate link for the plugin.
	 *
	 * @author Konstantin Obenland
	 * @since  1.0 - 23.03.2011
	 * @access protected
	 *
	 * @var    string
	 */
	protected $donate_link;

	/**
	 * The path to the plugin file.
	 *
	 * /path/to/wp-content/plugins/{plugin-name}/{plugin-name}.php
	 *
	 * @author Konstantin Obenland
	 * @since  2.0.0 - 30.05.2012
	 * @access protected
	 *
	 * @var    string
	 */
	protected $plugin_path;

	/**
	 * The path to the plugin directory.
	 *
	 * /path/to/wp-content/plugins/{plugin-name}/
	 *
	 * @author Konstantin Obenland
	 * @since  1.2 - 21.04.2011
	 * @access protected
	 *
	 * @var    string
	 */
	protected $plugin_dir_path;

	/**
	 * Constructor
	 *
	 * @author Konstantin Obenland
	 * @since  1.0 - 23.03.2011
	 * @access public
	 *
	 * @param  array $args {.
	 *      @type string $textdomain
	 *      @type string $plugin_name
	 *      @type string $plugin_path
	 *      @type string $donate_link_id
	 * }
	 */
	public function __construct( $args = array() ) {

		// Set class properties.
		$this->textdomain      = $args['textdomain'];
		$this->plugin_path     = $args['plugin_path'];
		$this->plugin_dir_path = plugin_dir_path( $args['plugin_path'] );
		$this->plugin_name     = plugin_basename( $args['plugin_path'] );

		load_plugin_textdomain( 'obenland-wp', false, $this->textdomain . '/lang' );

		$this->set_donate_link( $args['donate_link_id'] );
		$this->hook( 'plugins_loaded', 'parent_plugins_loaded' );
	}

	/**
	 * Hooks in all the hooks :)
	 *
	 * @author Konstantin Obenland
	 * @since  2.0.0 - 12.04.2012
	 * @access public
	 */
	public function parent_plugins_loaded() {
		$this->hook( 'plugin_row_meta' );
	}

	/**
	 * Adds a Donate link to our plugin row.
	 *
	 * @author Konstantin Obenland
	 * @since  1.0 - 23.03.2011
	 * @access public
	 *
	 * @param  array  $plugin_meta Existing plugin meta.
	 * @param  string $plugin_file Plugin slug.
	 * @return array
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( $this->plugin_name === $plugin_file ) {
			$plugin_meta[] = sprintf(
				'<a href="%1$s" target="_blank" title="%2$s">%2$s</a>',
				$this->donate_link,
				__( 'Donate', 'obenland-wp' )
			);
		}

		return $plugin_meta;
	}

	/**
	 * Hooks methods to their WordPress Actions and Filters.
	 *
	 * @example:
	 * $this->hook( 'the_title' );
	 * $this->hook( 'init', 5 );
	 * $this->hook( 'omg', 'is_really_tedious', 3 );
	 *
	 * @author Mark Jaquith
	 * @see    http://sliwww.slideshare.net/markjaquith/creating-and-maintaining-wordpress-plugins
	 * @since  1.5 - 12.02.2012
	 * @access protected
	 *
	 * @param  string $hook Action or Filter Hook name.
	 *
	 * @return boolean true
	 */
	protected function hook( $hook ) {
		$priority = 10;
		$method   = $this->sanitize_method( $hook );
		$args     = func_get_args();
		unset( $args[0] ); // Filter name.

		foreach ( (array) $args as $arg ) {
			if ( is_int( $arg ) ) {
				$priority = $arg;
			} else {
				$method = $arg;
			}
		}

		return add_action( $hook, array( $this, $method ), $priority, 999 );
	}

	/**
	 * Sets the donate link.
	 *
	 * @author Konstantin Obenland
	 * @since  1.1 - 03.04.2011
	 * @access protected
	 *
	 * @param  string $donate_link_id Donate link ID.
	 */
	protected function set_donate_link( $donate_link_id ) {
		$this->donate_link = add_query_arg(
			array(
				'cmd'              => '_s-xclick',
				'hosted_button_id' => $donate_link_id,
			),
			'https://www.paypal.com/cgi-bin/webscr'
		);
	}

	/**
	 * Sanitizes method names.
	 *
	 * @author Mark Jaquith
	 * @see    http://sliwww.slideshare.net/markjaquith/creating-and-maintaining-wordpress-plugins
	 * @since  1.5 - 12.02.2012
	 * @access private
	 *
	 * @param  string $method Method name to be sanitized.
	 *
	 * @return string Sanitized method name
	 */
	private function sanitize_method( $method ) {
		return str_replace( array( '.', '-' ), '_', $method );
	}
}
