jQuery(function($){	

	function update_fields() {
		var jpegs = [];
		$('#jpeg-list .jpeg').each(function(i, val){
			jpegs[i]=$(this).data('id');
			$('.delete', this).unbind('click');
			$('.delete', this).click(function(event) {
				event.preventDefault();
				$(this).parent('.jpeg').remove();
				update_fields();
			});
		});
		$('#oz_jpegs').val(JSON.stringify(jpegs));
		$('#oz_pdf').val($('#pdf-list').attr('data-id'));
		$('#oz_djvu').val($('#djvu-list').attr('data-id'));
	}

	$(document).ready(function(){
		$('#jpeg-list').sortable({
            update: update_fields,
        });
        update_fields();

        $('.delete-doc').click(function(event) {
        	event.preventDefault();
        	var type = $(this).data('type');
        	$('#'+type+'-list').attr('data-id', '');
        	$('#'+type+'-list').html('---------');
        	update_fields();
        });

		var document_media_frame;
		var current_frame_type;
    
	    $('.add-file').click(function(event){
	    	current_frame_type = $(this).data('type');
	    	if($(this).data('type')=='jpeg')
	    		var multiple = true;
	    	else
	    		var multiple = false;
	        event.preventDefault();
	        
	        document_media_frame = wp.media.frames.document_media_frame = wp.media({
	            className: 'media-frame',
	            frame: 'select',
	            multiple: multiple,
	        });

	        document_media_frame.on('select', function(){
	            var media_attachment = document_media_frame.state().get('selection').toJSON();
	            if(current_frame_type=='jpeg') {
	            	$.each(media_attachment, function(i, val) {
	            		var html ='<div class="jpeg" data-id="'+val.id+'"><img src="'+val.url+'"><a href="#" class="delete"><a href="'+val.editLink+'" class="post-edit-link"></div>';
	            		$('#jpeg-list').append(html);
	            	});
	            	
	            }
	            else {
	            	var html = '<a href="'+media_attachment[0].editLink+'">'+media_attachment[0].title+'</a>';
	            	$('#'+current_frame_type+'-list').html(html);
	            	$('#'+current_frame_type+'-list').attr('data-id', media_attachment[0].id);
	            } 
	            update_fields();
	        });
	        document_media_frame.open();
	    });
	});
});