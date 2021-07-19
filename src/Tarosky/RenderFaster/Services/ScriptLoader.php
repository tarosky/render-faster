<?php

namespace Tarosky\RenderFaster\Services;


use Tarosky\RenderFaster\Pattern\Service;

/**
 * Script loader.
 *
 * @package render-faster
 */
class ScriptLoader extends Service {

	/**
	 * @inheritDoc
	 */
	public function features() {
		return [
			'jquery_migrate' => true,
			'jquery_footer'  => false,
			'defer'          => false,
			'async'          => false,
			'inline'         => false,
		];
	}

	/**
	 * Register hooks.
	 */
	protected function init() {
		if ( $this->is_feature_active( 'jquery_migrate' ) || $this->is_feature_active( 'jquery_footer' ) ) {
			add_action( 'init', [ $this, 'jquery' ], 11 );
		}
		if ( $this->is_feature_active( 'defer' ) ) {
			add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10, 2 );
		}
		if ( $this->is_feature_active( 'inline' ) ) {
			add_filter( 'script_loader_tag', [ $this, 'inline_script' ], 11, 3 );
		}
	}

	/**
	 * Change jQuery loading.
	 */
	public function jquery() {
		// Do nothing on admin screen.
		if ( ! $this->is_public( 'jquery' ) ) {
			return false;
		}
		// Save current version.
		global $wp_scripts;
		$jquery     = $wp_scripts->registered[ 'jquery-core' ];
		$jquery_ver = $jquery->ver;
		$jquery_src = $jquery->src;
		// Flag.
		$move_jquery_to_footer = $this->is_feature_active( 'jquery_footer' );
		// Dependencies.
		$deps = [ 'jquery-core' ];
		if ( ! $this->is_feature_active( 'jquery_migrate' ) ) {
			$deps[] = 'jquery-migrate';
		}
		// Remove existing.
		wp_deregister_script( 'jquery' );
		wp_deregister_script( 'jquery-core' );
		// Register them again.
		wp_register_script( 'jquery-core', $jquery_src, [], $jquery_ver, $move_jquery_to_footer );
		wp_register_script( 'jquery', false, $deps, $jquery_ver, $move_jquery_to_footer );
	}

	/**
	 * Change script loader tag.
	 *
	 * @param string $tag    HTML tag.
	 * @param string $handle Handle name.
	 * @return string
	 */
	public function script_loader_tag( $tag, $handle ) {
		// Skip if not public.
		if ( ! $this->is_public( 'script' ) ) {
			return $tag;
		}
		// Allow & deny list.
		$allow_lists = apply_filters( 'render_faster_js_allow_list', [] );
		$deny_lists  = apply_filters( 'render_faster_js_deny_list', [] );
		// If in deny list, skip.
		if ( in_array( $handle, $deny_lists, true ) ) {
			return $tag;
		}
		// If allow listed and not included, skip.
		if ( ! empty( $allow_lists ) && ! in_array( $handle, $allow_lists, true ) ) {
			return $tag;
		}
		// If tag has after, skip.
		global $wp_scripts;
		if ( ! isset( $wp_scripts->registered[ $handle ] ) ) {
			return $tag;
		}
		$script = $wp_scripts->registered[ $handle ];
		// If 'after' exists, this cannot be deferred.
		if ( ! empty( $script->extra['after'] ) ) {
			return $tag;
		}
		// If already deferred, skip.
		if ( preg_match( '/ (defer|async)/u', $tag ) ) {
			return $tag;
		}
		$defer = 'defer';
		if ( $this->is_feature_active( 'async' ) && empty( $script->deps ) ) {
			$defer = 'async';
		}
		return str_replace( ' src=', " {$defer} src=", $tag );
	}

	/**
	 * Inline specified scripts.
	 *
	 * @todo Implement embedding.
	 * @param string $tag    HTML tag.
	 * @param string $handle Handle name.
	 * @param string $src    Script's src.
	 * @return string
	 */
	public function inline_script( $tag, $handle, $src ) {
		$allow_lists = apply_filters( 'render_faster_inline_scripts', [ 'hoverintent-js' ] );
		if ( in_array( $handle, $allow_lists, true ) ) {
			// This tag should be inline.
			if ( false !== strpos( $src, plugins_url() ) ) {
				// In plugin directory.
				$from = plugins_url();
				$dest = WP_PLUGIN_DIR;
			} elseif ( false !== strpos( $src, get_theme_root_uri() ) ) {
				// In theme root.
				// W.I.P
			} elseif ( false !== strpos( $src, network_home_url() )  ) {
				// Other, maybe core.
				// W.I.P
			} else {
				// This is outside. skip.
				return $tag;
			}
		}
		return $tag;
	}
}
