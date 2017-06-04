<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.delete
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL.');

/**
 * module Personal for Cotonti Siena
 *
 * @package Personal
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */

// Удаление вакансий
$udItems = personal_model_Vacancy::findByCondition(array(array('user_id', $id)));
if(!empty($udItems)){
    foreach($udItems as $udItemRow){
        $udItemRow->delete();
    }
}

// Удаление резюме
$udItems = personal_model_Resume::findByCondition(array(array('user_id', $id)));
if(!empty($udItems)){
    foreach($udItems as $udItemRow){
        $udItemRow->delete();
    }
}

// Удаление профилей работодателей
$udItems = personal_model_EmplProfile::findByCondition(array(array('user_id', $id)));
if(!empty($udItems)){
    foreach($udItems as $udItemRow){
        $udItemRow->delete();
    }
}