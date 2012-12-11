<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function tree_comments_autoload()
{
	mso_create_allow('tree_comments_edit', t('Админ-доступ к древовидным комментариям', __FILE__)); // права доступа
	mso_hook_add( 'admin_init', 'tree_comments_admin_init'); // хук на админку
	mso_hook_add( 'type-foreach-file', 'tree_comments'); // хук для своих foreach_file
	mso_hook_add( 'head', 'tree_comments_head'); // хук на шапку
	}

function tree_comments_head($args = array()){
	$url = getinfo('plugins_url') . 'tree_comments/';
	echo '<script type="text/javascript" src="'.$url.'js/jquery.tree-comments.js"></script>',NR;
	echo '<link rel="stylesheet" href="'.$url.'css/tree-comments.css" type="text/css" media="screen">',NR;
}

function tree_comments_uninstall($args = array())
{	
	mso_delete_option('tree_comments', 'plugins'); // удалим созданные опции плагина
	mso_remove_allow('tree_comments_edit'); // удалим созданные разрешения
	return $args;
}

function tree_comments_admin_init($args = array()) 
{
	if ( mso_check_allow('tree_comments_edit') ) {
	$this_plugin_url = 'plugin_options/tree_comments'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Древовидные комментарии', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'tree_comments_admin_page');
	}
	return $args;
}

function tree_comments_mso_options() 
{
	#$css = get_path_files(getinfo('plugins_dir') . 'tree_comments/css', getinfo('plugins_url') . 'tree_comments/css', false);
	#implode($css, '#');	

	mso_admin_plugin_options('tree_comments', 'plugins', 
		array(
			'tc_enable_tree' => array(
						'type' => 'checkbox', 
						'name' => 'Включить древовидные комментарии:', 
						'description' => 'Заменяет стандартный блок. Необходимо для опций касающихся списка комментариев.',
						'default' => '1'
					),
#			'tc_css' => array(
#						'type' => 'text', 
#						'name' => 'Файл стилей:', 
#						'description' => 'Можно загружать свои стили комментариев в tree_comments/css',
#						'values' =>  'tree-comments.css',
#						'default' => 'tree-comments.css'
#					),	
			'tc_date_format' => array(
						'type' => 'text', 
						'name' => 'Формат даты комментария:', 
						'description' => 'Можно задать любой произвольный формат. Примеры: (H:i d/m/Y) и (j F Y в H:i:s)',
						'default' => 'j F Y в H:i:s'
					),	
			'tc_comment_link' => array(
						'type' => 'radio', 
						'name' => 'Cсылка на комментарий:', 
						'description' => 'Вывод ссылки на комментарий post#comment-nn.',
						'values' => 'none||не отображать #text||добавить отдельным текстом # date||дата комментария станет ссылкой', 
						'default' => 'date'
					),
#			'tc_gravatar_noindex' => array(
#						'type' => 'checkbox', 
#						'name' => 'Gravatar noindex:', 
#						'description' => '',
#						'default' => '1'
#					),
			'tc_enable_form' => array(
						'type' => 'checkbox', 
						'name' => 'Изменить форму комментирования.', 
						'description' => 'Заменяет стандратный блок. Необходимо для опций плагина касающихся формы.',
						'default' => '0'
					),
			'tc_form_text1' => array(
						'type' => 'text', 
						'name' => 'Текст выводимый под формой анонимного комментирования:', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Используйте нормальные имена. Можно использовать @twitter-name. Ваш комментарий будет опубликован после проверки. '
					),
			'tc_form_text2' => array(
						'type' => 'textarea', 
						'name' => 'Текст выводимый под формой авторизации/регистрации:', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Если вы уже зарегистрированы как комментатор или хотите зарегистрироваться, укажите пароль и свой действующий email. При регистрации на указанный адрес придет письмо с кодом активации и ссылкой на ваш персональный аккаунт, где вы сможете изменить свои данные, включая адрес сайта, ник, описание, контакты и т.д., а также подписку на новые комментарии.'
					),
			'tc_form_text3' => array(
						'type' => 'text', 
						'name' => 'Строка выводимая перед системами авторизации:', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Авторизация:'
					),
			'tc_compact_form' => array(
						'type' => 'checkbox', 
						'name' => 'Компактный режим.', 
						'description' => '(альфа, доделать планирую к версии 03)',
						'default' => '0'
					),	
			'tc_form_nick' => array(
						'type' => 'checkbox', 
						'name' => 'Отображать поле "Ник" при регистрации.', 
						'description' => '(заготовка, доделать планирую к версии 03)',
						'default' => '0'
					),
			'tc_form_site' => array(
						'type' => 'checkbox', 
						'name' => 'Отображать поле "Сайт" при регистрации.', 
						'description' => '(заготовка, доделать планирую к версии 03)',
						'default' => '0'
					),
			),
		'Настройки древовидных комментариев',
		'Выберите необходимые опции.'
	);
}

function tree_comments ($tff = false) 
{   
	$options = mso_get_option('tree_comments', 'plugins', array() ); // получаем опции
	
	if ( !isset($options['tc_enable_tree']) ) $options['tc_enable_tree'] = '1';
#	if ( !isset($options['tc_css']) ) $options['tc_css'] = '';
	if ( !isset($options['tc_date_format']) ) $options['tc_date_format'] = 'j F Y в H:i:s';
	if ( !isset($options['tc_comment_link']) ) $options['tc_comment_link'] = 'date';
#	if ( !isset($options['tc_gravatar_noindex']) ) $options['tc_gravatar_noindex'] = '';
	if ( !isset($options['tc_enable_form']) ) $options['tc_enable_form'] = '0';
#	if ( !isset($options['tc_form_text1']) ) $options['tc_form_text1'] = '';
#	if ( !isset($options['tc_form_text2']) ) $options['tc_form_text2'] = '';
#	if ( !isset($options['tc_form_text3']) ) $options['tc_form_text3'] = '';
	if ( !isset($options['tc_compact_form']) ) $options['tc_compact_form'] = '0';
	if ( !isset($options['tc_form_nick']) ) $options['tc_form_nick'] = '0';
	if ( !isset($options['tc_form_site']) ) $options['tc_form_site'] = '0';
	
	if ($options['tc_enable_tree']) 
		{
			if ($tff == 'page-comments') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments.php';
			elseif ($tff == 'page-comments-do') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments-do.php';
		}
	
	if ($options['tc_enable_form']) 
		{
			if ($tff == 'page-comment-form') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comment-form.php';
		}
	return false;
	
}