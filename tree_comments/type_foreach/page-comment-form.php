<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	$options = mso_get_option('tree_comments', 'plugins', array() ); // получаем опции

	if (!isset($options['tc_comments_vk'])) $options['tc_comments_vk'] = '0';
	if (!isset($options['tc_comments_fb'])) $options['tc_comments_fb'] = '0';
	if (!isset($options['tc_block1'])) $options['tc_block1'] = 'comments-tree.php';
	if (!isset($options['tc_block2'])) $options['tc_block2'] = 'comments-vk.php';
	if (!isset($options['tc_block3'])) $options['tc_block3'] = 'comments-fb.php';
	if (!isset($options['tc_tabs'])) $options['tc_tabs'] = '1';
	
	$url = getinfo('site_url') . 'page/' . $page['page_slug'];
	#echo '</div>';
	echo '<div class="comments tabs_widget tabs_widget_99">'; 
	
	if ($options['tc_tabs']) {
		echo '<div class="tabs"><ul class="tabs-nav">';
		echo '<li class="elem tabs-current"><h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3></li>';
		#if ($options['tc_comments_vk']) 
		echo '<li class="elem"><h3 class="comments">' . t('Вконтакте') . '</h3></li>';
		#if ($options['tc_comments_fb']) 
		echo '<li class="elem"><h3 class="comments">' . t('Facebook') . ': <fb:comments-count href=' . $url . '></fb:comments-count></h3></li>';
		echo '</ul><div class="clearfix"></div>';
	}
	
	if ($options['tc_block1']) {
		if (file_exists(getinfo('plugins_dir') . 'tree_comments/blocks/' . $options['tc_block1'])) 
			require(getinfo('plugins_dir') . 'tree_comments/blocks/' . $options['tc_block1']); 
		else echo "<br>File " . $options['tc_block1'] . " not found";
	}
	
	if ($options['tc_block2']) {
		if (file_exists(getinfo('plugins_dir') . 'tree_comments/blocks/' . $options['tc_block2'])) 
			require(getinfo('plugins_dir') . 'tree_comments/blocks/' . $options['tc_block2']); 
		else echo "<br>File " . $options['tc_block2'] . " not found";
	}
	
	if ($options['tc_block3']) {
		if (file_exists(getinfo('plugins_dir') . 'tree_comments/blocks/' . $options['tc_block3'])) 
			require(getinfo('plugins_dir') . 'tree_comments/blocks/' . $options['tc_block3']); 
		else echo "<br>File " . $options['tc_block3'] . " not found";
	}
	
	echo '</div><!-- div tabs end-->';
	echo '</div><!-- div comments end-->';
?>