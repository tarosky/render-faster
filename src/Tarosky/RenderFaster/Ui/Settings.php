<?php

namespace Tarosky\RenderFaster\Ui;

use Tarosky\RenderFaster\Pattern\Singleton;
use Tarosky\RenderFaster\Services\LazyLoader;

/**
 * Setting screen.
 *
 * @package render-faster
 */
class Settings extends Singleton {

	/**
	 * Constructor.
	 */
	protected function init() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'template_redirect', [ $this, 'filter_image' ] );
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		$lazy_loader = LazyLoader::get_instance();
		// Image optimization.
		add_settings_section( 'render_faster_lazy_loading_section', __( 'Lazy Loading', 'render-faster' ), function() {
			printf(
				'<p class="description">%s <code>loading=&quot;lazy&quot;</code></p>',
				esc_html__( 'Add native loading options to HTML tags in all pages:', 'render-faster' )
			);
		}, 'render-faster' );
		// Image & iframe.
		foreach ( [
			'image'  => __( 'Images', 'render-faster' ),
			'iframe' => __( 'iframe', 'render-faster' ),
		] as $key => $label ) {
			$option_name = $lazy_loader->get_feature_option_key( $key );
			add_settings_field( $option_name, $label, function() use ( $key, $option_name, $lazy_loader ) {
				$defined   = $lazy_loader->get_defined_value( $key );
				$is_active = $lazy_loader->get_option( $key );
				foreach ( [
					[ __( 'Enabled', 'render-faster' ), true, '1' ],
					[ __( 'Disabled', 'render-faster' ), false, '' ],
				] as list( $label, $bool, $value ) ) {
					?>
					<p>
						<label>
							<input type="radio" name="<?php echo esc_attr( $option_name ); ?>" value="<?php echo esc_attr( $value ) ?>" <?php checked( $bool, $is_active ) ?> />
							<?php echo esc_html( $label ) ?>
						</label>
					</p>
					<?php
				}
				if ( ! is_null( $defined ) ) {
					printf(
						'<p class="description">%s <code>%s</code> = <strong>%s</strong></p>',
						__( 'Constant defined programmatically, the setting above will be overridden.', 'render-faster' ),
						esc_html( strtoupper( $option_name ) ),
						( $defined ? esc_html__( 'Enabled', 'render-faster' ) : esc_html__( 'Disabled', 'render-faster' ) )
					);
				}
			}, 'render-faster', 'render_faster_lazy_loading_section' );
			register_setting( 'render-faster', $option_name );
			// Add extra options for images.
			if ( 'image' === $key ) {
				foreach ( [
					[ 'eager_keys', __( 'High priority', 'render-faster' ), __( 'Img tag matching this csv list will be loaded "eager". Good for custom logo and post\'s eye-catch.', 'render-faster' ) ],
					[ 'should_not', __( 'Skip', 'render-faster' ), __( 'Img tag matching this csv list will be skipped and never added loading attributes.', 'render-faster' ) ],
				] as list( $extra_key, $extra_label, $extra_desc ) ) {
					$extra_option_name = 'render_faster_image_' . $extra_key;
					add_settings_field( $extra_option_name, $extra_label, function() use ( $extra_desc, $extra_option_name ) {
						printf(
							'<input class="regular-text" type="text" name="%s" value="%s" placeholder="%s" /><p class="description">%s</p>',
							esc_attr( $extra_option_name ),
							esc_attr( get_option( $extra_option_name, '' ) ),
							'e.g. custom-logo, post-thumbnail',
							esc_html( $extra_desc )
						);
					}, 'render-faster', 'render_faster_lazy_loading_section' );
					register_setting( 'render-faster', $extra_option_name );
				}
			}
		}

	}

	/**
	 * Register menu settings.
	 */
	public function admin_menu() {
		$title = __( 'Rendering Optimization', 'render-faster' );
		add_theme_page( $title, $title, 'edit_theme_options', 'render-faster', [ $this, 'render_setting' ], 100 );
	}

	/**
	 * Render settings screen.
	 */
	public function render_setting() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Rendering Optimization', 'render-faster' ) ?></h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
				<?php
				settings_fields( 'render-faster' );
				do_settings_sections( 'render-faster' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Filter images option.
	 */
	public function filter_image() {
		add_filter( 'render_faster_image_eager_keys', function() {
			return array_values( array_filter( array_map( 'trim', explode( ',', get_option( 'render_faster_image_eager_keys', '' ) ) ) ) );
		} );
		add_filter( 'render_faster_image_should_not', function() {
			return array_values( array_filter( array_map( 'trim', explode( ',', get_option( 'render_faster_image_should_not', '' ) ) ) ) );
		} );
	}
}
