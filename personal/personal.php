<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * module Personal for Cotonti Siena
 *
 * @package Personal
 * @author Kalnov Alexey
 * @copyright Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

// Environment setup
$env['location'] = 'personal';

// Self requirements
require_once cot_incfile($env['ext'], 'module');

// Default ACL
list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('personal', 'a');
cot_block($usr['auth_read']);

if(empty($m)) $m = 'main';   // Констроллер по-умолчанию

// Only if the file exists...
if (file_exists(cot_incfile($env['ext'], 'module', $m))) {
    require_once cot_incfile($env['ext'], 'module', $m);
    /* Create the controller */
    $_class = ucfirst($m).'Controller';
    $controller = new $_class();
    
    // TODO кеширование
    /* Perform the Request task */
    $currentAction = $a.'Action';
    if (!$a && method_exists($controller, 'indexAction')){
        $outContent = $controller->indexAction();
    }elseif (method_exists($controller, $currentAction)){
        $outContent = $controller->$currentAction();
    }else{
        // Error page
		cot_die_message(404);
		exit;
    }
    
    //ob_clean();
    require_once $cfg['system_dir'] . '/header.php';
    if (isset($outContent)) echo $outContent;
    require_once $cfg['system_dir'] . '/footer.php';
}else{
    // Error page
    cot_die_message(404);
    exit;
}