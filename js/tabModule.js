//Tab Module

(function($,window){
	'use-strict';

	var elements = {};

	//View

	var constructTab = function(tabContainer,classname){
		
		var tabContentEl = $(tabContainer).find('.'+classname+' > li');
		$.each(tabContentEl,function(){
			hideTabs(this);
		});
		
		var activeTab = ($('.tab-legend .active').length)? $('.tab-legend .active') : $('.tab-legend > li:first-child');
		showTab(activeTab,classname);
	};

	var hideTabs = function(tab,callback){
		$(tab).hide().removeClass('active');
		if(callback){
			callback();
		}
	};

	var showTab = function(tab,classname){
		var index = tab.index();
		var activ_background = $('.tab-legend > li').attr('data-background');
		var activ_border = $('.tab-legend > li').attr('box-border');
		
		hideTabs($('.'+classname+' .active'));
		$('.tab-legend .active').removeClass('active').addClass('inactive');
		$('.tab-legend > li').eq(index).removeClass('inactive').addClass('active');
		$('.tab-legend > li.inactive').removeAttr("style");
		$('.tab-legend > li.active').css({
			"background-color":activ_background,
			"border":'1px solid '+activ_border, 
		});		
		$('.'+classname+' > li').eq(index).fadeIn('slow','linear').addClass('active');
	};


	//Controller
	var tabController = function(tabContainer,classname){
		var tabLegendEl = $(tabContainer).find('.tab-legend li');
		
		
		$.each(tabLegendEl,function(){
			$(this).on('click', function(){
			
				var tabElement = $(this);
				showTab(tabElement,classname);
				
				if ( $(window).width() < 500) {
					
					var index = tabElement.index();
					
						var revised = index + 1;
						//alert(revised);
						var divid = '#content_'+revised;
						//alert(divid);
						$('html, body').animate({
						scrollTop: $(divid).offset().top-50
					}, 2000);
									
				}
				
				
				
			});
		});
	};

	var init = function(){
		console.log('Initiating Tab Module');
		var self = this;
		var tabElement = $('.tab');
		var contentclass = $('.tab ul:nth-child(2)').attr('class');
		//var class_name = $('.tab-legend').closest('div').attr('class').split(' ')[1];	
		if ( $(window).width() < 500) {
			$('.'+contentclass+' > li').find('span.p_title').hide();
			$('.tab-legend > li').find('span.p_title').show();
		}
		else
		{
			$('.'+contentclass+' > li').find('span.p_title').show();
			$('.tab-legend > li').find('span.p_title').hide();
		}
		$.each(tabElement, function(){
			constructTab(this,contentclass);
			tabController(this,contentclass);
		});
	};

	//public
	var tabModule = {
		init: init
	};

	//transport
	if(typeof(define)==='function' && define.amd){
		define(tabModule);
	} else if (typeof(exports)==='object'){
		module.tabModule = tabModule;
	} else {
		window.tabModule = tabModule;
	}
	
	

}(jQuery,window));


 tabModule.init();