<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.delete
[END_COT_EXT]
==================== */

/**
 * module Personal for Cotonti Siena
 *
 * @package Personal
 * @author Kalnov Alexey
 * @copyright (c) 2014 Portal30 Studio http://portal30.ru
 */

// Удаление вакансий
$udItems = personal_model_Vacancy::find(array(array('user_id', $id)));
if(!empty($udItems)){
    foreach($udItems as $udItemRow){
        $udItemRow->delete();
    }
}

// Удаление резюме
$udItems = personal_model_Resume::find(array(array('user_id', $id)));
if(!empty($udItems)){
    foreach($udItems as $udItemRow){
        $udItemRow->delete();
    }
}

// Удаление профилей работодателей
$udItems = personal_model_EmplProfile::find(array(array('user_id', $id)));
if(!empty($udItems)){
    foreach($udItems as $udItemRow){
        $udItemRow->delete();
    }
}