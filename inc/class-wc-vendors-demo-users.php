<?php
/**
 * Users Class
 */

class WC_DM_USER {
	/**
	 * Plugin file
	 *
	 * @var $plugin Plugin file.
	 */
	private $plugin;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin = WP_DM_PLUGIN_URL;
		add_action( 'init', array( $this, 'create_user' ) );
	}

	/**
	 * Load user json
	 */
	public function load_json_file() {
		$path = trailingslashit( $this->plugin ) . 'data-sample/users.json';
		$file = wp_remote_get( $path );
		$json = wp_remote_retrieve_body( $file );
		return json_decode( $json, true );
	}

	/**
	 * Check user exist
	 *
	 * @param string $user_name User name.
	 */
	public function check_user( $user_name ) {
		return username_exists( $user_name );
	}

	/**
	 * Create users.
	 */
	public function create_user() {
		$users = $this->load_json_file();

		if ( ! $users ) {
			return;
		}
		$inserted = array();
		foreach ( $users as $user_name => $user ) {

			if ( ! $this->check_user( $user_name ) ) {
				$passowrd   = $user['password'];
				$email      = $user['email'];
				$user_id    = wp_create_user( $user_name, $passowrd, $email );
				$inserted[] = $user_id;
				$done_user  = new WP_User( $user_id );
				$done_user->add_role( 'vendor' );
				$done_user->remove_role( 'subscriber' );
				wp_update_user(
					array(
						'ID'        => $user_id,
						'last_name' => $user['name'],
					)
				);
			}
			// } else {
			// $ex_user = get_user_by( 'login', $user_name );
			// $role    = $ex_user->roles;
			// if ( ! in_array( 'vendor', $role ) ) {
			// $ex_user->add_role( 'vendor' );
			// $ex_user->remove_role( 'subscriber' );
			// }
			// }
		}
		if ( ! empty( $inserted ) ) {
			do_action( 'wc_dm_after_create_user', $inserted );
		}

	}

}

return new WC_DM_USER();
