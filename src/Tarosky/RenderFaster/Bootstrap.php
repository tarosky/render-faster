<?php

namespace Tarosky\RenderFaster;

use Tarosky\RenderFaster\Pattern\Singleton;
use Tarosky\RenderFaster\Services\LazyLoader;
use Tarosky\RenderFaster\Services\ScriptLoader;
use Tarosky\RenderFaster\Ui\Settings;

/**
 * Bootstrap file.
 *
 * @package render-faster
 */
class Bootstrap extends Singleton {

	/**
	 * Constructor.
	 */
	protected function init() {
		LazyLoader::get_instance();
		ScriptLoader::get_instance();
		if ( ! defined( 'RENDER_FASTER_NO_UI' ) || ! RENDER_FASTER_NO_UI ) {
			Settings::get_instance();
		}
	}
}
