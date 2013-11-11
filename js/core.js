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
		hashmark: 'launch-360-tour',
		image: null,
		imageTitle: null,
		imageWidth: null,
		imageHeight: null,
		url: null
	};
	function unconfigure(me) {
		console.log("unconfigure:", config);

		if($el !== undefined) {
			$el.remove();
			$el = undefined;
		}

		return me;
	}
	function configure(me, cfg) {
		console.log("configure:", cfg);

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
	function render(me) {
		console.log("render");

		if( !config.enabled || !config.selector ) {
			return me;
		}

		if($el === undefined) {
			$el = createEl(config.selector);
			$el.find('a')
				.on('click',function(e){
				e.preventDefault();
				alert("This would do something with: " + config.url);
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