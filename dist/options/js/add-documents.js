jQuery(function($){
	var documents = new Array();
	var documentsSize = 0;
	var progress = 1;
	var error = false;
	$(document).ready(function(){
		

		$('.check-all').change(function(){
			var table = $(this).parents('table');
			if($(this).attr('checked')=='checked')
				$('input[type=checkbox]', table).slice(0,101).attr('checked', true);
			else
				$('input[type=checkbox]', table).attr('checked', false);
		});

		$('#documents-start').click(function(){
			$('input[type=checkbox]').attr('disabled', 'disabled');
			$('#documents-start').attr('disabled', 'disabled');
			$('#documents-start').text('Czekaj...');

			$('.document-data:checked').each(function(index, value){
				documents[index] = {
					action: 'upload_document',
					type: $(this).data('type'),
					path: $(this).data('path'),
					signature: $(this).data('signature'),
					monument: $('#monument').val()
				};			
				documentsSize++;				
			});
			$('#info-all-number').text(documentsSize);
			$('#documents-info').fadeIn();
			processDocuments();
		});
	});

	function processDocuments() {
		if(documents.length===0) {
			if(error==false) {
				$('#documents-info').html('<strong>Import dokumentów zakończony</strong><br/><br/>Możesz zamknąć tę stronę.<br/><br>Aby zaimportować kolejne dokumenty: <a href="'+window.location+'">odśwież</a>');
			}
			else {
				$('#documents-info #info-import').html("Niektóre dokumenty nie zostały zaimportowane.");
			}
			return;
		}

		var data = documents[0];
		var tr_class = data['signature']+'';
		$('#info-current-document').text(tr_class);
		$('#info-current-number').text(progress);

		if(tr_class.indexOf('.')!=-1) {
			var pattern = new RegExp('.', 'g');
			tr_class = tr_class.replace(/\./g, "-");
		}
		else {
			tr_class = tr_class;
		}
		$('.tr-'+tr_class+' .spinner').fadeIn();
		documents.shift();
		console.log(data);
		$.ajax({
			url:    otwarte_data.ajax_url,
			type: "POST",
			data: data,
			timeout: 900000,
			success: function(result) {
				$('.tr-'+tr_class+' .spinner').fadeOut();
				$('.tr-'+tr_class+' .status').html('&#10004;');
				if (result.indexOf("ok") == -1) {
					var error_text = $('<div class="oz-error"><div class="oz-error-header">Błąd</div>Sygnatura: '+tr_class+'<br/>Wiadomość:'+result.replace(/\n/g,"<br>").replace(/(<br\s*\/?>){3,}/gi, '<br>')+'<br/>Dokument nie został dodany</div>');
					$('#documents-info').append(error_text);
					error = true;
					$('.tr-'+tr_class+' .status').html('<span style="color: red;">błąd</span>');
				}
				progress++;
				processDocuments();
			}
		});	
	}

});