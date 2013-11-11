(function($){
	if($ === undefined) throw 'jQuery is not installed. This is required.';

	var
	singleton, $el,
	optKey = 't360_config',
	config = {},
	defaultOpts = {
		enabled: false,
		selector: 'header',
		positionCls: 'bottom-right',
		baseCls: 't360-promo',
		simpleDlgBaseCls: 'simple-dialog',
		hashmark: 'launch-360-tour',
		image: null,
		imageTitle: null,
		imageWidth: null,
		imageHeight: null,
		windowWidth: '90%',
		windowHeight: '90%',
		windowHintClose: 'Close tour',
		windowTitle: '360 Tour',
		url: null
	};
	function unconfigure(me) {

		if($el !== undefined) {
			$el.remove();
			$el = undefined;
		}

		return me;
	}
	function configure(me, cfg) {

		$.extend(config, cfg);

		// normalize enabled
		config.enabled = cfg.enabled !== undefined ?
				(cfg.enabled === true) :
				config.enabled;

		return me; // return latest config
	}
	function createEl(sel) {
		return $('<div><a><img/><a></div>')
			.appendTo(sel);
	}
	function showTourWindow(tourUrl, baseCls, closeHint, windowTitle) {
		var $o, $w, root = 'html', closeWin = function(e){
			e.preventDefault();
			$o.remove();
			$w.remove();
		};
		$o = $('<div>').appendTo(root);
		$w = $(['<div class="'+baseCls+'-window">',
		 	'<div class="'+baseCls+'-window-wrapper">',
		 		'<a class="'+baseCls+'-button"></a>',
		 		'<h3 class="'+baseCls+'-title"></h3>',
		 		'<div class="'+baseCls+'-body-wrapper">',
		 			'<iframe />',
		 		'</div>',
		 	'</div>',
		 '</div>'].join(''))
			.appendTo(root);

		$o.addClass(baseCls + '-overlay').on('click', closeWin);

		$w.find('a').attr({
				title: closeHint,
				href: '#close-tour'
			}).on('click', closeWin);

		$w.find('h3').html(windowTitle);

		$w.find('iframe').attr({
				src: tourUrl,
				frameborder: 0
			});
	}
	function render(me) {

		if( !config.enabled || !config.selector ) {
			return me;
		}

		if($el === undefined) {
			$el = createEl(config.selector);
			$el.find('a')
				.on('click',function(e){
					e.preventDefault();
					showTourWindow(config.url, config.simpleDlgBaseCls,
							config.windowHintClose, config.windowTitle);
				});
		}

		$el.toggleClass(config.baseCls, !$el.hasClass(config.baseCls))
			.addClass(config.positionCls);

		$el.find('a')
			.attr({
				href: '#' + config.hashmark,
				title: config.imageTitle
			});

		$el.find('img')
			.attr({
				src: config.image,
				alt: config.imageTitle,
				width: config.imageWidth,
				height: config.imageHeight
			});

		return me;
	}

	function T360(cfg){
		var me = this;

		me.reconfigure = function(cfg, performRender) {
			console.log("reconfigure");
			unconfigure(me);
			configure(me, cfg);
			return performRender ? render(me) : me;
		};

		me.render = function() {
			return render(me);
		};

		me.destroy = function() {
			return unconfigure(me);
		};

		return configure(me, cfg);
	};

	T360.getInstance = function() {
		return singleton ? singleton :
			singleton = new T360($.extend(true,{},defaultOpts,window[optKey]));
	};

	window.T360 = T360;

	$(function(){
		T360.getInstance().render();
	});

})(jQuery);