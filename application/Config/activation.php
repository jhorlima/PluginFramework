<?php
//Executar ao ativar o wordpress
$activation = function(){
    if (version_compare(PHP_VERSION, '5.5', '<') || version_compare(get_bloginfo('version'), '3.3', '<'))
        deactivate_plugins( basename( __FILE__ ) );
    error_log("Ativou",0);
};

//Executar ao desativar o wordpress
$deactivation = function(){
    error_log("Desativou",0);
};