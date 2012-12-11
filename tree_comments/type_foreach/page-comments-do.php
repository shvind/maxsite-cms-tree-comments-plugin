<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	$options = mso_get_option('tree_comments', 'plugins', array() ); // получаем опции

	if (!isset($options['tc_comments_vk'])) $options['tc_comments_vk'] = '0';
	if (!isset($options['tc_comments_fb'])) $options['tc_comments_fb'] = '0';
	
	echo '<div class="comments">';
	
	if ((!$options['tc_comments_vk']) && (!$options['tc_comments_fb']))  echo '<h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3>';
	
	if (($options['tc_comments_vk']) || ($options['tc_comments_fb'])) {
		echo '<div class="tabs"><ul class="tabs-nav">';
		echo '<li class="elem tabs-current"><h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3></li>';
		if ($options['tc_comments_vk']) echo '<li class="elem"><h3 class="comments">' . t('Вконтакте') . '</h3></li>';
		if ($options['tc_comments_fb']) echo '<li class="elem"><h3 class="comments">' . t('Facebook') . '</h3></li>';
		echo '</ul><div class="clearfix"></div>';
	}

	
	echo '<div class="tabs-box tabs-visible">';
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
	$options = mso_get_option('tree_comments', 'plugins', array() ); // получаем опции
	

	
		global $comms;
		global $tree_comments_child_list;
		$tree = '';
		foreach ( $parents as $parent ) {
			extract($parent);
			if ($users_id) $class = ' class="users"';
			elseif ($comusers_id) $class = ' class="comusers"';
			else $class = ' class="anonim"';
			
			if (!isset($options['tc_date_format'])) $options['tc_date_format'] = 'j F Y в H:i:s';
			$comments_date = mso_page_date($comments_date, 
									array(	'format' => $options['tc_date_format'], // получаем формат даты
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	
		
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
						. "&amp;size="
						. mso_get_option('gravatar_size', 'templates', '')
						. $d;
			}
		}
		
		if ($avatar_url) 

			$avatar_url = '<img src="' . $avatar_url . '" width="'. mso_get_option('gravatar_size', 'templates', '') .'" height="'. mso_get_option('gravatar_size', 'templates', '') .'" alt="" title="" style="float: left; margin: 5px 15px 10px 0;" class="gravatar">';
		
			$tree .= '<li style="clear: both;"' . $class . '><div class="tree-comment">';
			$tree .= '<div class="comment-info tree-comment">';
				$tree .= '&nbsp;<span class="tree-comment-author">' . $comments_url . '</span>';
				// опциональная ссылка на комментарий
				if (!isset($options['tc_comment_link'])) $options['tc_comment_link'] = 'date';
				if ($options['tc_comment_link'] == 'date') $tree .= ' |&nbsp;<span class="tree-comment-date"><a href="' . $page_slug . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
				else $tree .= '&nbsp;<span class="tree-comment-date">' . $comments_date . '</span>';
				if ($options['tc_comment_link'] == 'text') $tree .= '&nbsp;<span class="tree-comment-meta"><a href="' . $page_slug . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">(ссылка)</a></span>';
								
				if (is_login()) 
				{
					$edit_link = getinfo('siteurl') . 'admin/comments/edit/';
					$tree .= ' | ';
					$tree .= '<span class="tree-comment-edit"><a href="' . $edit_link . $comments_id . '">edit</a></span>';
					if (!isset($options['tc_comment_ip'])) $options['tc_comment_ip'] = '1';
					if ($options['tc_comment_ip']) $tree .= '<span class="tree-comment-ip">'. $comments_author_ip .'</span>';
				}	
				if (!$comments_approved) {
					$tree .= ' | ';
					$tree .= '<span class="tree-comment-moderate">Ожидает модерации</span>';			
				}	
	
			$tree .= '</div>';

			$tree .= '<div class="comments_content tree-comment-data">';
				$tree .= $avatar_url;
				$tree .= mso_comments_content($comments_content);	
			$tree .= '</div>';
			
			$tree .= '<div class="break"></div>';
			$tree .= '<div class="comment-reply" id="comment-reply-' . $comments_id . '">';
			$tree .= '<a class="comment-form-button" id="comment-form-button-' . $comments_id . '" type="button" name="comment-form-button-' . $comments_id . '" onclick="show_comment_form(' . $comments_id . ', ' . $page_id . ')">Ответить</a>';
			$tree .= '<div class="comment-form-comment" id="comment-form-comment-' . $comments_id . '"></div>';
			$tree .= '</div>';
			
			$tree .= '</div><!--div class="comments-end"-->';
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
	echo '</div>';
	

	if ($options['tc_comments_vk']) {
		
		if (!isset($options['tc_comments_vk_apiid'])) $options['tc_comments_vk_appiid'] = '';
		if (!isset($options['tc_comments_vk_limit'])) $options['tc_comments_vk_limit'] = '10';
		if (!isset($options['tc_comments_vk_width'])) $options['tc_comments_vk_width'] = '';
	
		echo '<div class="tabs-box">';
		echo '<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>';

		echo '<script type="text/javascript">
				VK.init({apiId:'. $options['tc_comments_vk_apiid'] .', onlyWidgets: true});
			</script>';
		echo '<div id="vk_comments"></div>
			<script type="text/javascript">
				VK.Widgets.Comments("vk_comments", {limit: '. $options['tc_comments_vk_limit'] .', width: "'. $options['tc_comments_vk_width'] .'", attach: "*"});
			</script>';
		echo '</div>';
	}
	
	
	if ($options['tc_comments_fb']) {
	
		#if (!isset($options['tc_comments_fb_apiid'])) $options['tc_comments_vk_appiid'] = '';
		if (!isset($options['tc_comments_fb_limit'])) $options['tc_comments_fb_limit'] = '10';
		if (!isset($options['tc_comments_fb_width'])) $options['tc_comments_fb_width'] = '660';
		
		echo '<div class="tabs-box">';
		echo '<div id="fb-root"></div>
			<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", "facebook-jssdk"));</script>';

		echo '<div class="fb-comments" data-href="'. $page['page_slug'] .'" data-num-posts="'. $options['tc_comments_fb_limit'] .'" data-width="'. $options['tc_comments_fb_width'] .'"></div>';
		echo '</div>';
		
	}
	
	echo '</div>';
?>