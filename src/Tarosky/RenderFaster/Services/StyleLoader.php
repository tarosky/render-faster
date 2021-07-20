<?php

namespace Tarosky\RenderFaster\Services;


use Tarosky\RenderFaster\Pattern\Service;

/**
 * Style loader.
 *
 * @package render-faster
 */
class StyleLoader extends Service {

	/**
	 * @return bool[]
	 */
	public function features() {
		return [
			'preload'          => false,
			'preload_polyfill' => true,
		];
	}

	/**
	 * Constructor.
	 */
	protected function init() {
		if ( $this->is_feature_active( 'preload' ) ) {
			add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 10, 4 );
			if ( $this->is_feature_active( 'preload_polyfill' ) ) {
				add_action( 'wp_footer', [ $this, 'preload_polyfill' ], 100 );
			}
		}
	}

	/**
	 * Replace style loader tag.
	 *
	 * @param string $tag    Link tag.
	 * @param string $handle Handle name.
	 * @param string $href   Href attribute.
	 * @param string $media  Media type.
	 * @return string
	 */
	public function style_loader_tag( $tag, $handle, $href, $media ) {
		if ( ! $this->is_public( 'css' ) ) {
			return $tag;
		}
		$deny_list = apply_filters( 'render_faster_css_deny_list', [] );
		if ( in_array( $handle, $deny_list, true ) ) {
			return $tag;
		}
		$html = <<<'HTML'
<link rel="preload" href="%1$s" as="style" onload="this.onload=null;this.rel='stylesheet'" data-handle="%3$s" media="%4$s" />
<noscript>
        %2$s
</noscript>
HTML;
		return sprintf( $html, esc_url( $href ), $tag, esc_attr( $handle ), esc_attr( $media ) );
	}

	/**
	 * Render polyfill for old browsers.
	 */
	public function preload_polyfill() {
		$src = dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/dist/vendor/loadcss/cssrelpreload.min.js';
		if ( ! file_exists( $src ) ) {
			return;
		}
		$content = file_get_contents( $src );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<script>%s</script>', $content );
	}

}
