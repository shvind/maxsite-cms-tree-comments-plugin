<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function tree_comments_autoload()
{
   mso_hook_add('type-foreach-file', 'tree_comments_tff');
   mso_hook_add('head', 'tree_comments_head');
}

function tree_comments_head($args = array()){
	$url = getinfo('plugins_url') . 'tree_comments/';
	echo '<script type="text/javascript" src="'.$url.'js/jquery.tree-comments.js"></script>',NR;
	echo '<link rel="stylesheet" href="'.$url.'css/tree-comments.css" type="text/css" media="screen">',NR;
}


function tree_comments_uninstall($args = array())
{	
	mso_delete_option('tree_comments', 'plugins');
	return $args;
}

function tree_comments_mso_options() 
{
	mso_admin_plugin_options('tree_comments', 'plugins', 
		array(
			'tree_comments_enable' => array(
						'type' => 'text', 
						'name' => 'Активация плагина:', 
						'description' => 'Изменение поля не имеет значения, всего лишь задел на будущее.',
						'default' => 'Включено'
					),										
			),
		'Настройки древовидных комментариев',
		'Укажите необходимые опции.'
	);
}

function tree_comments_tff($tff = false) 
{   
   if ($tff == 'page-comments') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments.php';
   elseif ($tff == 'page-comments-do') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments-do.php';
   
   return false;
}
