<?php

namespace Tarosky\RenderFaster\Services;

use Tarosky\RenderFaster\Pattern\Service;

/**
 * Lazy load image & iframe.
 *
 * @package Tarosky\RenderFaster\Services
 */
class LazyLoader extends Service {

	/**
	 * @inheritDoc
	 */
	public function features() {
		return [
			'image'  => false,
			'iframe' => false,
		];
	}

	/**
	 * Constructor.
	 */
	protected function init() {
		if ( $this->is_feature_active( 'image' ) ) {
			add_action( 'wp_body_open', [ $this, 'output_buffer_start' ], 9999 );
			add_action( 'wp_footer', [ $this, 'images_close' ], 100 );
		}
		if ( $this->is_feature_active( 'iframe' ) ) {
			add_action( 'wp_body_open', [ $this, 'output_buffer_start' ], 9998 );
			add_action( 'wp_footer', [ $this, 'iframes_close' ], 101 );
		}
	}

	/**
	 * Ob start.
	 */
	public function output_buffer_start() {
		ob_start();
	}

	/**
	 * Convert images.
	 */
	public function images_close() {
		$eagers     = apply_filters( 'render_faster_image_eager_keys', [] );
		$should_not = apply_filters( 'render_faster_image_should_not', [] );
		$content    = ob_get_contents();
		$content    = $this->convert_attributes( $content, 'img', $should_not, $eagers );
		ob_end_clean();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
	}

	/**
	 * Convert iframes.
	 */
	public function iframes_close() {
		$eagers     = apply_filters( 'render_faster_iframe_eager_keys', [] );
		$should_not = apply_filters( 'render_faster_iframes_should_not', [] );
		$content    = ob_get_contents();
		$content    = $this->convert_attributes( $content, 'iframe', $should_not, $eagers );
		ob_end_clean();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
	}

	/**
	 * Replace contents.
	 *
	 * @param string   $content
	 * @param string   $tag_name
	 * @param string[] $should_not
	 * @param string[] $eagers
	 *
	 * @return string
	 */
	public function convert_attributes( $content, $tag_name, $should_not, $eagers ) {
		return preg_replace_callback( '#<(' . $tag_name . ')([^>]+)>#u', function( $matches ) use ( $should_not, $eagers ) {
			list( $match, $tag, $attr ) = $matches;
			// If should not, skip.
			foreach ( $should_not as $str ) {
				if ( false !== strpos( $attr, $str ) ) {
					return $match;
				}
			}
			// Add lazy attr if not exists.
			if ( false === strpos( $attr, 'loading=' ) ) {
				// If this is cover image, eager.
				$loading = 'lazy';
				foreach ( $eagers as $eager ) {
					if ( false !== strpos( $attr, $eager ) ) {
						$loading = 'eager';
					}
				}
				$attr = sprintf( ' loading="%s"%s', esc_attr( $loading ), $attr );
			} elseif ( preg_match( '#loading=#u', $attr ) ) {
				// Skip eager images like cover image.
				foreach ( $eagers as $eager ) {
					if ( false !== strpos( $attr, $eager ) ) {
						$attr = preg_replace( '/loading=([\'"])[^\'"]+([\'"])/u', 'loading=$1eager$2', $attr );
					}
				}
			}
			return sprintf( '<%s%s>', $tag, $attr );
		}, $content );
	}
}
