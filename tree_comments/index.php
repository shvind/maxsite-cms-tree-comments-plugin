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
	#$this_plugin_url = 'tree_comments'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Древовидные комментарии', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'tree_comments_admin_page');
	}
	return $args;
}

function tree_comments_mso_options() 
{
#	$css = get_path_files(getinfo('plugins_dir') . 'tree_comments/css', getinfo('plugins_url') . 'tree_comments/css', false);
#	implode($css, '#');	

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
			'tc_comment_ip' => array(
						'type' => 'checkbox', 
						'name' => 'Отображать IP адрес комментатора', 
						'description' => 'Отображается админу для каждого комментария в строке "ник, дата и тд"',
						'default' => '1'
					),
			'tc_comments_vk' => array(
						'type' => 'checkbox', 
						'name' => 'Система комментирования Вконтакте', 
						'description' => 'Добавляется вкладка с комментариями от вконтактика',
						'default' => '0'
					),
			'tc_comments_vk_apiid' => array(
						'type' => 'text', 
						'name' => 'Ваш API Id:', 
						'description' => 'Получить API-ID можно <a href="http://vkontakte.ru/developers.php?oid=-1&p=Comments" target="_blank">здесь</a>.',
						'default' => ''
					),
			'tc_comments_vk_limit' => array(
						'type' => 'text', 
						'name' => 'Количество комментариев Вконтакте:', 
						'description' => 'Пагинация комментариев (вконтакт предлагает 5, 10, 15, 20)',
						'default' => '10'
					),	
			'tc_comments_vk_width' => array(
						'type' => 'text', 
						'name' => 'Ширина блока комментариев Вконтакте:', 
						'description' => 'Если не указывать будет 100%',
						'default' => ''
					),						
			'tc_comments_fb' => array(
						'type' => 'checkbox', 
						'name' => 'Система комментирования Facebook', 
						'description' => 'Добавляется вкладка с комментариями от фейсбука',
						'default' => '0'
					),
			'tc_comments_fb_limit' => array(
						'type' => 'text', 
						'name' => 'Количество комментариев Facebook:', 
						'description' => 'Пагинация комментариев (любое значение)',
						'default' => '10'
					),	
			'tc_comments_fb_width' => array(
						'type' => 'text', 
						'name' => 'Ширина блока комментариев Facebook:', 
						'description' => 'Нужно указывать значение в пикселях. 660px у стандартного щаблона.',
						'default' => '660'
					),	
			'tc_form_text4' => array(
						'type' => 'text', 
						'name' => 'Первая cтрока выводимая для анонимного комментирования.', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Не регистрировать/аноним'
					),
			'tc_form_text1' => array(
						'type' => 'text', 
						'name' => 'Вторая cтрока выводимая для анонимного комментирования.', 
						'description' => 'Можно задать любой текст. При включении модерации анонимов, соответствующий текст добавляется в конец автоматически.',
						'default' => 'Используйте нормальные имена. Можно использовать @twitter-name. '
					),
			'tc_form_text5' => array(
						'type' => 'text', 
						'name' => 'Первая строка выводимая для регистрации.', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Зарегистрирован или новая регистрация'
					),
			'tc_form_text2' => array(
						'type' => 'textarea', 
						'name' => 'Вторая строка выводимая для регистрации.', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Если вы уже зарегистрированы как комментатор или хотите зарегистрироваться, укажите пароль и свой действующий email. При регистрации на указанный адрес придет письмо с кодом активации и ссылкой на ваш персональный аккаунт, где вы сможете изменить свои данные, включая адрес сайта, ник, описание, контакты и т.д., а также подписку на новые комментарии.'
					),
			'tc_form_text3' => array(
						'type' => 'text', 
						'name' => 'Строка выводимая перед системами авторизации:', 
						'description' => 'Можно задать любой текст.',
						'default' => 'Авторизация: '
					),
			'tc_enable_form' => array(
						'type' => 'checkbox', 
						'name' => 'Изменить форму комментирования.', 
						'description' => 'Используется старая форма комментариев <0.61. Заменяет стандратный блок. Необходимо для опций плагина касающихся формы.',
						'default' => '0'
					),
			'tc_form_reg' => array(
						'type' => 'checkbox', 
						'name' => 'Переключить способ комментирования по умолчанию.', 
						'description' => 'Актуально, если включено анонимное комментирование.',
						'default' => '0'
					),	
#			'tc_compact_form' => array(
#						'type' => 'checkbox', 
#						'name' => 'Компактный режим.', 
#						'description' => 'Актуально, если включено анонимное комментирование. (альфа)',
#						'default' => '0'
#					),	
			'tc_form_nik' => array(
						'type' => 'checkbox', 
						'name' => 'Отображать поле "Ник" при регистрации.', 
						'description' => 'работает на версии >0.60',
						'default' => '0'
					),
			'tc_form_url' => array(
						'type' => 'checkbox', 
						'name' => 'Отображать поле "Сайт" при регистрации.', 
						'description' => 'работает на версии >0.60',
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
	
	if (!isset($options['tc_enable_tree'])) $options['tc_enable_tree'] = '1';
#	if (!isset($options['tc_css'])) $options['tc_css'] = '';
	if (!isset($options['tc_enable_form'])) $options['tc_enable_form'] = '0';

	
	if ($options['tc_enable_tree']) 
		{
			if ($tff == 'page-comments') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments.php';
			if ($tff == 'page-comments-do') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments-do.php';
		}
	
	if ($options['tc_enable_form']) 
		{
			if ($tff == 'page-comment-form') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comment-form.php';
		}
	return false;
	
}