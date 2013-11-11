(function($){
	if($ === undefined) throw 'jQuery is not installed. This is required.';

	function T360(cfg){
		console.log("t360 loaded. config:", cfg, ', instance:', this);
		return this;
	};

	$(function(){
		var
		config = window.t360_config,
		instance = new T360(config);
	});

})(jQuery);