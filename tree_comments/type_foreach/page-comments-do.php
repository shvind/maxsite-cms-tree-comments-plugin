<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	echo '<div class="comments">';
	echo '<h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3>';

	$tree_comments_first_level = 'tree-comments-level-0';
	global $tree_comments_child_list;
	$tree_comments_child_list = 'tree-comments-list-childs';
	$comments_parent_id = 0;
	global $comms;
	$comms	= $comments;

	$parents = array();
	foreach ( $comments as $comment ) {
		// определим корневые узлы
		if ( $comment['comments_parent_id'] == 0 ) $parents[] = $comment;
	}
	
	$out = '<ul class="' . $tree_comments_first_level . '">';
	$out .=  build_tree( $parents, 0  ); 
	$out .= '</ul>';	

	echo $out;
	
	function  build_tree($parents, $parent_id){
		global $comms;
		global $tree_comments_child_list;
		$tree = '';
		foreach ( $parents as $parent ) {
			extract($parent);
			if ($users_id) $class = ' class="users"';
			elseif ($comusers_id) $class = ' class="comusers"';
			else $class = ' class="anonim"';
			$comments_date = mso_date_convert('H:i d/m/Y', $comments_date);
		
			$data = array( 	'users_email' => $users_email,
							'comusers_email' => $comusers_email,
							'users_avatar_url' => $users_avatar_url,
							'comusers_avatar_url' => $comusers_avatar_url 
                       	);
						
		$avatar_url = '';
		if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
		elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
		
		if (!$avatar_url) 
		{ // аватарки нет, попробуем получить из gravatara
			
			if ($users_email) $grav_email = $users_email;
			elseif ($comusers_email) $grav_email = $comusers_email;
			else $grav_email = '';
			
			if ($grav_email)
			{
				if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
					$d = '&amp;d=' . urlencode($gravatar_type);
				else 
					$d = '';
				
				$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=" 
						. md5($grav_email)
						. "&amp;size=80"
						. $d;
			}
		}
		
		if ($avatar_url) 
			$avatar_url = '<span style="display: none"><![CDATA[<noindex>]]></span><img src="' . $avatar_url . '" width="80" height="80" alt="" title="" style="float: left; margin: 5px 15px 10px 0;" class="gravatar"><span style="display: none"><![CDATA[</noindex>]]></span>';
		
			$tree .= '<li style="clear: both;"' . $class . '><div class="tree-comment">';
			$tree .= '<div class="tree-comment-avatar">' . $avatar_url . '</div>';
			$tree .= '<div class="tree-comment-data">';
				//$tree .= '<span class="tree-comment-meta"><a href="#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
				
				// author
				$tree .= '<span class="tree-comment-author">' . $comments_url . '</span>';
				
				$tree .= '&nbsp;<span class="tree-comment-meta">(' . $comments_date . ')</span>';
				
				

				if (is_login()) 
				{
					$edit_link = getinfo('siteurl') . 'admin/comments/edit/';
					$tree .= ' | ';
					$tree .= '<span class="tree-comment-edit"><a href="' . $edit_link . $comments_id . '">edit</a></span>';
				}	
				if (!$comments_approved) {
					$tree .= ' | ';
					$tree .= '<span class="tree-comment-moderate">Ожидает модерации</span>';			
				}	
				$tree .= '<span class="tree-comment-content">' . mso_comments_content($comments_content) . '</span>';	
			$tree .= '</div>';

			$tree .= '<div class="break"></div>';
			$tree .= '<div class="comment-reply" id="comment-reply-' . $comments_id . '">';
			$tree .= '<a class="comment-form-button" id="comment-form-button-' . $comments_id . '" type="button" name="comment-form-button-' . $comments_id . '" onclick="show_comment_form(' . $comments_id . ', ' . $page_id . ')">Ответить</a>';
			$tree .= '<div class="comment-form-comment" id="comment-form-comment-' . $comments_id . '"></div>';
			$tree .= '</div>';
			
			$tree .= '</div>';
			/**/
				$childs =array();
				foreach ( $comms as $comm ) {
					if ( $comm['comments_parent_id'] == $parent['comments_id']) { $childs[] = $comm; }
				}
				if ( isset( $childs ) && ( count($childs) > 0 ) )
				{			
					$tree .= '<ul class="' . $tree_comments_child_list . '">';
					$tree .= build_tree ( $childs, $parent['comments_id'] );
					$tree .= '</ul>';
				}
			/**/
			$tree .= '</li>';	
		}
		return $tree;			

    }	
?>