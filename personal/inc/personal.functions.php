<?php
/**
 * module Personal for Cotonti Siena
 *
 * @package Personal
 * @author Kalnov Alexey
 * @copyright Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

// Lang file
require_once cot_langfile('personal', 'module');

// Global variables
global $L, $db_x, $db_personal_categories, $db_personal_staff, $db_personal_education_levels, $db_personal_empl_profiles,
       $db_personal_languages,
       $db_personal_vacancies, $db_personal_vacancies_employment, $db_personal_vacancies_schedule,

       $db_personal_resumes, $db_personal_resumes_lang_levels, $db_personal_resumes_education,
       $db_personal_resumes_recommend, $db_personal_resumes_experience, $db_personal_resumes_employment,
       $db_personal_resumes_schedule;


$db_personal_categories = (isset($db_personal_categories)) ? $db_personal_categories : $db_x . 'personal_categories';
$db_personal_languages  = (isset($db_personal_languages)) ? $db_personal_languages : $db_x . 'personal_languages';
$db_personal_education_levels = (isset($db_personal_education_levels)) ? $db_personal_education_levels :
    $db_x . 'personal_education_levels';
$db_personal_staff      = (isset($db_personal_staff)) ? $db_personal_staff : $db_x . 'personal_staff';

$db_personal_empl_profiles  = (isset($db_personal_empl_profiles)) ? $db_personal_empl_profiles :
    $db_x . 'personal_empl_profiles';

$db_personal_vacancies  = (isset($db_personal_vacancies)) ? $db_personal_vacancies : $db_x . 'personal_vacancies';
$db_personal_vacancies_employment = (isset($db_personal_vacancies_employment)) ? $db_personal_vacancies_employment :
    $db_x . 'personal_vacancies_employment';
$db_personal_vacancies_schedule = (isset($db_personal_vacancies_schedule)) ? $db_personal_vacancies_schedule :
    $db_x . 'personal_vacancies_schedule';


$db_personal_resumes  = (isset($db_personal_resumes)) ? $db_personal_resumes : $db_x . 'personal_resumes';
$db_personal_resumes_lang_levels  = (isset($db_personal_resumes_lang_levels)) ? $db_personal_resumes_lang_levels :
    $db_x . 'personal_resumes_lang_levels';
$db_personal_resumes_education  = (isset($db_personal_resumes_education)) ? $db_personal_resumes_education :
    $db_x . 'personal_resumes_education';
$db_personal_resumes_recommend  = (isset($db_personal_resumes_recommend)) ? $db_personal_resumes_recommend :
    $db_x . 'personal_resumes_recommendations';
$db_personal_resumes_experience = (isset($db_personal_resumes_experience)) ? $db_personal_resumes_experience :
    $db_x . 'personal_resumes_experience';
$db_personal_resumes_employment = (isset($db_personal_resumes_employment)) ? $db_personal_resumes_employment :
    $db_x . 'personal_resumes_employment';
$db_personal_resumes_schedule = (isset($db_personal_resumes_schedule)) ? $db_personal_resumes_schedule :
    $db_x . 'personal_resumes_schedule';



function personal_select_tree($name, $chosen = array(), $data, $attrs = array(), $labelField = 'title', $parentField = 'parent'){

    if(!defined('PERSONAL_JSTREE')){
        cot_rc_link_footer(cot::$cfg['modules_dir'].'/personal/js/jstree/jstree.min.js');
        cot_rc_link_footer(cot::$cfg['modules_dir'].'/personal/js/jstree/themes/default/style.min.css');
        define('PERSONAL_JSTREE', 1);
    }

    $chosen = cot_import_buffered($name, $chosen);
    $error = cot::$cfg['msg_separate'] ? cot_implode_messages($name, 'error') : '';

    $out = array(); // Массив элементов для вывода

    // Данные - это связанная модель
    if (is_string($data) && is_subclass_of($data, 'Som_Model_Abstract')) {
        /** @var Som_Model_Abstract $modelToLink */
        $modelToLink = $data;

        $itemList   = $modelToLink::find(array(), 0, 0, array($labelField));
        if(!empty($itemList)){
            foreach ($itemList as $Model) {
                $parent = '#';
                if(!empty($Model->{$parentField})){
                    if($Model->{$parentField} instanceof Som_Model_Abstract){
                        $parent = $Model->{$parentField}->getId();
                    }else{
                        $parent = $Model->{$parentField};
                    }
                    if(empty($parent)) $parent = '#';
                }
                $out[] = array(
                    'id' => $Model->getId(),
                    'parent' => $parent,
                    'text' => $Model->{$labelField}
                );
            }
        }
    }else{
        // реализуй меня
    }

    if(!empty($chosen) && !empty($out)){
        foreach($chosen as $val){
            $currVal = null;
            if($val instanceof Som_Model_Abstract){
                $currVal = $val->getId();
            }elseif($val !== null && $val !== ''){
                $currVal = $val;
            }

            if($currVal !== null){
                foreach($out as $okey => $oval){
                    if($oval['id'] == $currVal){
                        $out[$okey]['state'] = array(
                            'opened' => 'true',
                            'selected' => 'true'
                        );
                    }
                }
            }
        }
    }

    if(empty($attrs['id'])) $attrs['id'] = str_replace(array('[]','][','[',']',' '), array('','_','_','','_'), $name);

    if (!empty($attrs['class'])) {
        if (mb_strpos($attrs['class'], 'form-control') === false)
            $attrs['class'] .= ' form-control';
    } else {
        $attrs['class'] = 'form-control';
    }

    if (!empty($attrs['style'])) {
        if (mb_strpos($attrs['style'], 'height') === false)
            $attrs['style'] .= ' height: auto';
    } else {
        $attrs['style'] = 'height: auto';
    }

    cot_rc_embed_footer(
        "$('#{$attrs['id']}').jstree({
                'core' : {
                    'data' : ".json_encode($out)."
                },
                'plugins' : [ 'wholerow', 'checkbox' ]});
            $('#{$attrs['id']}').on('changed.jstree', function (e, data) {
                //console.log(data.selected);
            });
            $('#{$attrs['id']}').parents('form').submit(function(e) {
                var selected = $('#{$attrs['id']}').jstree('get_selected');
                $.each(selected, function( index, value ) {
                    var el = '<input type=\"hidden\" name=\"".$name."[]\" value=\"'+ value +'\">';
                    $('#{$attrs['id']}').after( el );
                });
                //console.log('Form submitted');
                //console.log(selected);
            });"
    );

    $input_attrs = cot_rc_attr_string($attrs);

    $ret = "<div {$input_attrs}></div>{$error}";

    return $ret;
}

/**
 * Опыт работы словами
 *
 * @param int $month Количество месяцев
 * @return string
 */
function personal_friendlyExperience($month){
    global $Ls;

    return 'Сделать вывод опыта работы: personal_friendlyExperience()';
    if(mb_strpos($birthdate, '-') !== false) $birthdate = strtotime($birthdate);

    $age = (int)$birthdate;
    if($age > 300)  $age = cot_build_age($age);

    if(empty($age)) return '';

    $ret = cot_declension($age, $Ls['Years'], false, true);

    return $ret;

}

/**
 * Generates resume list widget
 * @param  string  $tpl        Template code
 * @param  integer $items      Number of items to show. 0 - all items
 * @param  string  $order      Sorting order (SQL)
 * @param  string  $condition  Custom selection filter (SQL)
 * @param  string  $cat        Custom parent category code
 * @param  string  $blacklist  Category black list, semicolon separated
 * @param  string  $whitelist  Category white list, semicolon separated
 * @param  boolean $sub        Include subcategories TRUE/FALSE
 * @param  string  $pagination Pagination parameter name for the URL, e.g. 'pld'. Make sure it does not conflict with other paginations.
 * @param  boolean $noself     Exclude the current page from the rowset for pages.
 * @return string              Parsed HTML
 *
 * @todo категории, $noself
 */
function personal_resumeList($tpl = 'personal.resumelist', $items = 0, $order = '', $condition = '', $cat = '',
                             $blacklist = '', $whitelist = '', $sub = true, $pagination = 'prl', $noself = false)
{
    //global $db, $db_pages, $db_users, $env, $structure;

    // Compile lists
    if (!empty($blacklist))
    {
        $bl = explode(';', $blacklist);
    }

    if (!empty($whitelist))
    {
        $wl = explode(';', $whitelist);
    }

//    // Get the cats
//    $cats = array();
//    if (empty($cat) && (!empty($blacklist) || !empty($whitelist)))
//    {
//        // All cats except bl/wl
//        foreach ($structure['page'] as $code => $row)
//        {
//            if (!empty($blacklist) && !in_array($code, $bl)
//                || !empty($whitelist) && in_array($code, $wl))
//            {
//                $cats[] = $code;
//            }
//        }
//    }
//    elseif (!empty($cat) && $sub)
//    {
//        // Specific cat
//        $cats = cot_structure_children('page', $cat, $sub);
//    }
//
//    if (count($cats) > 0)
//    {
//        if (!empty($blacklist))
//        {
//            $cats = array_diff($cats, $bl);
//        }
//
//        if (!empty($whitelist))
//        {
//            $cats = array_intersect($cats, $wl);
//        }
//
//        $where_cat = "AND page_cat IN ('" . implode("','", $cats) . "')";
//    }
//    elseif (!empty($cat))
//    {
//        $where_cat = "AND page_cat = " . $db->quote($cat);
//    }

    $cond = array();
    if($condition != ''){
        $cond[] = array('SQL', $condition);
    }
    if(empty($condition) || mb_strpos($condition, 'active') === false){
        $cond[] = array('active', 1);
    }

//    if ($noself && defined('COT_PAGES') && !defined('COT_LIST'))
//    {
//        global $id;
//        $where_condition .= " AND page_id != $id";
//    }

    // Get pagination number if necessary
    if (!empty($pagination))
    {
        list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
    }
    else
    {
        $d = 0;
    }

    // Display the items
    $t = new XTemplate(cot_tplfile($tpl, 'module'));

    /* === Hook === */
    foreach (cot_getextplugins('personal.resumelist.query') as $pl)
    {
        include $pl;
    }
    /* ===== */

    $totalitems = personal_model_Resume::count($cond);

    $order = empty($order) ? 'sort DESC' : $order;
    $items = (int)$items;

    $itemList = null;
    if($totalitems > 0) $itemList = personal_model_Resume::find($cond, $items, $d, $order);

    $i = 1;
    if(!empty($itemList)){
        foreach ($itemList as $key => $itemRow){
            $t->assign( personal_model_Resume::generateTags($itemRow, 'RESUME_ROW_'));
            $t->assign(array(
                'RESUME_ROW_NUM'     => $i,
                'RESUME_ROW_ODDEVEN' => cot_build_oddeven($i),
                'RESUME_ROW_RAW'     => $itemRow
            ));

//            $t->assign(cot_generate_usertags($row, 'PAGE_ROW_OWNER_'));

            /* === Hook === */
            foreach (cot_getextplugins('personal.resumelist.loop') as $pl)
            {
                include $pl;
            }
            /* ===== */

            $t->parse("MAIN.RESUME_ROW");
            $i++;
        }
    }
    // Render pagination
    $url_area = defined('COT_PLUG') ? 'plug' : cot::$env['ext'];
    if (defined('COT_LIST'))
    {
        global $list_url_path;
        $url_params = $list_url_path;
    }
    elseif (defined('COT_PAGES'))
    {
        global $al, $id, $pag;
        $url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
    }
    else
    {
        $url_params = array();
    }

    $url_params[$pagination] = $durl;
    $pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination);

    $t->assign(array(
        'RESUME_LIST_PAGINATION'  => $pagenav['main'],
        'RESUME_LIST_PAGEPREV'    => $pagenav['prev'],
        'RESUME_LIST_PAGENEXT'    => $pagenav['next'],
        'RESUME_LIST_FIRST'       => $pagenav['first'],
        'RESUME_LIST_LAST'        => $pagenav['last'],
        'RESUME_LIST_CURRENTPAGE' => $pagenav['current'],
        'RESUME_LIST_TOTALLINES'  => $totalitems,
        'RESUME_LIST_MAXPERPAGE'  => $items,
        'RESUME_LIST_TOTALPAGES'  => $pagenav['total']
    ));

    /* === Hook === */
    foreach (cot_getextplugins('personal.resumelist.tags') as $pl)
    {
        include $pl;
    }
    /* ===== */

    $t->parse();
    return $t->text();
}

/**
 * Generates vacancy list widget
 * @param  string  $tpl        Template code
 * @param  integer $items      Number of items to show. 0 - all items
 * @param  string  $order      Sorting order (SQL)
 * @param  string  $condition  Custom selection filter (SQL)
 * @param  string  $cat        Custom parent category code
 * @param  string  $blacklist  Category black list, semicolon separated
 * @param  string  $whitelist  Category white list, semicolon separated
 * @param  boolean $sub        Include subcategories TRUE/FALSE
 * @param  string  $pagination Pagination parameter name for the URL, e.g. 'pld'. Make sure it does not conflict with other paginations.
 * @param  boolean $noself     Exclude the current page from the rowset for pages.
 * @return string              Parsed HTML
 *
 * @todo категории, $noself
 */
function personal_vacancyList($tpl = 'personal.vacancylist', $items = 0, $order = '', $condition = '', $cat = '',
                             $blacklist = '', $whitelist = '', $sub = true, $pagination = 'pv', $noself = false)
{
    global $db_users;

    // Compile lists
    if (!empty($blacklist))
    {
        $bl = explode(';', $blacklist);
    }

    if (!empty($whitelist))
    {
        $wl = explode(';', $whitelist);
    }

    $cond = array();
    if($condition != ''){
        $cond[] = array('SQL', $condition);
    }
    if(empty($condition) || mb_strpos($condition, 'active') === false){
        $cond[] = array('active', 1);
        $cond[] = array('active_to', date('Y-m-d H:i:s', cot::$sys['now']), '>=');
        $cond[] = array('status', 0);
    }

    // Get pagination number if necessary
    if (!empty($pagination))
    {
        list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
    }
    else
    {
        $d = 0;
    }

    // Display the items
    $t = new XTemplate(cot_tplfile($tpl, 'module'));

    /* === Hook === */
    foreach (cot_getextplugins('personal.vacancylist.query') as $pl)
    {
        include $pl;
    }
    /* ===== */

    $totalitems = personal_model_Vacancy::count($cond);

    $order = empty($order) ? 'sort DESC' : $order;
    $items = (int)$items;

    $itemList = null;
    if($totalitems > 0) $itemList = personal_model_Vacancy::find($cond, $items, $d, $order);

    $i = 1;
    if(!empty($itemList)){

        // Получить пользователей;
        $userIds = array();
        foreach ($itemList as $key => $itemRow){
            if($itemRow->user_id > 0 && !in_array($itemRow->user_id, $userIds)) $userIds[] = $itemRow->user_id;
        }
        $users = array();
        if(!empty($userIds)){
            $res = cot::$db->query("SELECT * FROM $db_users WHERE user_id IN(".implode(',', $userIds).")");
        }
        while($userRow = $res->fetch()){
            $users[$userRow['user_id']] = $userRow;
        }

        foreach ($itemList as $key => $itemRow){
            $t->assign( personal_model_Vacancy::generateTags($itemRow, 'VACANCY_ROW_'));
            $t->assign(array(
                'VACANCY_ROW_NUM'     => $i,
                'VACANCY_ROW_ODDEVEN' => cot_build_oddeven($i),
                'VACANCY_ROW_RAW'     => $itemRow
            ));

            if(isset($users[$itemRow->user_id])){
                $t->assign(cot_generate_usertags($users[$itemRow->user_id], 'VACANCY_ROW_USER_'));
            }else{
                $t->assign(cot_generate_usertags(array('user_id' => 0), 'VACANCY_ROW_USER_'));
            }

            if(!empty($itemRow->profile)){
                $t->assign(personal_model_EmplProfile::generateTags($itemRow->profile, 'VACANCY_ROW_EMPL_PROFILE_'));
            }else{
                $t->assign(array(
                    'VACANCY_ROW_EMPL_PROFILE_ID' => 0,
                    'VACANCY_ROW_EMPL_PROFILE_TITLE' => '',
                ));
            }

            /* === Hook === */
            foreach (cot_getextplugins('personal.vacancylist.loop') as $pl)
            {
                include $pl;
            }
            /* ===== */

            $t->parse("MAIN.VACANCY_ROW");
            $i++;
        }
    }
    // Render pagination
    $url_area = defined('COT_PLUG') ? 'plug' : cot::$env['ext'];
    if (defined('COT_LIST'))
    {
        global $list_url_path;
        $url_params = $list_url_path;
    }
    elseif (defined('COT_PAGES'))
    {
        global $al, $id, $pag;
        $url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
    }
    else
    {
        $url_params = array();
    }

    $url_params[$pagination] = $durl;
    $pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination);

    $t->assign(array(
        'VACANCY_LIST_PAGINATION'  => $pagenav['main'],
        'VACANCY_LIST_PAGEPREV'    => $pagenav['prev'],
        'VACANCY_LIST_PAGENEXT'    => $pagenav['next'],
        'VACANCY_LIST_FIRST'       => $pagenav['first'],
        'VACANCY_LIST_LAST'        => $pagenav['last'],
        'VACANCY_LIST_CURRENTPAGE' => $pagenav['current'],
        'VACANCY_LIST_TOTALLINES'  => $totalitems,
        'VACANCY_LIST_MAXPERPAGE'  => $items,
        'VACANCY_LIST_TOTALPAGES'  => $pagenav['total']
    ));

    /* === Hook === */
    foreach (cot_getextplugins('personal.vacancylist.tags') as $pl)
    {
        include $pl;
    }
    /* ===== */

    $t->parse();
    return $t->text();
}