<?php
//Menu administrador Configurações
$menu = [
    'name' => 'Ínicio',
    'title' => 'Ínicio',
    'slug' => 'inicio',
    'Controller' => 'Home',
    'icon' => 'dashicons-layout',
    'ajax' => true,
    'post' => true,
    'login' => true
];

//Submenu administrador Configurações
$subMenu = [
	[
        'name' => 'Teste',
        'title' => 'teste',
        'slug' => 'teste',
        'Controller' => 'Home',
        'hidden' => false,
        'ajax' => true,
        'login' => true,
        'post' => true,
    ],
    [
        'name' => 'Teste2',
        'title' => 'teste2',
        'slug' => 'teste2',
        'Controller' => 'Home',
        'hidden' => false,
        'ajax' => true,
        'login' => true,
        'post' => true,
    ],
];

//Actions do plugin, são executadas mesmo que não esteja sendo acessado por um usuário logado.
$actions = [
    [
        'name' => 'Ínicio',
        'slug' => 'bla',
        'Controller' => 'Home',
        'shortCode' => 'blabla'
    ],
];

//Argumentos para iniciar após carregar o plugin
$init = function(){};

//Argumentos para iniciar após a página administrador carregar
$admin_init = function(){};

//Adicionar ao cabeçalho do wordpress
$wp_head = function(){};

//Adicionar ao cabeçalho administrador do wordpress
$admin_head = function(){};

//Adicionar ao rodapé do wordpress
$wp_footer = function(){};