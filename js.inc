<script type="text/javascript">
//<![CDATA[
	var suggestedTags = new Array();
	var currentTags = new Array();
	function yql_gettags() {
		document.getElementById('yql_suggestions').innerHTML = 'Getting suggestions...';
		jQuery.post('<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php', {text: yql_getcontent(), action: 'yql_gettags', cookie: document.cookie}, yql_showtags);
	}
	function yql_getcontent() {
		if (typeof tinyMCE != 'undefined' && tinyMCE.activeEditor != null && tinyMCE.activeEditor.isHidden() == false) {
			return tinyMCE.activeEditor.getBody().innerHTML;
		}
		return document.getElementById('content').value;
	}
	function yql_showtags(tags) {
		suggestedTags = tags.split(', ');
		if (suggestedTags[0] == '') suggestedTags = new Array();
		suggestions = document.getElementById('yql_suggestions');
		suggestions.innerHTML = '';
		for (i = 0; i < suggestedTags.length; i++) {
			var el = document.createElement('div');
			el.className = 'yql_tag';
			el.id = 'suggestion_' + i;
			var html = '<img src="<?php get_bloginfo('wpurl'); ?>/wp-content/plugins/yql-auto-tagger/images/add.png" onclick="yql_add_suggestion(\'suggestion_' + i + '\')" /> ' + suggestedTags[i];
			el.innerHTML = html;
			suggestions.appendChild(el);
		}
	}
	function yql_add_tag(tag) {
		found = false;
		for (j = 0; j < currentTags.length; j++) {
			if (currentTags[j] == tag) found = true;
		}
		if (found == false && tag != '') {
			if (document.getElementById('yql_taglist').value != '') {
				document.getElementById('yql_taglist').value = document.getElementById('yql_taglist').value + ', ' + tag;
			} else {
				document.getElementById('yql_taglist').value = tag;
			}
		}
		yql_redisplay_tags();
	}
	function yql_add_suggestion(full_id) {
		id = full_id.replace('suggestion_', '');
		tag = suggestedTags[id];
		yql_add_tag(tag);
		document.getElementById(full_id).style.display = 'none';
	}
	function yql_add_manual() {
		newtags = document.getElementById('yql_manual').value.split(',');
		for (k = 0; k < newtags.length; k++) {
			yql_add_tag(jQuery.trim(newtags[k]));
		}
		document.getElementById('yql_manual').value = '';
	}
	function yql_delete(full_id) {
		id = full_id.replace('ctag_', '');
		currentTags = document.getElementById('yql_taglist').value.split(', ');
		currentTags.splice(id, 1);
		var liststr = '';
		for (i = 0; i < currentTags.length; i++) {
			liststr += currentTags[i];
			if (i != (currentTags.length - 1)) {
				liststr += ', ';
			}
		}
		document.getElementById('yql_taglist').value = liststr;
		yql_redisplay_tags();
	}
	function yql_redisplay_tags() {
		yql_update_current_tags();
		existing = document.getElementById('yql_tag_box');
		existing.innerHTML = '';
		for (i = 0; i < currentTags.length; i++) {
			var el = document.createElement('div');
			el.className = 'yql_tag';
			el.id = 'ctag_' + i;
			var html = '<img src="<?php get_bloginfo('wpurl'); ?>/wp-content/plugins/yql-auto-tagger/images/delete.png" onclick="yql_delete(\'ctag_' + i + '\')" /> ' + currentTags[i];
			el.innerHTML = html;
			existing.appendChild(el);
		}
	}
	function yql_update_current_tags() {
		temp = document.getElementById('yql_taglist').value.split(', ');
		if (temp.length > 1) {
			currentTags = temp;
		} else {
			if (temp[0] != '') currentTags = temp;
		}
	}
	jQuery(document).ready(function() { document.getElementById('tagsdiv-post_tag').style.display = 'none'; });
//]]>
</script>