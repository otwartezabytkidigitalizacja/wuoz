jQuery(function($){
	$(document).ready(function(){
		$('#documents-list').dataTable({
		 	"bPaginate": false,
        	"bLengthChange": false,
        	"bAutoWidth": false,
		 	"oLanguage" :{
				"sProcessing":   "Proszę czekać...",
				"sLengthMenu":   "Pokaż _MENU_ pozycji",
				"sZeroRecords":  "Nie znaleziono żadnych pasujących indeksów",
				"sInfo":         "Pozycje od _START_ do _END_ z _TOTAL_ łącznie",
				"sInfoEmpty":    "Pozycji 0 z 0 dostępnych",
				"sInfoFiltered": "(filtrowanie spośród _MAX_ dostępnych pozycji)",
				"sInfoPostFix":  "",
				"sSearch":       "Szukaj:",
				"sUrl":          "",
				"oPaginate": {
					"sFirst":    "Pierwsza",
					"sPrevious": "Poprzednia",
					"sNext":     "Następna",
					"sLast":     "Ostatnia"
				}
			}
		 });

		$('#monument').change(function(event) {
			$('#connected-documents').html('');
			data = {
					action: 'get_documents_list',
					monument_id: $(this).val(),
				};	
			$.post(otwarte_data.ajax_url, data, function(response) {
				var documents = JSON.parse(response);
				if(response==null)
					return;

				$.each(documents, function(index, val) {
					var doc = val;
					console.log(doc);
					$('#connected-documents').append($('<div class="doc">'+doc.title+'<span class="delete-doc" data-id="'+doc.id+'">x</span></div>'));

					$('.delete-doc').click(function(event) {
						var doc = $(this).parent('.doc');
						$(doc).css('background-color', '#cccccc');
						var data = {
							action: 'disconnect',
							monument_id: $('#monument').val(),
							document_id: $(this).data('id'),
						}
						$.post(otwarte_data.ajax_url, data, function(response) {
							$(doc).remove();
						});
					});
				});
			});
		});

		$('#monument').trigger('change');

		$('.connect').click(function(event) {
			if($('#monument').val()=='') {
				alert("Wybierz najpierw zabytek!");
				return;
			}
			var data = {
				action: 'connect',
				monument_id: $('#monument').val(),
				document_id: $(this).data('id'),
			}
			$.post(otwarte_data.ajax_url, data, function(response) {
				var doc = JSON.parse(response);
				$('#connected-documents').append($('<div class="doc">'+doc.title+'<span class="delete-doc" data-id="'+doc.id+'">x</span></div>'));
				$('.delete-doc').click(function(event) {
						var doc = $(this).parent('.doc');
						$(doc).css('background-color', '#cccccc');
						var data = {
							action: 'disconnect',
							monument_id: $('#monument').val(),
							document_id: $(this).data('id'),
						}
						$.post(otwarte_data.ajax_url, data, function(response) {
							$(doc).remove();
						});
					});
			});
		});
	});
});