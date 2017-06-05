<?php


namespace calderawp\calderaforms\pro\api\local;
use calderawp\calderaforms\pro\container;


/**
 * Class route for settings save/get
 *
 * Local REST API route
 * @package calderawp\calderaforms\pro\api\local
 */
class settings implements \Caldera_Forms_API_Route {

	/**
	 * @inheritdoc
	 */
	public function add_routes( $namespace ) {
		register_rest_route( $namespace, '/settings/pro',
			array(
				'methods'         => 'POST',
				'callback'        => array( $this, 'update_settings' ),
				'args'            => array(
					'accountId' => array(
						'type'                  => 'integer',
						'required'              => false,
						'default'               => 0,
						'sanatization_callback' => 'absint'
					),
					'apiKey'  => array(
						'type'                  => 'string',
						'required'              => false,
					),
					'apiSecret' => array(
						'type'                  => 'string',
						'required'              => false,
					),
					'generatePDFs' => array(
						'type'                  => 'boolean',
						'required'              => false,
						'sanatization_callback' => 'rest_sanitize_boolean'
					),
					'enhancedDelivery' => array(
						'type'                  => 'boolean',
						'required'              => false,
						'sanatization_callback' => 'rest_sanitize_boolean'
					)
				),
				'permissions_callback' => array( $this, 'permissions' )
			)
		);
		register_rest_route( $namespace, '/settings/pro',
			array(
				'methods'         => 'GET',
				'callback'        => array( $this, 'get_settings' ),
				'args'            => array(

				),
				'permissions_callback' => array( $this, 'permissions' )
			)
		);
	}

	/**
	 *Check request permissions
	 *
	 * @return bool
	 */
	public function permissions(){
		return current_user_can( \Caldera_Forms::get_manage_cap( 'admin' ) );
	}

	/**
	 * Update settings via WP REST API
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function update_settings( \WP_REST_Request $request ){
		$settings = container::get_instance()->get_settings();
		if( ! empty( $request[ 'accountId' ] ) ){
			$settings->set_account_id(  $request[ 'accountId' ] );
		}

		if( ! empty( $request[ 'apiKey' ] ) ){
			$settings->set_api_public( $request[ 'apiKey' ] );
		}

		if( ! empty( $request[ 'apiSecret' ] ) ){
			$settings->set_api_secret( $request[ 'apiSecret' ] );
		}

		if( ! empty( $request[ 'forms' ] ) ){
			foreach ( $request[ 'forms' ] as $form ){
				$this->handle_form( $form );
			}
		}

		if( false === $request[ 'enhancedDelivery' ]  ){
			$settings->set_enhanced_delivery( 'false' );
		}

		if( true === $request[ 'enhancedDelivery' ]  ){
			$settings->set_enhanced_delivery( 'true' );
		}

		$settings->save();

		return rest_ensure_response( container::get_instance()->get_settings()->toArray() );

	}

	/**
	 * Handle saving settings of a form
	 *
	 * @param array $form
	 */
	protected function handle_form( array $form ){
		$setting = container::get_instance()->get_settings()->get_form( $form[ 'form_id' ] );
		foreach ( $setting->get_properties() as $property ){
			if( isset( $form[ $property ] ) ){
				$setting->$property = $form[ $property ];
			}
		}

		$setting->save();

	}

	/**
	 * Read settings via WP REST API
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_settings( \WP_REST_Request $request ){
		return rest_ensure_response( container::get_instance()->get_settings()->toArray() );

	}
}