<?php

namespace Tarosky\RenderFaster\Pattern;

/**
 * Service pattern.
 *
 * @package render-faster
 */
abstract class Service extends Singleton {

	/**
	 * List of features.
	 *
	 * Format should be [ 'feature_name' => true ],
	 * feature name as key, default value as value in boolean.
	 * The feature name should be snake case e.g. a_feature_to_remove_attributes
	 *
	 * @return bool[]
	 */
	abstract public function features();

	/**
	 * Get option name.
	 *
	 * @param string $feature Feature name.
	 *
	 * @return string
	 */
	public function get_feature_option_key( $feature ) {
		return 'render_faster_' . $feature;
	}

	/**
	 * Get data from option.
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public function get_option( $feature ) {
		$features    = $this->features();
		$default     = $features[ $feature ];
		$option_name = $this->get_feature_option_key( $feature );
		return (bool) get_option( $option_name, $default );
	}

	/**
	 * Get defined value.
	 *
	 * @param string $feature Feature name.
	 * @return bool|null
	 */
	public function get_defined_value( $feature ) {
		$constant_name = strtoupper( $this->get_feature_option_key( $feature ) );
		return defined( $constant_name ) ? constant( $constant_name ) : null;
	}

	/**
	 * Detect if features is active.
	 *
	 * @param string $feature Name of feature.
	 * @return bool
	 */
	public function is_feature_active( $feature ) {
		$features = $this->features();
		if ( ! array_key_exists( $feature, $features ) ) {
			return false;
		}
		$option_name = $this->get_feature_option_key( $feature );
		$is_active   = $this->get_option( $feature );
		// If constant is defined, it overrides
		$constant = $this->get_defined_value( $feature );
		if ( ! is_null( $constant ) ) {
			$is_active = $constant;
		}
		return (bool) apply_filters( $option_name . '_is_active', $is_active );
	}

	/**
	 * Whether if this is public area.
	 *
	 * @param string $context Context of "public"
	 * @return bool
	 */
	public function is_public( $context = '' ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$is_public = ! is_admin() && ! ( isset( $_SERVER['SCRIPT_FILENAME'] ) && 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) );
		return (bool) apply_filters( 'render_fast_is_public', $is_public, $context );
	}
}
