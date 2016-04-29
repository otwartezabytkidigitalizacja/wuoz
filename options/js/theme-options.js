jQuery(function($){

    $.fn.delete_slide = function() {
        this.parent('.slide').remove();
        get_slider_data();
    };

    $.fn.delete_icon = function() {
        this.parent('.folder-icon').remove();
        get_icons_data();
    }

    function get_slider_data() {
        var slides = new Array();
        $('#slides .slide').each(function(index, el) {
            slides[index]={
                text: $('.slide-text', this).text(),
                li_1: $('.li-1', this).text(),
                li_2: $('.li-2', this).text(),
                li_3: $('.li-3', this).text(),
            };           
        });
        //console.log(slides);
        $('#oz_slides').val(JSON.stringify(slides));
    }

    function get_icons_data() {
        var icons = new Array();
        $('#folder-icons .folder-icon').each(function(index, el) {
            icons[index]=$(el).data('icon-id');
        });
        //console.log(icons);
        $('#oz_folder_icons').val(JSON.stringify(icons));
    }

    var icon_media_frame;

    $(document).ready(function(){
        $('#theme-options-accordion').accordion({active: false, collapsible: true, heightStyle: "content"});
        $('#slides').sortable({
            update: get_slider_data,
        });

        $('#add-slide').click(function(event) {
            var element = $('<div class="slide"><div class="slide-text">'
                                +$('#slide-text').val()
                                +'</div><div class="slide-list">'
                                +'<ul>'
                                +'<li class="li-1">'+$('#slide-li-1').val()+'</li>'
                                +'<li class="li-2">'+$('#slide-li-2').val()+'</li>'
                                +'<li class="li-3">'+$('#slide-li-3').val()+'</li>'
                                +'</ul>'
                                +'</div><div class="delete"></div></div>');

            $('#slides').append(element);
            $('.delete', element).click(function(){
                $(this).delete_slide();
            });
            get_slider_data();
        });

        $('.slide .delete').click(function(){
            $(this).delete_slide();
        });

        $('.folder-icon .delete').click(function() {
            $(this).delete_icon();
        });

        $('#add-folder-icon').click(function(event) {
            if(icon_media_frame) {
                icon_media_frame.open();
                return;
            }

            icon_media_frame = wp.media.frames.icon_media_frame = wp.media({
                frame: 'select',
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            icon_media_frame.on('select', function(){
                var media_attachment = icon_media_frame.state().get('selection').first().toJSON();
                var icon = $('<div class="folder-icon" data-icon-id="'+media_attachment.id+'" style="background-image: url('+media_attachment.url+');"><div class="delete"></div></div>');
                $('#folder-icons').prepend(icon);
                get_icons_data();
            });

            icon_media_frame.open();
        });
    });
});