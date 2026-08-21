/**
 * JavaScript used by mod/photos
 */
$(document).ready(function() {

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		UploadInit();
	}

	$(".comment-edit-form  textarea").editor_autocomplete(baseurl+"/acl?f=&n=1");
	$('textarea').editor_autocomplete(baseurl+"/acl");
	$('textarea').bbco_autocomplete('bbcode');
});

// initialize
function UploadInit() {

	let nickname = $('#invisible-photos-file-upload').data('nickname');
	let fileselect = $("#photos-upload-choose");
	let filedrag = $("#photos-upload-form");
	let submit = $("#dbtn-submit");
	let idx = 0;
	let reload = false;


	$('#invisible-photos-file-upload').fileupload({
		url: 'photos/' + nickname,
		dataType: 'json',
		dropZone: filedrag,
		maxChunkSize: 4 * 1024 * 1024,

		add: function(e,data) {

			idx++;
			data.files[0].idx = idx;
			prepareHtml(data.files[0]);

			let allow_cid = ($('#photos-upload-form').data('allow_cid') || []);
			let allow_gid = ($('#photos-upload-form').data('allow_gid') || []);
			let deny_cid  = ($('#photos-upload-form').data('deny_cid') || []);
			let deny_gid  = ($('#photos-upload-form').data('deny_gid') || []);

			$('.acl-field').remove();

			$(allow_gid).each(function(i,v) {
				$('#photos-upload-form').append("<input class='acl-field' type='hidden' name='group_allow[]' value='"+v+"'>");
			});
			$(allow_cid).each(function(i,v) {
				$('#photos-upload-form').append("<input class='acl-field' type='hidden' name='contact_allow[]' value='"+v+"'>");
			});
			$(deny_gid).each(function(i,v) {
				$('#photos-upload-form').append("<input class='acl-field' type='hidden' name='group_deny[]' value='"+v+"'>");
			});
			$(deny_cid).each(function(i,v) {
				$('#photos-upload-form').append("<input class='acl-field' type='hidden' name='contact_deny[]' value='"+v+"'>");
			});

			data.formData = $('#photos-upload-form').serializeArray();

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

	$('#dbtn-submit').click(function(event) { event.preventDefault(); $('#invisible-photos-file-upload').trigger('click'); return false;});

}


function prepareHtml(f) {
	var num = f.idx - 1;
	var i = f.idx;
	$('#upload-index #new-upload-progress-bar-' + num.toString()).after(
		'<tr id="new-upload-' + i + '" class="new-upload">' +
		'<td></td>' +
		'<td><i class="fa fa-fw ' + getIconFromType(f.type) + '" title="' + f.type + '"></i></td>' +
		'<td>' + f.name + '</td>' +
		'<td id="upload-progress-' + i + '"></td><td></td><td></td>' +
		'<td class="d-none d-md-table-cell">' + formatSizeUnits(f.size) + '</td><td class="d-none d-md-table-cell"></td>' +
		'</tr>' +
		'<tr id="new-upload-progress-bar-' + i + '" class="new-upload">' +
		'<td colspan="8" class="upload-progress-bar">' +
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
