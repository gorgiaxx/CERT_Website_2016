jQuery(document).ready(function($) {

	$('#upload_face').click(function() {

		wp.media.editor.add('custom_upload');
		
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var send_insert_bkp = wp.media.editor.insert;
		
		var button = $(this);
		upload_ID =  jQuery("input[name='upload_face']");
		
		wp.media.editor.send.attachment = function(props, attachment) {

			upload_ID.val(attachment['sizes'][props['size']]['url']);
			button.attr("src",attachment['sizes'][props['size']]['url'])
			upload_ID.trigger("change");
			wp.media.editor.send.attachment = send_attachment_bkp
		}

		wp.media.editor.insert = function(html) {
			wp.media.editor.insert = send_insert_bkp;
		}

		wp.media.editor.open('custom_upload');

		wp.media.editor.get('custom_upload').on('escape', function(e){
			wp.media.editor.send.attachment = send_attachment_bkp;
			wp.media.editor.insert = send_insert_bkp;
		});
		
		return false;
	});

	$('#upload_background').click(function() {

		wp.media.editor.add('custom_upload');
		
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var send_insert_bkp = wp.media.editor.insert;
		
		var button = $(this);
		upload_ID =  jQuery("input[name='background']");
		
		wp.media.editor.send.attachment = function(props, attachment) {

			upload_ID.val(attachment['sizes'][props['size']]['url']);
			button.attr("src",attachment['sizes'][props['size']]['url'])
			upload_ID.trigger("change");
			wp.media.editor.send.attachment = send_attachment_bkp
		}

		wp.media.editor.insert = function(html) {
			wp.media.editor.insert = send_insert_bkp;
		}

		wp.media.editor.open('custom_upload');

		wp.media.editor.get('custom_upload').on('escape', function(e){
			wp.media.editor.send.attachment = send_attachment_bkp;
			wp.media.editor.insert = send_insert_bkp;
		});
		
		return false;
	});
	$('#upload_thumb_img').click(function() {

		wp.media.editor.add('custom_upload');
		
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var send_insert_bkp = wp.media.editor.insert;
		
		var button = $(this);
		upload_ID =  jQuery("input[name='thumb_img']");
		
		wp.media.editor.send.attachment = function(props, attachment) {

			upload_ID.val(attachment['sizes'][props['size']]['url']);
			button.attr("src",attachment['sizes'][props['size']]['url'])
			upload_ID.trigger("change");
			wp.media.editor.send.attachment = send_attachment_bkp
		}

		wp.media.editor.insert = function(html) {
			wp.media.editor.insert = send_insert_bkp;
		}

		wp.media.editor.open('custom_upload');

		wp.media.editor.get('custom_upload').on('escape', function(e){
			wp.media.editor.send.attachment = send_attachment_bkp;
			wp.media.editor.insert = send_insert_bkp;
		});
		
		return false;
	});
});
