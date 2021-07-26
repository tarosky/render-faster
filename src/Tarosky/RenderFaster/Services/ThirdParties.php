<?php

namespace Tarosky\RenderFaster\Services;


use Tarosky\RenderFaster\Pattern\Service;

/**
 * Third party enhancement.
 *
 * @package render-faster
 */
class ThirdParties extends Service {

	/**
	 * @var string[] List of scripts.
	 */
	protected $enqueues = [];

	/**
	 * List of features.
	 *
	 * @return bool[]
	 */
	public function features() {
		return [
			'embeds' => false,
		];
	}


	/**
	 * Constructor.
	 */
	protected function init() {
		add_action( 'template_redirect', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		if ( $this->is_feature_active( 'embeds' ) ) {
			add_filter( 'the_content', [ $this, 'filter_embeds' ], 100 );
			add_action( 'wp_footer', [ $this, 'render_embed_scripts' ], 9999 );
		}
	}

	/**
	 * Filter embed scripts.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function filter_embeds( $content ) {
		// Twitter.
		$content = preg_replace_callback( '@<script async src="https://platform\.twitter\.com/widgets\.js" charset="utf-8"></script>@', function() {
			$script = 'https://platform.twitter.com/widgets.js';
			if ( ! in_array( $script, $this->enqueues, true ) ) {
				$this->enqueues[] = $script;
			}
			return '';
		}, $content );
		// Instagram.
		$content = preg_replace_callback( '@<script async src="//www\.instagram\.com/embed\.js"></script>@u', function() {
			$script = 'https://www.instagram.com/embed.js';
			if ( ! in_array( $script, $this->enqueues, true ) ) {
				$this->enqueues[] = $script;
			}
			return '';
		}, $content );
		return $content;
	}

	/**
	 * Render embed scripts.
	 */
	public function render_embed_scripts() {
		if ( empty( $this->enqueues ) ) {
			// No scripts, do nothing.
			return;
		}
		$urls = json_encode( $this->enqueues );
		echo <<<HTML
<script>
!function(){
	var loaded = false;
	var urls = {$urls};
	var lazyLoad = function() {
		if ( ! loaded ) {
			loaded = true;
			window.removeEventListener( 'scroll', lazyLoad );
			window.removeEventListener( 'mousemove', lazyLoad );
			window.removeEventListener( 'mousedown', lazyLoad );
			window.removeEventListener( 'touchstart', lazyLoad );
			window.removeEventListener( 'keydown', lazyLoad );
			var head = document.getElementsByTagName( 'head' )[0];
			urls.forEach( function( src ) {
				var s = document.createElement( 'script' );
				s.async = true;
				s.src = src;
				head.appendChild( s );
			} );
		}
	};

	window.addEventListener( 'scroll', lazyLoad );
	window.addEventListener( 'mousemove', lazyLoad );
	window.addEventListener( 'mousedown', lazyLoad );
	window.addEventListener( 'touchstart', lazyLoad );
	window.addEventListener( 'keydown', lazyLoad );
	window.addEventListener( 'load', function() {
    // Reload or internal page link.
    if (window.pageYOffset) {
      lazyLoad();
    }
  });
}();
</script>
HTML;
	}
}
