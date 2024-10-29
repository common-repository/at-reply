<?php
/*
Plugin Name: @reply
Plugin URI: http://wordpress.org/extend/plugins/at-reply/
Description: Automagically link Twitterish "@name:" replies in comments.
Version: 1.1
Author: Jeff Waugh
Author URI: http://bethesignal.org/
*/

/*
Copyright (C) Jeff Waugh <http://bethesignal.org/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function at_reply_filter($content) {
	global $post;

	if (preg_match('/^@([^:]+): /', $content, $matches)) {
		// Reverse the array to begin the author search at the 'bottom'
		$comments = array_reverse(get_approved_comments($post->ID));
		// Delete the first element, as it is the current comment
		$comments = array_slice($comments, 1);

		foreach ($comments as $comment) {
			// First matching post is the cited author's last comment
			if (strcasecmp($matches[1], $comment->comment_author) == 0) {
				$cid = $comment->comment_ID;
				$lnk = get_permalink($post->ID);
				// Link the author name to the comment anchor
				return preg_replace(
					'/^@([^:]+): /',
					'@<a href="'.$lnk.'#comment-'.$cid.'">\1</a>: ',
					$content);
			}
		}
	}

	return $content;
}

add_filter('comment_text', 'at_reply_filter');
?>
