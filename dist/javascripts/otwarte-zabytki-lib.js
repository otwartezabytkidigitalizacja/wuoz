(function ($) {
    inits = function () {
        dynamicElements();
        $(document).foundation();
        $('#object-reveal').foundation('reveal',
            {
                animation: 'fadeAndslide',
                open: function () {
                    $('body').addClass('popup-open');
                },
                close: function () {
                $('body').removeClass('popup-open');
                }, opened: function () {
                    $('.reveal-modal-bg').css('top', $('nav.top-bar').height() + 'px');
                }, closed: function () {
                    $('.reveal-modal-bg').css('top', '0');
                }
            });
        $('.link-form-submit').on(
            {
                click: function (event) {
                    event.preventDefault();
                    $(this).parents('form').submit();
                }
            });// vimeo load on click
        jQuery(document).ready(function () {
            jQuery('#video-reveal').foundation('reveal',
                {
                    open: function () {
                        var data = {action: 'load_vimeo'};
                        $.post(otwarte_data.ajax_url, data, function (response) {
                            console.info('load vimeo', response);
                            $('#video-reveal .flex-video').html(response);
                        });
                    }
                });
        });// my fav bar action
        $('#my-favs #ver-bar').on({click: toggleMyFavBar});
        if ($('body').hasClass('home') || $('body').hasClass('single')) {
            thumb_events();
        }

        mobileMenu();
    };

    showLoadedInitials = function () {// on loaded images
        $(window).load(function () {//$('#ajax-wrapper').removeClass('preloader');
        });
    };// Init Actions

    dynamicElements = function () {
        //$('.first-top-container').css({'margin-top': $('nav.top-bar').height()});//my favs top setup
        $('#my-favs').css({'top': $('nav.top-bar').height()});//add scroll to fixed element if too high
        if ($('#list-cnt').outerHeight() > 0.5 * $(window).height())$('#list-cnt .scrollable').addClass('scroll-active');
        else $('#list-cnt .scrollable').removeClass('scroll-active');//responsive long doc titles
        //$('h4.label span.title-text').each(function () {
        //    var parent = $(this).parent('h4');
        //    if (($(this).height() + 16) >= parent.height()) parent.height($(this).height() + 15);
        //    else parent.css('height', 'auto');
        //});

        // keeps the columns change in my folders view
        reColumnFolders();// when resizing window
        $(document).foundation('interchange', 'reflow');
    };// home page scroll

    showHeaderSearch = function () {
        if ($('body').hasClass('home')) {
            if ($('#home-search').length > 0 && ($(window).scrollTop() > ($('#slider').outerHeight() + $('#home-search').innerHeight()))) {
                $('#header-search').fadeIn();
                $('#home-search .oz-button').fadeOut('slow');
            }
            else {
                $('#header-search').fadeOut('slow');
                $('#home-search .oz-button').fadeIn('slow');
            }
        }
    };//Map JS

    init_home_map = function () {
        if ($('#home-map').length > 0) {
            var h, scroll;
            if ($('#home-map-container').length > 0) {
                h = 600;
                scroll = false;
            }
            else {
                var header = $('#header-menu').outerHeight(true);//var footer = $('footer').outerHeight(true);
                $('#home-map').css('margin-top', header + 'px');
                h = $(window).height() - header;
                scroll = true;
            }
            $('#home-map').height(h).gmap3({
                map: {
                    options: {
                        minZoom: 7,
                        center: [52.06881, 19.47974],
                        mapTypeControl: false,
                        panControl: false,
                        scrollwheel: scroll,
                        zoomControlOptions: {style: google.maps.ZoomControlStyle.SMALL},
                        styles: [{stylers: [{saturation: -95}, {lightness: 5}]}, {
                            featureType: "poi", elementType: "labels",
                            stylers: [{visibility: "off"}]
                        }]
                    }, events: {
                        click: function () {
                            $('#home-map').gmap3({clear: "overlay"});
                        }
                    }
                }
            });
        }
    };

    load_home_map = function () {
        var data = {action: 'map_objects'};
        $.post(otwarte_data.ajax_url, data, function (response) {
            if (response === 0)return;
            $('#home-map').gmap3({
                marker: {
                    values: jQuery.parseJSON(response),
                    options: {
                        icon: new google.maps.MarkerImage(otwarte_data.template_url + "/img/monument-marker.png")
                    },
                    cluster: {
                        radius: 30, 3: {content: "<div class='cluster'>CLUSTER_COUNT</div>", width: 30, height: 52},
                        events: {
                            click: function (cluster) {
                                var map = $(this).gmap3("get");
                                map.setCenter(cluster.main.getPosition());
                                map.setZoom(map.getZoom() + 1);
                            }
                        }
                    },
                    events: {
                        click: function (marker, event, context) {
                            if ($('#ovr' + context.id).length > 0) {
                                $('#home-map').gmap3({clear: "overlay"});
                            }
                            else {
                                var monument_data = {action: 'monument_data', id: context.id};
                                $.post(otwarte_data.ajax_url, monument_data, function (response) {
                                    var data = jQuery.parseJSON(response);
                                    $('#home-map').gmap3({clear: "overlay"}, {
                                        overlay: {
                                            latLng: marker.getPosition(), options: {
                                                content: '<div id="ovr' +
                                                context.id + '" class="map-ovr">' + '<h4 class="label">' + data.name + '</h4>' +
                                                '<div class="ovr-content">' + data.excerpt + '</div>' + '<div class="ovr-button-container"><a href="' + data.permalink +
                                                '"></a></div>' + '</div>', offset: {x: 20, y: -54}
                                            }
                                        }
                                    });
                                });
                            }
                        }
                    }
                },
                autofit: {}
            });
        });
    };

    monument_map = function () {
        if ($('#monument-map').length > 0) {
            $('#monument-map').height(400).gmap3({
                map: {
                    options: {
                        minZoom: 7,
                        center: [52.06881, 19.47974],
                        mapTypeControl: false,
                        panControl: false,
                        scrollwheel: scroll,
                        zoomControlOptions: {style: google.maps.ZoomControlStyle.SMALL},
                        styles: [{
                            stylers: [{saturation: -95}, {lightness: 5}
                            ]
                        }, {featureType: "poi", elementType: "labels", stylers: [{visibility: "off"}]}]
                    }
                }, marker: {
                    lat: $('#monument-map').data('lat'),
                    lng: $('#monument-map').data('lng'),
                    options: {icon: new google.maps.MarkerImage(otwarte_data.template_url + "/img/monument-marker.png")}
                },
                autofit: {}
            });
        }
    };

    contact_map = function () {
        if ($('#contact-map').length > 0) {
            $('#contact-map').height(300).gmap3({
                map: {
                    options: {
                        zoom: 15,
                        minZoom: 7,
                        center: [$('#contact-map').data('lat'), $('#contact-map').data('lng')],
                        mapTypeControl: false,
                        panControl: false,
                        scrollwheel: scroll,
                        zoomControlOptions: {style: google.maps.ZoomControlStyle.SMALL},
                        styles: [{
                            stylers: [{saturation: -95}, {lightness: 5}
                            ]
                        }, {featureType: "poi", elementType: "labels", stylers: [{visibility: "off"}]}]
                    }
                }, marker: {
                    lat: $('#contact-map').data('lat'), lng: $('#contact-map').data('lng'),
                    options: {icon: new google.maps.MarkerImage(otwarte_data.template_url + "/img/monument-marker.png")}
                }
            });
        }
    };// adding to favourites

    thumb_events = function () {
        $('.single-thumb .fav-star-ico').on({click: handle_star_click});//make them draggable inside open folder
        dragItemInsideFolder();// removeSingleItemFromFolder
        $('.rem-from-fol').on({click: removeSingleItemFomFolder});
        $(document).foundation('interchange', 'reflow');
        $('.single-thumb[data-post-id]').on({click: get_single_object});
    };// my favs bar events

    my_favs_bar_inside_events = function () {
        $('#my-favs .del-favs').on({click: remove_all_favs});
        $('#my-favs .remove-fav').on({click: remove_from_bar});//add scroll to fixed element if too high
        if ($('#list-cnt').outerHeight() > 0.5 * $(window).height())$('#list-cnt .scrollable').addClass('scroll-active');
        else $('#list-cnt .scrollable').removeClass('scroll-active');//refresh drag abbility
        if ($('body').hasClass('page-template-my_folders-php')) {
            var draggers = $('#my-favs #list-cnt ul li');
            draggers.draggable({helper: "clone"});
        }
    };

    toggleMyFavBar = function () {
        var bar_cnt = $(this).parents('#my-favs');
        var data;
        if (bar_cnt.hasClass('bar-opened')) {
            data = {action: 'close_favs'};
            $.post(otwarte_data.ajax_url, data, function (response) {
            });
        }
        else {
            data = {action: 'open_favs'};
            $.post(otwarte_data.ajax_url, data, function (response) {
            });
        }
        bar_cnt.toggleClass('bar-opened');// keep the folders react to bar open close events
        reColumnFolders();
    };

    remove_from_bar = function () {
        var id = parseInt($(this).parents('li').attr('data-post-id'));
        var clicker = $(this);
        var data = {action: 'remove_fav', post_id: id};
        console.info($(this));
        $.post(otwarte_data.ajax_url, data, function (response) {
            if (response === 0) {
                clicker.parents('li').remove();
                $('.single-thumb[data-post-id="' + id + '"] .alreadyfav').removeClass('alreadyfav');
                refresh_favs();
            }
        });
    };

    remove_all_favs = function (event) {
        event.preventDefault();
        var clicker = $(this);
        var data = {action: 'remove_all_favs'};
        $.post(otwarte_data.ajax_url, data, function (response) {
            //if (response === 0) {
                clicker.fadeOut('slow');
                $('.single-thumb .alreadyfav').removeClass('alreadyfav');
                refresh_favs();
            //}
        });
    };// adding from thumbnail

    handle_star_click = function (event) {
        event.preventDefault();
        event.stopPropagation();
        var add;
        var idToHandle = parseInt($(this).parents('.single-thumb').attr('data-post-id'));
        var post_type = $(this).parents('.single-thumb').attr('data-post-type');
        if ($(this).hasClass('alreadyfav')) add = false;
        else add = true;
        if (add) add_to_fav(idToHandle, $(this));
        else remove_fav(idToHandle, $(this));
    };

    add_to_fav = function (id, clicker) {
        var data = {action: 'add_to_fav', post_id: id};
        $.post(otwarte_data.ajax_url, data, function (response) {
            //console.info(response);
            if (parseInt(response) === 0) {
                clicker.addClass('alreadyfav');
                refresh_favs();
                makeObjectAnimate(clicker.parents('.large-3'), 'pulse');
            }
        });
    };

    remove_fav = function (id, clicker) {
        var data = {action: 'remove_fav', post_id: id};
        $.post(otwarte_data.ajax_url, data, function (response) {
            //console.info(response);
            if (parseInt(response) === 0) {
                clicker.removeClass('alreadyfav');
                refresh_favs();
                makeObjectAnimate(clicker.parents('.large-3'), 'pulse');
            }
        });
    };

    refresh_favs = function () {
        var data = {action: 'refresh_all_favs'};
        $('#list-cnt .scrollable').addClass('preloader');
        $.post(otwarte_data.ajax_url, data, function (response) {
            if (response !== -1) {
                $('#list-cnt').html(response);
                $('#list-cnt .scrollable').removeClass('preloader');
            }
            my_favs_bar_inside_events();
        });
    };// ajax filters sort

    ajaxFilterAction = function () {
        var filters = $(' #filter-grey #recent-label .object-filter a ');
        var doctype = $('#filter-grey #recent-label #document-type');
        var montype = $('#filter-grey #recent-label #monument-type');
        var docfilter = $('#filter-grey #recent-label .object-filter a[data-type="document"]');
        var monfilter = $('#filter-grey #recent-label .object-filter a[data-type="monument"]');
        filters.on({
                click: function () {
                    var filterson = $(' #filter-grey #recent-label .object-filter a.ajax-on ');
                    if ($(this).hasClass('ajax-on') && filterson.length === 2) {
                        $(this).removeClass('ajax-on');
                    }
                    else {
                        $(this).addClass('ajax-on');
                    }
                    $('.category-info').hide();

                    if (docfilter.hasClass('ajax-on')) {
                        $('.document-types').show();
                        $('.monument-types').hide();
                        $('.select2-results').addClass("document_results");
                        $('.select2-results').removeClass("monument_results");
                    }
                    if (monfilter.hasClass('ajax-on')) {
                        $('.monument-types').show();
                        $('.document-types').hide();
                        $('.select2-results').removeClass("document_results");
                        $('.select2-results').addClass("monument_results");
                    }

                    if (docfilter.hasClass('ajax-on') && monfilter.hasClass('ajax-on')) {
                        $('.document-types').hide();
                        $('.monument-types').hide();
                        $('.category-info').show();
                    }
                }
        });
        doctype.on({
                change: function () {
                    $(this).addClass('ajax-on');
                    getPageObjects(1);
                }
        });
        montype.on({
            change: function () {
                $(this).addClass('ajax-on');
                getPageObjects(1);
            }
        });

        var sorters = $(' #filter-grey #recent-label .object-sorters a ');
        sorters.on(
            {
                click: function () {
                    $(this).siblings('a').removeClass('ajax-on');
                    $(this).addClass('ajax-on');
                    if ($(this).attr('data-order') === 'ASC') $(this).attr('data-order', 'DESC'); else $(this).attr('data-order', 'ASC');
                }
            });
        filters.add(sorters).on(
            {
                click: function (event) {
                    event.preventDefault();
                    getPageObjects(1);
                    window.location.hash = '';
                }
            });
    };

    getPageObjects = function (page) {//console.info(page);
        var post_types = [];
        $(' #filter-grey #recent-label .object-filter a.ajax-on ').each(function () {
            post_types.push($(this).attr('data-type'));
        });
        var sorter = $(' #filter-grey #recent-label .object-sorters a.ajax-on ').attr('data-order-by');
        var order = $(' #filter-grey #recent-label .object-sorters a.ajax-on ').attr('data-order');
        var doctype = $('#filter-grey #recent-label #document-type.ajax-on').val();
        var montype = $('#filter-grey #recent-label #monument-type.ajax-on').val();
        var data = {action: 'get_page_objects', post_type: post_types, orderby: sorter, order: order, paged: page};
        if (doctype !== null && doctype !== undefined && post_types.length === 1 && post_types[0] === 'document')data.doctype = doctype;
        if (montype !== null && montype !== undefined && post_types.length === 1 && post_types[0] === 'monument')data.montype = montype;
        var keyword = $("#searchword").val();
        if (keyword !== "") data.keyword = keyword;
        console.info(data);
        $('#ajax-wrapper').addClass('preloader');
        $.post(otwarte_data.ajax_url, data, function (response) {
            if (response !== -1) {
                $('#ajax-wrapper').html(response);//console.info(response);
                if ($('#ajax-wrapper img').length > 0) {
                    $('#ajax-wrapper img').last().load(function () {
                        thumb_events();
                        $('#ajax-wrapper').removeClass('preloader');
                    });
                }
                else {
                    $('#ajax-wrapper').removeClass('preloader');
                }
            }
            paginationRefresh();
        });
    };

    paginationRefresh = function () {
        $('.pagination a').on(
            {
                click: function (event) {
                    event.preventDefault();
                    var page;
                    if ($(this).hasClass('prev'))page = parseInt($('.pagination .current').parent().prev().children().text());
                    if ($(this).hasClass('next'))page = parseInt($('.pagination .current').parent().next().children().text());
                    if (!($(this).hasClass('next') || $(this).hasClass('prev')))page = parseInt($(this).text());
                    window.location.hash = 'strona' + page;
                    console.info(page);//console.info(( $(this).hasClass('next') ||  $(this).hasClass('prev')));
                    if ($('body').hasClass('page-template-searchpage-php'))getPageObjects(page);
                    if ($('body').hasClass('page-template-my_folders-php'))getUserFolderPage(page);
                }
            });
    };// pushstate handler

    triggerAjaxPaginationOnHash = function () {// general search results on hash change
        var page;
        if ($('#search-results.search-base').length > 0) {
            $(window).on('hashchange', function () {
                page = window.location.hash;
                page = parseInt(page.replace('#strona', ''));
                if (isNaN(page)) page = 1;
                getPageObjects(page);
            });
            page = window.location.hash;
            page = parseInt(page.replace('#strona', ''));
            if (isNaN(page) || page === '') page = 1;
            getPageObjects(page);
        }
        // user selected posts on hash change
        if ($('#search-results.user-folder').length > 0) {
            $(window).on('hashchange', function () {
                var page = window.location.hash;
                page = parseInt(page.replace('#strona', ''));
                if (isNaN(page)) page = 1;
                getUserFolderPage(page);//alert('whata-whata');
            });
            page = window.location.hash;
            page = parseInt(page.replace('#strona', ''));
            if (isNaN(page) || page === '') page = 1;
        }
    };// draggable and droppable elements for users favs

    activateUserDnD = function () {
        var droppers = $('#my-cats-cnt .sf-cnt:not(.add-cnt)').droppable(
            {
                hoverClass: 'about-to-drop', drop: function (event, ui) {
                var dropped = $('.ui-draggable-dragging').attr('data-post-id');
                var folder = $(this).find('.single-folder');
                var posts_in = folder.attr('data-post-in');
                if (posts_in !== "")
                    posts_in = posts_in.split(',');
                else
                    posts_in = [];
                if (posts_in.indexOf(dropped) === -1) {
                    posts_in.push(dropped);
                    folder.attr('data-post-in', posts_in);
                    var folder_id = parseInt(folder.attr('data-folder-id'));
                    var posts = folder.attr('data-post-in');
                    console.info(folder_id, posts);
                    var data = {action: 'save_after_change', folder: folder_id, posts: posts};
                    $.post(otwarte_data.ajax_url, data, function (response) {
                        makeObjectAnimate(folder.parent(), 'bounce');
                    });
                    /* if ($(this).hasClass('browsed')) */
                    $(this).trigger('click');
                }
                else {
                    $('#msg-reveal p').html('Ten element znajduje się już w tym folderze!');
                    $('#msg-reveal').foundation('reveal', 'open');
                }
                // console.info();
            }
            });
        droppers.on({click: loadUserFolder});
    };// load user folder

    loadUserFolder = function (e) {
        e.preventDefault();
        $('.sf-cnt.browsed').removeClass('browsed');
        $(this).addClass('browsed');
        var folder = $(this).find('.single-folder');
        var f_name = $(this).find('.title-text').html();
        var post__in = folder.attr('data-post-in');
        if (post__in !== "") post__in = post__in.split(','); else post__in = false;
        if (post__in) {
            var data = {action: 'load_user_folder', posts: post__in, f_name: f_name, folders_page: true};// and open closed container
            makeOpenFolderDroppable();
            jQuery('#search-results').css('height', jQuery('#search-results').outerHeight());
            $('#ajax-wrapper').addClass('preloader');
            $.post(otwarte_data.ajax_url, data, function (response) {
                if (response !== -1) {
                    $('#ajax-wrapper').html(response);//console.info(response);
                    jQuery('#search-results').css('height', 'auto');
                    if ($('#ajax-wrapper img').length > 0) {
                        $('#ajax-wrapper img').last().load(function () {
                            $('#ajax-wrapper').removeClass('preloader');
                            thumb_events();
                        });
                    }
                    else {
                        $('#ajax-wrapper').removeClass('preloader');
                    }
                }
                paginationRefresh();
                $(document).foundation('interchange', 'reflow');
            });
            scrollToElement('#search-results');
        }
        else {
            $('#msg-reveal p').html('W tym folderze nie ma żadnych elementów');
            $('#ajax-wrapper *').remove();
            $('#msg-reveal').foundation('reveal', 'open');
            $('#search-results').addClass('closed');
        }
    };

    getUserFolderPage = function (page) {
        var post__in = $('.sf-cnt.browsed .single-folder').attr('data-post-in');
        if (post__in !== "") posts = post__in.split(','); else posts = false;

        //console.info(post__in);
        var data = {action: 'load_user_folder', posts: posts, paged: page};//console.info(clicked);
        if (posts) {
            $('#ajax-wrapper').addClass('preloader');
            $.post(otwarte_data.ajax_url, data, function (response) {
                if (response !== -1) {
                    $('#ajax-wrapper').html(response);//console.info(response);
                    if ($('#ajax-wrapper img').length > 0) {
                        $('#ajax-wrapper img').last().load(function () {
                            $('#ajax-wrapper').removeClass('preloader');
                        });
                    }
                    else {
                        $('#ajax-wrapper').removeClass('preloader');
                    }
                }
                paginationRefresh();
                thumb_events();
                $(document).foundation('interchange', 'reflow');
            });
        }
    };//add user folder

    addUserFolder = function () {
        $('#add-user-folder').on({click: addSingleFolder});
        $('#add-folder').on(
            {
                submit: function (e) {
                    e.preventDefault();
                }
            });
        $('#folder-name').on(
            {
                keypress: function (e) {
                    if (e.keyCode === 13) {
                        $(this).parent().trigger('submit');
                        addSingleFolder(e);
                    }
                }
            });
        $(document).foundation('abide', 'events');
        $('#addfolder-reveal .large-6').on(
            {
                click: function () {
                    $(this).siblings().removeClass('cover-sel');
                    $(this).addClass('cover-sel');
                    $('#folder-cover').val($(this).attr('data-cover-id'));
                }
            });
    };// helper function

    addSingleFolder = function (e) {
        e.preventDefault();
        $('#folder-name').focus();
        var form = $('#add-folder');
        form.trigger('validate');
        var browsed = $('.sf-cnt.browsed .single-folder').attr('data-folder-id');// cover chooser
        if (form.find('[data-invalid]').length <= 0) {
            var data = {action: 'add_user_folder', name: $('#folder-name').val(), cover: $('#folder-cover').val()};
            $('#addfolder-reveal').foundation('reveal', 'close');
            $.post(otwarte_data.ajax_url, data, function (response) {
                $('#my-cats-cnt').html(response);
                activateUserDnD();
                addUserFolder();
                removeUserFolder();
                reColumnFolders();// if some folder was browsed before
                if (browsed !== undefined) {
                    $('.sf-cnt .single-folder[data-folder-id=' + browsed + ']').parent().addClass('browsed');
                }
                $(document).foundation('interchange', 'reflow');
            });
        }
    };

    removeUserFolder = function () {
        $('.rem-folder.ico').on(
            {
                click: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    confrimationAction($(this), 'removeFolder');
                },
                removeFolder: removeSingleFolder
            });
    };// boolean make confirmation message

    confrimationAction = function (clicked, action) {
        $('#msg-reveal p').html('<span class="conf-msg"> Czy napewno chcesz usunąć ten folder? </span> ');
        var yes = '<span class="oz-button confirm"> Tak </span>';
        var no = '<span class="oz-button no-confirm"> Nie </span>';
        var buttons = $(yes).add($(no));
        $('#msg-reveal p').append(buttons);
        $('#msg-reveal p span.oz-button').wrapAll('<div class="button-group">');
        $('#msg-reveal').foundation('reveal', 'open');
        $('#msg-reveal .confirm').on(
            {
                click: function () {
                    clicked.trigger(action);
                    console.info(clicked);
                    $('#msg-reveal').foundation('reveal', 'close');
                }
            });
        $('#msg-reveal .no-confirm').on(
            {
                click: function () {
                    $('#msg-reveal').foundation('reveal', 'close');
                }
            });
    };

    removeSingleFolder = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var father = $(this).parent();
        var browsed = $('.sf-cnt.browsed .single-folder').attr('data-folder-id');
        if (father.parent().hasClass('browsed')) {
            $('#search-results').addClass('closed');
            $('#ajax-wrapper *').remove();
        }
        makeObjectAnimate(father.parent(), 'fadeOutDown');
        var data = {action: 'remove_user_folder', fid: parseInt(father.attr('data-folder-id'))};
        $.post(otwarte_data.ajax_url, data, function (response) {
            $('#my-cats-cnt').html(response);
            $('#addfolder-reveal').foundation('reveal', 'close');
            activateUserDnD();
            addUserFolder();
            removeUserFolder();
            reColumnFolders();// if the one that was removed wasnt browsed
            if (browsed !== undefined) {
                $('.sf-cnt .single-folder[data-folder-id=' + browsed + ']').parent().addClass('browsed');
            }
            else {
                $('#search-results').addClass('closed');
            }
            $(document).foundation('interchange', 'reflow');
        });
    };// keep the folders visible

    reColumnFolders = function () {// run only for my folders page
        if (($('#my-folders-wrapper').length > 0)) {
            var sep = jQuery('<div class="row">');
            var w_width = $(window).width();
            var scale = 6;
            if (w_width >= 1440 && w_width < 1920) scale = 5;
            if (w_width >= 1024 && w_width < 1440) scale = 4;
            if (w_width >= 768 && w_width < 1024) scale = 3;
            if ($('#my-favs').hasClass('bar-opened')) {
                $('#my-cats-cnt > .row').remove();
                $('.sf-cnt').each(function (index, value) {
                    if ((index + 1) % scale === 0) sep.clone().insertAfter($(this));
                });
            }
            else {// reflow divs
                $('#my-cats-cnt >.row').remove();
                $('.sf-cnt').each(function (index, value) {
                    if ((index + 1) % 6 === 0) sep.clone().insertAfter($(this));
                });
            }
        }
    };// make items from open folders draggable

    dragItemInsideFolder = function () {
        var draggers = $('#search-results.user-folder .single-thumb ');
        draggers.draggable({helper: "clone"});
    };// remove single item from folder

    removeSingleItemFomFolder = function (e) {
        e.stopPropagation();
        var folder = $('.sf-cnt.browsed .single-folder');
        var posts_in = folder.attr('data-post-in');
        var toRem = $(this).parents('.single-thumb').attr('data-post-id');
        if (posts_in !== "") posts_in = posts_in.split(','); else posts_in = [];
        var index = posts_in.indexOf(toRem);
        if (index > -1) {
            makeObjectAnimate($(this).parents('.large-3'), 'fadeOutDown');
            posts_in.splice(index, 1);
            folder.attr('data-post-in', posts_in);
            var folder_id = parseInt(folder.attr('data-folder-id'));
            var posts = folder.attr('data-post-in');
            var data = {action: 'save_after_change', folder: folder_id, posts: posts};
            $.post(otwarte_data.ajax_url, data, function (response) {
                $('.sf-cnt.browsed').trigger('click');
                makeObjectAnimate($('.sf-cnt.browsed'), 'bounce');
            });
        }
    };// make open folder droppable

    makeOpenFolderDroppable = function () {
        $('#search-results').removeClass('closed');
        $('#search-results').droppable(
            {
                hoverClass: function () {
                    if ($('.ui-draggable-dragging').hasClass('single-thumb')) return 'ui-infolder';
                    else
                        return 'drop-it';
                },
                drop: function () {
                if (!$('.ui-draggable-dragging').hasClass('single-thumb')) {
                    var dropped = $('.ui-draggable-dragging').attr('data-post-id');
                    var folder = $('.sf-cnt.browsed .single-folder');
                    var posts_in = folder.attr('data-post-in');
                    if (posts_in !== "") posts_in = posts_in.split(','); else posts_in = [];
                    if (posts_in.indexOf(dropped) === -1) {
                        posts_in.push(dropped);
                        folder.attr('data-post-in', posts_in);
                        var folder_id = parseInt(folder.attr('data-folder-id'));
                        var posts = folder.attr('data-post-in');
                        console.info(folder_id, posts);
                        var data = {action: 'save_after_change', folder: folder_id, posts: posts};
                        makeObjectAnimate($('.sf-cnt.browsed'), 'bounce');
                        $.post(otwarte_data.ajax_url, data, function (response) {
                            $('.sf-cnt.browsed').trigger('click');
                        });
                        if ($(this).hasClass('browsed'))$(this).trigger('click');
                    }
                    else {
                        $('#msg-reveal p').html('Ten element znajduje się już w tym folderze!');
                        $('#msg-reveal').foundation('reveal', 'open');
                    }
                }
                else {
                    console.info('alreadyinit');
                }
            }
            });
    };// make object animate: fadeOutDown or bounce

    makeObjectAnimate = function (object, animation) {
        $(object).removeClass(animation + ' animated').addClass(animation + ' animated');
        var wait = window.setTimeout(function () {
            $(object).removeClass(animation + ' animated');
        }, 1300);
    };// scroll to elements

    scrollToElement = function (el) {
        if ($(el).length > 0) {
            var winTop = $(el).offset().top;
            $('html, body').animate({scrollTop: winTop}, 1000, 'swing');
        }
    };//adding to favourrites from single view

    handle_button_star_click = function (event) {
        event.preventDefault();
        var add;
        var idToHandle = parseInt($(this).parents('.popup-info').attr('data-post-id'));
        var post_type = $(this).parents('.single-thumb').attr('data-post-type');
        if ($(this).hasClass('alreadyfav')) add = false; add = true;
        if (add) $(this).find('span:not(.ico)').html('Odznacz');else $(this).find('span:not(.ico)').html('Zaznacz');
        //console.info(idToHandle);
        if (add) add_to_fav(idToHandle, $(this)); else remove_fav(idToHandle, $(this));
    };

    singlePopupEvents = function () {
        $('.accordion').foundation('section', {multi_expand: true});//$(document).foundation('foundation', 'section', 'reflow');
        $('.popup-info a.fav-cnt').on({click: handle_button_star_click});
        $('.accordion section a').on(
            {
                click: function (e) {
                    e.preventDefault();
                }
            });
        $('.accordion .title').on(
            {
                click: function (e) {
                    e.preventDefault();
                    if ($(this).closest('section').hasClass('active')) {
                        $(this).closest('section').find('.content').slideToggle('slow');
                        $(this).closest('section').removeClass('active');
                    }
                    else {
                        $(this).closest('section').find('.content').slideToggle('slow');
                        $(this).closest('section').addClass('active');//console.info($(this).siblings('section'));
                        var map = $("#monument-map").gmap3('get');
                        google.maps.event.trigger(map, 'resize');
                        $("#monument-map").gmap3({autofit: {}, map: {options: {zoom: 16}}});
                    }
                }
            });
        $('.popup-nav:not(.inside-nav)').on({click: get_single_object});
        $('.inside-nav').on({click: get_single_object_inside});
        $('.single-cnt .info-button').on(
            {
                click: function () {
                    $('.popup-controls.document-controls').toggleClass('open');
                    $('#detailed-info .top-section').slideToggle('slow');
                    $(this).toggleClass('open');
                }
            });
        $('.close-popup').on(
            {
                click: function () {
                    if ($('#object-reveal').attr('data-old-parent-url') !== '' && $('#object-reveal').attr('data-use-parent') === '1') {
                        window.history.pushState(null, null, $('#object-reveal').attr('data-old-parent-url'));
                    }
                    else
                    if ($('#object-reveal').attr('data-old-url') !== '' && $('#object-reveal').attr('data-old-parent-url') !=='') {
                        window.history.pushState(null, null, $('#object-reveal').attr('data-old-url'));
                        $('#object-reveal').attr('data-use-parent', '1');
                    }
                    else if ($('#object-reveal').attr('data-old-parent-url') === '' && $('#object-reveal').attr('data-old-url') !== '') {
                        window.history.pushState(null, null, $('#object-reveal').attr('data-old-url'));
                    }
                }
            });
        singleDocumentJcarousel();
    };

    get_single_object = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).attr('data-post-id');
        var single;
        if ($(this).hasClass('popup-nav') || $(this).hasClass('close-popup')) {
            single = $('#search-results .single-thumb[data-post-id=' + $(this).attr('data-post-id') + ']');
            if ($('body').hasClass('home')) single = $('.single-thumb[data-post-id=' + $(this).attr('data-post-id') + ']');
        }
        else {
            single = $(this);
        }
        var href = $('a', this).attr('href');
        var title = $('.title-text', this).text();
        var next = single.parent().next();
        var prev = single.parent().prev();
        if (next.hasClass('row')) {
            next = next.next();
        }
        if (prev.hasClass('row')) {
            prev = prev.prev();
        }
        var prev_id = prev.find('.single-thumb').attr('data-post-id');
        var next_id = next.find('.single-thumb').attr('data-post-id');
        var data = {action: 'get_single_object', object_id: id, next: next_id, prev: prev_id};
        if (!$('#object-reveal').hasClass('open'))$('#object-reveal').foundation('reveal', 'open');
        $('#object-reveal .ajax-result').addClass('preloader');//$(".attachment-full").smartZoom('destroy');
        $.post(otwarte_data.ajax_url, data, function (response) {
            console.info(response);
            $('#object-reveal .ajax-result').html(response);//$('#object-reveal').foundation('reveal', 'open');
            scrollToElement($('#object-reveal'));
            singlePopupEvents();
            dynamicElements();
            $('#object-reveal').attr('data-old-url', window.location.href);
            window.history.pushState(title, title, href);

            window.onpopstate = function (event) {
                $('a.close-popup.close-reveal-modal').trigger('click');
            };
            $('.single-cnt img').last().load(function () {
                insidePopupThumbEvents();
                $('#object-reveal .ajax-result').removeClass('preloader');
                $('#related-docs>section>div').removeClass('preloader');
                $(".attachment-full").wheelzoom();
                $('#nav-prev-page').on(
                    {
                        click: function (event) {
                            if ($('#docs-scans li.active').prev().length !== 0)$('#docs-scans li.active').prev().find('a').trigger('click');
                        }
                    });
                $('#nav-next-page').on(
                    {
                        click: function (event) {
                            if ($('#docs-scans li.active').next().length !== 0)$('#docs-scans li.active').next().find('a').trigger('click');
                        }
                    });
                $('#zoomInButton').on(
                    {
                        click: function () {
                            $(".attachment-full").trigger('wheelzoom.zoomin');
                        }
                    });
                $('#zoomOutButton').on(
                    {
                        click: function () {
                            $(".attachment-full").trigger('wheelzoom.zoomout');
                        }
                    });
            });
            if ($('.single-cnt img').length === 0) {
                insidePopupThumbEvents();
                $('#object-reveal .ajax-result').removeClass('preloader');
                $('#related-docs>section>div').removeClass('preloader');
                $(".attachment-full").wheelzoom();
                $('#nav-prev-page').on(
                    {
                        click: function (event) {
                            if ($('#docs-scans li.active').prev().length !== 0)$('#docs-scans li.active').prev().find('a').trigger('click');
                        }
                    });
                $('#nav-next-page').on(
                    {
                        click: function (event) {
                            if ($('#docs-scans li.active').next().length !== 0)$('#docs-scans li.active').next().find('a').trigger('click');
                        }
                    });
                $('#zoomInButton').on(
                    {
                        click: function () {
                            $(".attachment-full").trigger('wheelzoom.zoomin');
                        }
                    });
                $('#zoomOutButton').on(
                    {
                        click: function () {
                            $(".attachment-full").trigger('wheelzoom.zoomout');
                        }
                    });
            }
            monument_map();
        });
    };

    get_single_object_inside = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).attr('data-post-id');
        var single;
        var mon_docs, mon_id;
        var href = $('a', this).attr('href');
        var title = $('.title-text', this).text();
        if ($(this).hasClass('inside-nav')) {
            single = $(this).attr('data-post-id');
            mon_docs = $('#inside-controler').attr('data-documents-ids');
            mon_id = $('a.close-popup').attr('data-post-id');
        }
        else {
            single = $(this);
            mon_docs = $('#related-docs').attr('data-documents-ids');
            mon_id = $('#monument-controller').attr('data-monument-id');
        }
        var data = {action: 'get_single_object', object_id: id, mon_docs: mon_docs, inside: true, mon_id: mon_id};
        if (!$('#object-reveal').hasClass('open'))$('#object-reveal').foundation('reveal', 'open');
        $('#object-reveal .ajax-result').addClass('preloader');// $(".attachment-full").smartZoom('destroy');
        $.post(otwarte_data.ajax_url, data, function (response) {//console.info(response);
            $('#object-reveal .ajax-result').html(response);//$('#object-reveal').foundation('reveal', 'open');
            scrollToElement($('#object-reveal'));
            singlePopupEvents();
            dynamicElements();
            $('#object-reveal').attr('data-old-parent-url', $('#object-reveal').attr('data-old-url'));
            $('#object-reveal').attr('data-old-url', window.location.href);
            window.history.pushState(title, title, href);

            window.onpopstate = function (event) {
                $('a.close-popup').trigger('click');
            };
            $('.single-cnt img').last().load(function () {
                insidePopupThumbEvents();
                $('#object-reveal .ajax-result').removeClass('preloader');
                $('#related-docs>section>div').removeClass('preloader');
                $(".attachment-full").wheelzoom();
                $('#zoomInButton').on(
                    {
                        click: function () {
                            $(".attachment-full").trigger('wheelzoom.zoomin');
                        }
                    });
                $('#zoomOutButton').on(
                    {
                        click: function () {
                            $(".attachment-full").trigger('wheelzoom.zoomout');
                        }
                    });
                $('#nav-prev-page').on(
                    {
                        click: function (event) {
                            alert();
                            if ($('#docs-scans li.active').prev().length !== 0)$('#docs-scans li.active').prev().trigger('click');
                        }
                    });
                $('#nav-next-page').on(
                    {
                        click: function (event) {
                            if ($('#docs-scans li.active').next().length !== 0)$('#docs-scans li.active').next().trigger('click');
                        }
                    });
            });
        });
    };

    insidePopupThumbEvents = function () {
        $('#related-docs .single-thumb .fav-star-ico').on({click: handle_star_click});//make them draggable inside open folder
        //dragItemInsideFolder();
        // removeSingleItemFromFolder
        $('#related-docs .rem-from-fol').on({click: removeSingleItemFomFolder});
        $('#related-docs h4.label span.title-text').each(function () {
            var parent = $(this).parent('h4');// console.info('elem', $(this),'parent', parent, 'wysEl', $(this).height(), 'wysPar',  parent.height());
            if (($(this).height() + 16) >= parent.height()) parent.height($(this).height() + 15);else parent.css('height', 'auto');
        });// rescale titles after reloading
        $(document).foundation('interchange', 'reflow');
        $('#related-docs .single-thumb[data-post-id]').on({click: get_single_object_inside});
        if (!$('#inside-controler a.close-popup').hasClass('close-reveal-modal')) {
            $('#inside-controler a.close-popup').on({click: get_single_object});
        }
    };

    singleDocumentJcarousel = function () {
        var jcarousel = $('.jcarousel');
        jcarousel.on('jcarousel:reload jcarousel:create', function () {
            var width = jcarousel.innerWidth();
            if (width >= 600) {
                width = width / 3;
            }
            else if (width >= 350) {
                width = width / 2;
            }
            jcarousel.jcarousel('items').css('width', 120 + 'px');
        });
        jcarousel.jcarousel({wrap: null});
        $('.jcarousel-control-prev').jcarouselControl({target: '-=1'});
        $('.jcarousel-control-next').jcarouselControl({target: '+=1'});

        $('#docs-scans .jcarousel').on(
            {
                click: function (e) {
                    e.preventDefault();
                    var cur = $('#docs-scans .jcarousel').find('li.active');// // console.info(cur);
                    cur.removeClass('active');
                    var ind = $(this).parent().index() + 1;
                    $('.page-start').html(ind);
                    $(this).parent().addClass('active');// jQuery(".attachment-full").trigger('wheelzoom.reset');
                    // $('#document-browser img').css('background-image', 'url(' + $(this).attr('data-full-src') + ')');
                    $('#document-browser img').remove();
                    $('#document-browser .zoom-container').html('<div class="preloader">&nbsp</div>');
                    $('html, body').animate({scrollTop: 0}, 500);
                    var full_src = $(this).attr('data-full-src');
                    var img = $('<img src="' + full_src + '" style="background-image: url(' + full_src + ');">').load(function () {
                        $('#document-browser .zoom-container .preloader').remove();
                        $('#document-browser .zoom-container').html(img);
                        $('#document-browser .zoom-container img').wheelzoom();
                        $('#document-browser .zoom-container img').css('background-image', 'url(' + full_src + ')');
                    });
                }
            }, 'a');
    };

    searchFunction = function(){
        //searchPage
        $("#keyword_search").on({
            submit: search_by_keyword
        });
        $("#keyword_search span").on({
            click: search_by_keyword
        });

        $("#document-type").select2({
            dropdownParent: ".document-types"
        });

        $("#monument-type").select2({
            dropdownParent: ".document-types"
        });
    };

    search_by_keyword = function (event) {
        event.preventDefault();
        getPageObjects(1);
    };

    mobileMenu = function(){
        var showRightPush = document.getElementById( 'showRightPush' ), body = document.body, menuRight = document.getElementById( 'cbp-spmenu-s2' );
        showRightPush.onclick = function() {
            classie.toggle( this, 'active' );
            classie.toggle( body, 'cbp-spmenu-push-toleft' );
            classie.toggle( menuRight, 'cbp-spmenu-open' );
        };
    };

})(jQuery);