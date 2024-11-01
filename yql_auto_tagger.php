<?php
/***************************************************************************

Plugin Name: YQL Auto Tagger
Plugin URI: http://www.rogerstringer.com/wp-yql-auto-tagger
Description: Suggests tags for your posts based on semantic analysis of your post content with the Open Calais API.
Version: 1.3.1
Author: Roger Stringer
Author URI: http://www.rogerstringer.com

***************************************************************************/

//Include the YQL Tags class by Roger Stringer
//http://www.rogerstringer.com/yql-tags
require('yqltags.php');

//Initialization to add the box to the post page
add_action('admin_menu', 'yql_init');
function yql_init() {
	add_meta_box('yqltags', 'YQL Auto Tagger', 'yql_box', 'post', 'normal', 'high');
}
function yql_box() {
	?>
	<style type="text/css">
		.yql_tag {
			float: left;
			padding: 3px 6px 7px 3px;
			background: #e1f3fd;
			font-size: 8pt;
			color: #000;
			margin: 0 5px 5px 0;
		}
		.yql_tag img {
			position: relative;
			top: 3px;
		}
	</style>
	<?php require('js.inc'); ?>
	<?php
	//Existing post tags
	global $post;
	$existing_tags = wp_get_post_tags($post->ID);
	$tags = array();
	if (count($existing_tags) > 0) {
	    foreach ($existing_tags as $tag) {
	        if ($tag->taxonomy == 'post_tag')
	            $tags[] = $tag->name;
	    }
	}
	?>
	<input type="hidden" name="yql_taglist" id="yql_taglist" value="<?php echo implode(', ', $tags); ?>" />
	<label for="yql_manual">Add your own tags:</label>
	<br />
	<input type="text" name="yql_manual" id="yql_manual" value="" /> <input type="button" class="button" onclick="yql_add_manual()" value="Add Tags" />
	<br /><br />
	<b>Post Tags:</b>
	<br /><br />
	<div id="yql_tag_box" style="min-height: 40px">
	</div>
	<div style="clear: left"></div>
	<b>Suggested Tags:</b>
	<br /><br />
	<div id="yql_suggestions" style="min-height: 40px">
	</div>
	<div style="clear: left"></div>
	<input type="button" class="button" onclick="yql_gettags()" value="Get Tag Suggestions" /><br /><br />
	<script type="text/javascript"> yql_redisplay_tags(); </script>
<?php	
}
add_action('save_post', 'yql_savetags', 10, 2);
function yql_savetags($post_id, $post) {
	if ($post->post_type == 'revision') return;
	if (!isset($_POST['yql_taglist'])) return;
	$taglist = $_POST['yql_taglist'];
	$tags = split(', ', $taglist);
	if (strlen(trim($taglist)) > 0 && count($tags) > 0) {
		wp_set_post_tags($post_id, $tags);
	} else {
		wp_set_post_tags($post_id, array());
	}
}

//Register an AJAX hook for the function to get the tags
add_action('wp_ajax_yql_gettags', 'yql_gettags');

//This is the function that runs when the author requests tags for 
//their post. It connects to the Open Calais API, sends the post text,
//parses the entities returned and puts them into a tag list to return
function yql_gettags() {
	$content = stripslashes($_POST['text']);
	
	$yt = new YQLTag();
	$tags = $yt->getTags($content);	
	if (count($tags) == 0)die("No Tags");
	die(implode($tags, ', '));	
}