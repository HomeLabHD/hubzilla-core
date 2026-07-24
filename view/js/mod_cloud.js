/**
 * JavaScript for mod/cloud
 */

$(document).ready(function () {

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		UploadInit();
	}

	let attach_drop_id;
	let attach_draging;
	let attach_multi_selected_count = 0;

	// Per File Tools

	// highlight and scroll into view if we got an id via hash
	if(window.location.hash) {
		$('#cloud-index-' + window.location.hash.substr(1)).addClass('cloud-index-active').get(0).scrollIntoView({block: 'center'});
	}

	$(document).on('click', '.cloud-tool-info-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		close_and_deactivate_all_panels();
		$('#cloud-tool-info-' + id).toggle();
		$('#cloud-index-' + id).addClass('cloud-index-active');
	});

	$(document).on('click', '.cloud-tool-perms-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		activate_id(id);
	});

	$(document).on('click', '.cloud-tool-rename-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		activate_id(id);
		$('#cloud-tool-rename-' + id).show();
	});

	$(document).on('click', '.cloud-tool-move-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		activate_id(id);
		$('#cloud-tool-move-' + id).show();
	});

	$(document).on('click', '.cloud-tool-categories-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		activate_id(id);
		$('#id_categories_' + id).tagsinput({
			tagClass: 'badge rounded-pill bg-warning text-dark'
		});
		$('#cloud-tool-categories-' + id).show();
	});

	$(document).on('click', '.cloud-tool-download-btn', function (e) {
		close_and_deactivate_all_panels();
	});

	$(document).on('click', '.cloud-tool-dir-download-btn', function (e) {
		e.preventDefault();
		close_and_deactivate_all_panels()

		let id = $(this).data('id');
		if(! id) {
			return false;
		}

		close_and_deactivate_all_panels();

		$('body').css('cursor', 'wait');
		toast(aStr.download_info, 'info');

		let data = [
			{name: 'attach_path', value: window.location.pathname},
			{name: 'channel_id', value: channelId},
			{name: 'attach_ids[]', value: id}
		]

		$.post('attach', data, function (data) {
			if (data.success) {
				$('body').css('cursor', 'auto');
				window.location.href = '/attach/download?token=' + data.token;
			}
		});

	});

	$(document).on('click', '.cloud-tool-delete-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');

		close_and_deactivate_all_panels();

		let confirm = confirmDelete();
		if (confirm) {
			$('body').css('cursor', 'wait');
			$('#cloud-index-' + id).css('opacity', 0.33);

			let form = $('#attach_edit_form_' + id).serializeArray();
			form.push({name: 'delete', value: 1});

			$.post('attach_edit', form, function (data) {
				if (data.success) {
					$('#cloud-index-' + id + ', #cloud-tools-' + id).remove();
					$('body').css('cursor', 'auto');
				}
				return true;
			});

		}
		return false;
	});

	$(document).on('click', '.cloud-tool-cancel-btn', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		close_and_deactivate_all_panels();
		$('#attach_edit_form_' + id).trigger('reset');
		$('#id_categories_' + id).tagsinput('destroy');
	});

	// Per File Tools Eend

	// DnD

	$(document).on('drop', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on('dragover', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on('dragleave', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on('drop', '.cloud-index.attach-drop', function (e) {
		let target = $(this);
		let folder = target.data('folder');
		let id = target.data('id');


		if(typeof folder === typeof undefined) {
			return false;
		}

		// Check if it's a file
		if (typeof e.dataTransfer !== typeof undefined && e.dataTransfer.files[0]) {
			$('#file-folder').val(folder);
			return true;
		}

		if(id === attach_drop_id) {
			return false;
		}

		if(target.hasClass('attach-drop-zone') && attach_draging) {
			return false;
		}

		target.removeClass('attach-drop-ok');

		$.post('attach_edit', {'channel_id': channelId, 'dnd': 1, 'attach_id': attach_drop_id, ['newfolder_' + attach_drop_id]: folder }, function (data) {
			if (data.success) {
				$('#cloud-index-' + attach_drop_id + ', #cloud-tools-' + attach_drop_id).remove();
				attach_drop_id = null;
			}
		});
	});

	$(document).on('dragover', '.cloud-index.attach-drop', function (e) {
		let target = $(this);

		if(target.hasClass('attach-drop-zone') && attach_draging) {
			return false;
		}

		target.addClass('attach-drop-ok');
	});

	$(document).on('dragleave', '.cloud-index', function (e) {
		let target = $(this);
		target.removeClass('attach-drop-ok');
	});

	$(document).on('dragstart', '.cloud-index', function (e) {
		let target = $(this);
		attach_drop_id = target.data('id');
		// dragstart is not fired if a file is draged onto the window
		// we use this to distinguish between drags and file drops
		attach_draging = true;
	});

	$(document).on('dragend', '.cloud-index', function (e) {
		let target = $(this);
		target.removeClass('attach-drop-ok');
		attach_draging = false;
	});

	// DnD End

	// Multi Tools

	$(document).on('change', '#cloud-multi-tool-select-all', function (e) {
		if ($(this).is(':checked')) {
			// Reset the counter here in case any were already manualy selected
			attach_multi_selected_count = 0;
			$('.cloud-multi-tool-checkbox').prop('checked', true);
			$('.cloud-index:not(#cloud-index-up)').addClass('cloud-index-selected cloud-index-active');
			$('.cloud-tools').addClass('cloud-index-selected');
		}
		else {
			$('.cloud-multi-tool-checkbox').prop('checked', false);
			$('.cloud-index').removeClass('cloud-index-selected cloud-index-active');
			$('.cloud-tools').removeClass('cloud-index-selected');
		}

		$('.cloud-multi-tool-checkbox').trigger('change');
	});

	$(document).on('change', '.cloud-multi-tool-checkbox', function (e) {
		let id = $(this).val();

		if ($(this).is(':checked')) {
			attach_multi_selected_count++;
			// Only run when the first item is selected (performance)
			if (attach_multi_selected_count === 1) {
				close_all_panels();
				$('#cloud-multi-actions').addClass('bg-warning');
				$('#multi-dropdown-button').fadeIn();
			}

			$('#cloud-index-' + id).addClass('cloud-index-selected cloud-index-active');
			$('#cloud-tools-' + id).addClass('cloud-index-selected');
			$('<input id="aid_' + id + '" class="attach-ids-input" type="hidden" name="attach_ids[]" value="' + id + '">').prependTo('#attach_multi_edit_form');
		}
		else {
			attach_multi_selected_count--;
			// Only run when the last item is deselected (performance)
			if (attach_multi_selected_count === 0) {
				$('#cloud-multi-actions').removeClass('bg-warning');
				$('#multi-dropdown-button').fadeOut();
				close_and_deactivate_all_panels();
				disable_multi_acl();
			}

			$('#cloud-index-' + id).removeClass('cloud-index-selected cloud-index-active');
			$('#cloud-tools-' + id).removeClass('cloud-index-selected');

			if ($('#cloud-multi-tool-select-all').is(':checked')) {
				$('#cloud-multi-tool-select-all').prop('checked', false);
			}

			$('#aid_' + id).remove();
		}
	});

	$(document).on('click', '#cloud-multi-tool-perms-btn', function (e) {
		e.preventDefault();

		close_all_panels();
		enable_multi_acl();

		$('#cloud-multi-tool-submit').show();
	});

	$(document).on('click', '#cloud-multi-tool-move-btn', function (e) {
		e.preventDefault();

		close_all_panels();
		disable_multi_acl();

		$('#cloud-multi-tool-submit, #cloud-multi-tool-move').show();
	});

	$(document).on('click', '#cloud-multi-tool-categories-btn', function (e) {
		e.preventDefault();

		close_all_panels();
		disable_multi_acl();

		$('#id_categories').tagsinput({
			tagClass: 'badge rounded-pill bg-warning text-dark'
		});

		$('#cloud-multi-tool-submit, #cloud-multi-tool-categories').show();
	});

	$(document).on('click', '#cloud-multi-tool-download-btn', function (e) {
		e.preventDefault();

		let post_data = $('.cloud-multi-tool-checkbox:checked').serializeArray();

		if(! post_data.length) {
			return false;
		}

		close_and_deactivate_all_panels();

		$('body').css('cursor', 'wait');
		toast(aStr.download_info, 'info');

		post_data.push(
			{name: 'attach_path', value: window.location.pathname},
			{name: 'channel_id', value: channelId}
		);

		$.post('attach', post_data, function (data) {
			if (data.success) {
				$('body').css('cursor', 'auto');
				window.location.href = '/attach/download?token=' + data.token;
			}
		});

	});

	$(document).on('click', '#cloud-multi-tool-delete-btn', function (e) {
		e.preventDefault();

		close_and_deactivate_all_panels();

		let post_data = $('.cloud-multi-tool-checkbox:checked').serializeArray();

		if(! post_data.length) {
			return false;
		}

		let confirm = confirmDelete();
		if (confirm) {
			$('body').css('cursor', 'wait');
			$('.cloud-index-selected').css('opacity', 0.33);

			post_data.push(
				{ name: 'channel_id', value: channelId },
				{ name: 'delete', value: 1},
			);

			$.post('attach_edit', post_data, function (data) {
				if (data.success) {
					console.log(data);
					$('.cloud-index-selected').remove();
					$('body').css('cursor', 'auto');
				}
				return true;
			});
		}
		return false;

	});

	$(document).on('click', '.cloud-multi-tool-cancel-btn', function (e) {
		e.preventDefault();

		close_and_deactivate_all_panels();
		disable_multi_acl();

		$('#attach_multi_edit_form').trigger('reset');
		$('#id_categories').tagsinput('destroy');
	});

	// Multi Tools End

	// Helper Functions

	function disable_multi_acl() {
		$('#multi-perms').val(0);
		$('#multi-dbtn-acl, #recurse_container').hide();
		$('#attach-multi-edit-perms').removeClass('btn-group');
	}

	function enable_multi_acl() {
		$('#multi-perms').val(1);
		$('#multi-dbtn-acl, #recurse_container').show();
		$('#attach-multi-edit-perms').addClass('btn-group');
	}

	function close_all_panels() {
		$('.cloud-tool, .cloud-multi-tool').hide();
	}

	function deactivate_all_panels() {
		$('.cloud-index').removeClass('cloud-index-active');
	}

	function close_and_deactivate_all_panels() {
		close_all_panels();
		deactivate_all_panels();
	}

	function activate_id(id) {
		close_and_deactivate_all_panels();
		$('#cloud-multi-tool-select-all:checked, .cloud-multi-tool-checkbox:checked').prop('checked', false).trigger('change');

		$('#cloud-tool-submit-' + id).show();
		$('#cloud-index-' + id).addClass('cloud-index-active');
	}

});




// initialize
function UploadInit() {

	let submit = $("#upload-submit");
	let idx = 0;
	let filedrag = $(".cloud-index.attach-drop");
	let reload = false;

	if (!$('#invisible-cloud-file-upload').length)
		return;

	$('#invisible-cloud-file-upload').fileupload({
		url: 'file_upload',
		dataType: 'json',
		dropZone: filedrag,
		maxChunkSize: 4 * 1024 * 1024,
		add: function(e,data) {

			idx++;
			data.files[0].idx = idx;
			prepareHtml(data.files[0]);

			let allow_cid = ($('#ajax-upload-files').data('allow_cid') || []);
			let allow_gid = ($('#ajax-upload-files').data('allow_gid') || []);
			let deny_cid  = ($('#ajax-upload-files').data('deny_cid') || []);
			let deny_gid  = ($('#ajax-upload-files').data('deny_gid') || []);

			$('.acl-field').remove();

			$(allow_gid).each(function(i,v) {
				$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='group_allow[]' value='"+v+"'>");
			});
			$(allow_cid).each(function(i,v) {
				$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='contact_allow[]' value='"+v+"'>");
			});
			$(deny_gid).each(function(i,v) {
				$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='group_deny[]' value='"+v+"'>");
			});
			$(deny_cid).each(function(i,v) {
				$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='contact_deny[]' value='"+v+"'>");
			});

			data.formData = $('#ajax-upload-files').serializeArray();

			// trick it into not uploadiong all files at once
			$('#new-upload-' + data.files[0].idx).one('fileupload_trigger', function () {
				data.submit();
			});

			$('#new-upload-1').trigger('fileupload_trigger');
		},

		progress: function(e,data) {
			let id = data.files[0].idx;
			if(data.loaded == data.total) {
				if(id == data.originalFiles.length) {
					reload = true;
				}
				else {
					// trigger uploading the next file
					let next_id = id + 1;
					let timeout = data.files[0].type.startsWith('image') ? 3000 : 1000;
					setTimeout(function(){ $('#new-upload-' + next_id).trigger('fileupload_trigger'); }, timeout);
				}
			}

			// Dynamically update the percentage complete displayed in the file upload list
			$('#upload-progress-' + id).html(Math.round(data.loaded / data.total * 100) + '%');
			$('#upload-progress-bar-' + id).css('width', Math.round(data.loaded / data.total * 100) + '%');

		},

		stop: function(e,data) {
			if(reload) {
				console.log('Upload completed');
				window.location.href = window.location.href;
			}
		}
	});

	$('#upload-submit').click(function(event) { event.preventDefault(); $('#invisible-cloud-file-upload').trigger('click');});

}

function prepareHtml(f) {
	var num = f.idx - 1;
	var i = f.idx;
	$('#cloud-index #new-upload-progress-bar-' + num.toString()).after(
		'<tr id="new-upload-' + i + '" class="new-upload">' +
		'<td></td>' +
		'<td><i class="fa fa-fw ' + getIconFromType(f.type) + '" title="' + f.type + '"></i></td>' +
		'<td>' + f.name + '</td>' +
		'<td id="upload-progress-' + i + '"></td><td></td><td></td>' +
		'<td class="d-none d-md-table-cell">' + formatSizeUnits(f.size) + '</td><td class="d-none d-md-table-cell"></td>' +
		'</tr>' +
		'<tr id="new-upload-progress-bar-' + i + '" class="new-upload">' +
		'<td colspan="9" class="upload-progress-bar">' +
		'<div class="progress" style="height: 1px;">' +
		'<div id="upload-progress-bar-' + i + '" class="progress-bar bg-info" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>' +
		'</div>' +
		'</td>' +
		'</tr>'
	);
}

function formatSizeUnits(bytes){
	if      (bytes>=1000000000) {bytes=(bytes/1000000000).toFixed(2)+' GB';}
	else if (bytes>=1000000)    {bytes=(bytes/1000000).toFixed(2)+' MB';}
	else if (bytes>=1000)       {bytes=(bytes/1000).toFixed(2)+' KB';}
	else if (bytes>1)           {bytes=bytes+' bytes';}
	else if (bytes==1)          {bytes=bytes+' byte';}
	else                        {bytes='0 byte';}
	return bytes;
}

// this is basically a js port of include/text.php getIconFromType() function
function getIconFromType(type) {
	let map = {
		//Common file
		'application/octet-stream': 'bi-file-earmark',
		//Text
		'text/plain': 'bi-file-earmark-text',
		'text/markdown': 'bi-filetype-md',
		'text/bbcode': 'bi-file-earmark-text',
		'text/html': 'bi-filetype-html',
		'application/msword': 'bi-file-earmark-word',
		'application/pdf': 'bi-file-earmark-pdf',
		'application/vnd.oasis.opendocument.text': 'bi-file-earmark-text',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'bi-file-earmark-text',
		'application/epub+zip': 'bi-file-earmark-text',
		//Spreadsheet
		'application/vnd.oasis.opendocument.spreadsheet': 'bi-file-earmark-spreadsheet',
		'application/vnd.ms-excel': 'bi-file-earmark-spreadsheet',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'bi-file-earmark-spreadsheet',
		//Image
		'image/jpeg': 'bi-file-earmark-image',
		'image/png': 'bi-file-earmark-image',
		'image/gif': 'bi-file-earmark-image',
		'image/webp': 'bi-file-earmark-image',
		'image/svg+xml': 'bi-filetype-svg',
		//Archive
		'application/zip': 'bi-file-earmark-zip',
		'application/x-rar-compressed': 'bi-file-earmark-zip',
		//Audio
		'audio/mpeg': 'bi-file-earmark-music',
		'audio/wav': 'bi-file-earmark-music',
		'application/ogg': 'bi-file-earmark-music',
		'audio/ogg': 'bi-file-earmark-music',
		'audio/webm': 'bi-file-earmark-music',
		'audio/mp4': 'bi-file-earmark-music',
		//Video
		'video/quicktime': 'bi-file-earmark-play',
		'video/webm': 'bi-file-earmark-play',
		'video/mp4': 'bi-file-earmark-play',
		'video/x-matroska': 'bi-file-earmark-play'
	};

	let iconFromType = 'bi-file-earmark';

	if (type in map) {
		iconFromType = map[type];
	}

	return iconFromType;
}


