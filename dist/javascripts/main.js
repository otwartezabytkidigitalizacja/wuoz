jQuery(function($){
	$(document).ready(function(){
		// Login Popup and neccessary RWD margins
		inits();

		//slider Margin and dynamic elements

		$(window).on('resizeEnd', function() {
			dynamicElements();
				console.info($(window).width());
				clearTimeout(this.resizeTO);
			});

		$(window).resize(function() {
			if(this.resizeTO) clearTimeout(this.resizeTO);

			this.resizeTO = setTimeout(function() {
				$(this).trigger('resizeEnd');
			}, 10);
		});
		$(window).scroll(function(){
			showHeaderSearch();
		});

		init_home_map();
		load_home_map();
		monument_map();
		contact_map();

		
		if($('body').hasClass('single-document')){
			$(".attachment-full").wheelzoom();

			$('#zoomInButton').on({
				click: function(){
					$(".attachment-full").trigger('wheelzoom.zoomin');
				}
			});

			$('#zoomOutButton').on({
				click: function(){
					$(".attachment-full").trigger('wheelzoom.zoomout');
				}
			});

			$('#nav-prev-page').on({
				click: function(event) {
					if($('#docs-scans li.active').prev().length!==0)
						$('#docs-scans li.active').prev().find('a').trigger('click');
				}
			});

			$('#nav-next-page').on({
				click: function(event) {
					if($('#docs-scans li.active').next().length!==0)
						$('#docs-scans li.active').next().find('a').trigger('click');
				}
			});
		}
		

		// object thumbnails event for logged in users

		//my favs bar
		my_favs_bar_inside_events();

		//ajax Filters in Search
		if(window.location.hash==="#zabytki")
			$('a[data-type="monument"]').addClass('ajax-on');
		if(window.location.hash==="#dokumenty") {
			$('a[data-type="document"]').addClass('ajax-on');
			$('#filter-grey #recent-label .document-types').fadeIn();
		}
		ajaxFilterAction();

		//alert(jQuery('.single-thumb:first').attr('data-post-id'));
		paginationRefresh();

		//back button for reloading results
		triggerAjaxPaginationOnHash();

		//activate droppable and draggable

		activateUserDnD();

		//user folders events
		addUserFolder();
		removeUserFolder();

		//testing popup events

		singlePopupEvents();

	});
	// when the doc and images are loaded
	showLoadedInitials();
	zoomInOnPlus = function(){
		var img = $('#document-browser img.attachment-full');
		var zwidth = $('#document-browser img.attachment-full').css('background-size');
		console.info(zwidth);
	};

	searchFunction();

});
