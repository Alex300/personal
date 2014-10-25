<?php
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Personal admin main controller
 *
 * @package Personal
 * @author Kalnov Alexey
 * @copyright Portal30 Studio http://portal30.ru
 */
class MainController{
    
    /**
     * Main (index) Action.
     */
    public function indexAction(){
        global $L;

        $tmp = cot::$cfg['modules_dir'].'/'.cot::$env['ext'].'/tpl/'.cot::$env['ext'].'.admin.css';
        cot_rc_link_file($tmp);

        $tpl = new XTemplate(cot_tplfile(cot::$env['ext'].'.admin.main'));
        $tpl->assign(array(

            'PAGE_TITLE' => "Панель управления",

        ));
        $tpl->parse('MAIN');

        return $tpl->text();
	}

    public function categoryAction(){
        global $adminpath, $adminsubtitle;

        $adminpath[] = array(cot_url('admin', array('m' => cot::$env['ext'], 'a' => 'category')), cot::$L['Categories']);
        $adminsubtitle = cot::$L['Categories'].' - '.$adminsubtitle;

        cot_rc_link_file(cot::$cfg['themes_dir'].'/'.cot::$cfg['defaulttheme'].'/js/select2/select2.css');
        cot_rc_link_footer(cot::$cfg['themes_dir'].'/'.cot::$cfg['defaulttheme'].'/js/select2/select2.min.js');
        cot_rc_link_footer(cot::$cfg['modules_dir'].'/'.cot::$env['ext'].'/js/'.cot::$env['ext'].'.admin.js');

        $categories = personal_model_Category::getAllFlat();

        $parent = array();
        if(!empty($categories)){
            foreach($categories as $cat){
                $parent[$cat->id] = str_repeat('-', $cat->level * 3).' '.$cat->title;
            }
        }

        $act = cot_import('act', 'P', 'ALP');
        if($act == 'addnew'){
            $category = new personal_model_Category();
            $category->title = trim(cot_import('title', 'P', 'TXT'));
            if(empty($category->title)) $category->title = cot::$L['personal_no_name'];
            $category->parent = cot_import('parent', 'P', 'INT');
            if($id = $category->save()){
                cot_message(sprintf(cot::$L['personal_category_x_added'], $category->title));
            }else{
                cot_message(cot::$L['personal_category_add_error']);
            }
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
        }

        $tpl = new XTemplate(cot_tplfile(cot::$env['ext'].'.admin.category'));

        if(!empty($categories)){
            $i = 1;
            foreach($categories as $key => $cat) {

                $sel = 0;
                if(!empty($cat->parent)) $sel = $cat->parent->id;
                $catChildren = $cat->children();
                $posParents = $parent;
                unset($posParents[$cat->id]);
                // Нельзя назначить родителем одного из своих потомков
                if(!empty($catChildren)){
                    foreach($posParents as $key1 => $val){
                        if(array_key_exists($key1, $catChildren)) unset($posParents[$key1]);
                    }
                }

                $tpl->assign(personal_model_Category::generateTags($cat, 'LIST_ROW_'));
                $tpl->assign(array(
                    'LIST_ROW_NUM' => $i,
                    'LIST_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'LIST_ROW_TD_STYLE' => ($cat->level > 0) ? 'padding-left: '.($cat->level * 33).'px' : '',
                    'LIST_ROW_FORM_TITLE' => cot_inputbox('text', "title[{$cat->id}]", $cat->title,
                        array('class'=>'form-control', 'style'=>"width:97%; display: inline-block")),
                    'LIST_ROW_FORM_PARENT' => cot_selectbox($sel, "parent[{$cat->id}]", array_keys($posParents),
                        array_values($posParents), true,
                        array('class'=>'form-control width100 select2'))
                ));
                $tpl->parse('MAIN.LIST_ROW');
                $i++;
            }
            $tpl->assign(array(
                'LIST_FORM_URL' => cot_url('admin', array('m'=>'personal', 'a'=>'categoryMassSave')),
            ));
        }else{
            $tpl->parse('MAIN.EMPTY');
        }

        $tpl->assign(array(
            'PAGE_TITLE' => cot::$L['Categories'],
            'LIST_TOTALLINES' => count($categories),
            'ADDFORM_NAME' => cot_inputbox('text', 'title', '', array('class'=>'form-control width80')),
            'ADDFORM_PARENT' => cot_selectbox(0, 'parent', array_keys($parent), array_values($parent), true,
                array('class'=>'form-control width100 select2')),
            'ADDFORM_URL'  => cot_url('admin', array('m'=>'personal', 'a'=>'category')),
        ));
        $tpl->parse('MAIN');

        if(!COT_AJAX){
            cot_rc_embed_footer("ajaxSuccessHandlers.push({
                func: function(msg){
                    if(typeof(jQuery.fn.select2) != 'undefined'){
                        $('select.select2').select2({
                            placeholder: 'Кликните для выбора'
                        });
                    }
                }
            });");
        }

        return $tpl->text();
    }

    /**
     * Массовое сохранение категорий
     */
    public function categoryMassSaveAction(){
        global $Ls;

        $categories = personal_model_Category::getAllFlat();

        if(empty($_POST)){
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
        }

        $titleArr = cot_import('title', 'P', 'ARR');
        $parentArr = cot_import('parent', 'P', 'ARR');

        if(empty($titleArr)){
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
        }
        $updated = 0;
        foreach($titleArr as $catid => $title){
            if(empty($categories[$catid])) continue;

            $title = trim(cot_import($title, 'D', 'TXT')); // Фильтр ввода

            $needSave = false;
            if($title != '' && $categories[$catid]->title != $title){
                $categories[$catid]->title = $title;
                $needSave = true;
            }
            $parent = cot_import($parentArr[$catid], 'D', 'INT');
            $catChildren = $categories[$catid]->children();
            if(empty($catChildren)) $catChildren = array();
            $parentId = 0;
            if(!empty($categories[$catid]->parent)) $parentId = $categories[$catid]->parent->id;
            if(!array_key_exists($parent, $catChildren) && $parentId != $parent){
                $categories[$catid]->parent = $parent;
                $needSave = true;
            }
            if($needSave && $categories[$catid]->save()){
                $updated++;
                cot_message(sprintf(cot::$L['personal_category_x_updated'], $categories[$catid]->title));
            }
        }

        if($updated > 0) cot_message(cot::$L['Updated'].': '.cot_declension($updated, $Ls['Category'], false, true));
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
    }

    public function categoryDeleteAction(){
        global $Ls;

        $cid = cot_import('cid', 'G', 'INT');
        if(!$cid){
            cot_message(cot::$L['personal_category_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
        }

        $category = personal_model_Category::getById($cid);
        if(!$category){
            cot_message(cot::$L['personal_category_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
        }

        $children = $category->children();
        $deleted = 0;
        if(!empty($children)){
            foreach($children as $catRow){
                if($catRow->id == $category->id) continue; // Сначала удалим всех потомков
                $title = $catRow->title;
                $catRow->delete();
                $deleted++;
                cot_message(sprintf(cot::$L['personal_category_x_deleted'], $title));
            }
        }

        $title = $category->title;
        $category->delete();
        $deleted++;
        cot_message(sprintf(cot::$L['personal_category_x_deleted'], $title));

        if($deleted > 0) cot_message(cot::$L['Deleted'].': '.cot_declension($deleted, $Ls['Category'], false, true));

        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'category'), '', true));
    }

    /**
     * Уровни в штатном расписании
     */
    public function staffAction(){
        global $adminpath, $adminsubtitle;

        $adminpath[] = array(cot_url('admin', array('m' => cot::$env['ext'], 'a' => 'staff')), cot::$L['personal_staff_levels']);
        $adminsubtitle = cot::$L['personal_staff_levels'].' - '.$adminsubtitle;

        $act = cot_import('act', 'P', 'ALP');
        if($act == 'addnew'){
            $staff = new personal_model_Staff();
            $staff->title = trim(cot_import('title', 'P', 'TXT'));
            if(empty($staff->title)) $staff->title = cot::$L['personal_no_name'];
            if($id = $staff->save()){
                cot_message(sprintf(cot::$L['personal_staff_x_added'], $staff->title));
            }else{
                cot_message(cot::$L['personal_staff_add_error']);
            }
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));
        }

        $tpl = new XTemplate(cot_tplfile(cot::$env['ext'].'.admin.staff'));

        $staffLevels = personal_model_Staff::find();

        if(!empty($staffLevels)){
            $i = 1;
            foreach($staffLevels as $key => $staffRow) {
                $tpl->assign(personal_model_Staff::generateTags($staffRow, 'LIST_ROW_'));
                $tpl->assign(array(
                    'LIST_ROW_NUM' => $i,
                    'LIST_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'LIST_ROW_FORM_TITLE' => cot_inputbox('text', "title[{$staffRow->id}]", $staffRow->title,
                        array('class'=>'form-control', 'style'=>"width:97%; display: inline-block")),
                ));
                $tpl->parse('MAIN.LIST_ROW');
                $i++;
            }
            $tpl->assign(array(
                'LIST_FORM_URL' => cot_url('admin', array('m'=>'personal', 'a'=>'staffMassSave')),
            ));
        }else{
            $tpl->parse('MAIN.EMPTY');
        }

        $tpl->assign(array(
            'PAGE_TITLE' => cot::$L['personal_staff_levels'],
            'LIST_TOTALLINES' => count($staffLevels),
            'ADDFORM_NAME' => cot_inputbox('text', 'title', '', array('class'=>'form-control width80')),
            'ADDFORM_URL'  => cot_url('admin', array('m'=>'personal', 'a'=>'staff')),
        ));
        $tpl->parse('MAIN');

        return $tpl->text();

    }

    /**
     * Массовое сохранение уровней в штатном расписании
     */
    public function staffMassSaveAction(){
        global $Ls;

        if(empty($_POST)) cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));

        /** @var personal_model_Staff[] $staffs */
        $staffs = array();
        $tmp = personal_model_Staff::find();
        if(empty($tmp)) cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));

        foreach($tmp as $staffRow){
            $staffs[$staffRow->id] = $staffRow;
        }

        $titleArr = cot_import('title', 'P', 'ARR');

        if(empty($titleArr)){
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));
        }

        $updated = 0;
        foreach($titleArr as $staffId => $title){
            if(empty($staffs[$staffId])) continue;

            $title = trim(cot_import($title, 'D', 'TXT')); // Фильтр ввода

            $needSave = false;
            if($title != '' && $staffs[$staffId]->title != $title){
                $staffs[$staffId]->title = $title;
                $needSave = true;
            }

            if($needSave && $staffs[$staffId]->save()){
                $updated++;
                cot_message(sprintf(cot::$L['personal_staff_x_updated'], $staffs[$staffId]->title));
            }
        }

        if($updated > 0) cot_message(cot::$L['Updated'].': '.cot_declension($updated, $Ls['Staff'], false, true));
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));
    }

    /**
     * Удаления уровня в штатном расписании
     */
    public function staffDeleteAction(){
        global $Ls;

        $sid = cot_import('sid', 'G', 'INT');
        if(!$sid){
            cot_message(cot::$L['personal_staff_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));
        }

        $item = personal_model_Staff::getById($sid);
        if(!$item){
            cot_message(cot::$L['personal_staff_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));
        }

        $title = $item->title;
        $item->delete();
        cot_message(sprintf(cot::$L['personal_staff_x_deleted'], $title));
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'staff'), '', true));
    }


    /**
     * Уровни образования
     */
    public function educationAction(){
        global $adminpath, $adminsubtitle;

        $adminpath[] = array(cot_url('admin', array('m' => cot::$env['ext'], 'a' => 'education')), cot::$L['personal_education_levels']);
        $adminsubtitle = cot::$L['personal_education_levels'].' - '.$adminsubtitle;

        $act = cot_import('act', 'P', 'ALP');
        if($act == 'addnew'){
            $item = new personal_model_EducationLevel();
            $item->title = trim(cot_import('title', 'P', 'TXT'));
            if(empty($item->title)) $item->title = cot::$L['personal_no_name'];
            if($id = $item->save()){
                cot_message(sprintf(cot::$L['personal_education_x_added'], $item->title));
            }else{
                cot_message(cot::$L['personal_education_level_add_error']);
            }
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));
        }

        $tpl = new XTemplate(cot_tplfile(cot::$env['ext'].'.admin.education_level'));

        $educationLevels = personal_model_EducationLevel::find();

        if(!empty($educationLevels)){
            $i = 1;
            foreach($educationLevels as $key => $educationRow) {
                $tpl->assign(personal_model_EducationLevel::generateTags($educationRow, 'LIST_ROW_'));
                $tpl->assign(array(
                    'LIST_ROW_NUM' => $i,

                    'LIST_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'LIST_ROW_FORM_TITLE' => cot_inputbox('text', "title[{$educationRow->id}]",
                        htmlspecialchars($educationRow->title),array('class'=>'form-control',
                                'style'=>"width:97%; display: inline-block")),
                ));
                $tpl->parse('MAIN.LIST_ROW');
                $i++;
            }
            $tpl->assign(array(
                'LIST_FORM_URL' => cot_url('admin', array('m'=>'personal', 'a'=>'educationMassSave')),
            ));
        }else{
            $tpl->parse('MAIN.EMPTY');
        }

        $tpl->assign(array(
            'PAGE_TITLE' => cot::$L['personal_education_levels'],
            'LIST_TOTALLINES' => count($educationLevels),
            'ADDFORM_NAME' => cot_inputbox('text', 'title', '', array('class'=>'form-control width80')),
            'ADDFORM_URL'  => cot_url('admin', array('m'=>'personal', 'a'=>'education')),
        ));
        $tpl->parse('MAIN');

        return $tpl->text();

    }

    /**
     * Массовое сохранение уровней в штатном расписании
     */
    public function educationMassSaveAction(){
        global $Ls;

        if(empty($_POST)) cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));

        /** @var personal_model_EducationLevel[] $items */
        $items = array();
        $tmp = personal_model_EducationLevel::find();
        if(empty($tmp)) cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));

        foreach($tmp as $itemRow){
            $items[$itemRow->id] = $itemRow;
        }

        $titleArr = cot_import('title', 'P', 'ARR');

        if(empty($titleArr)){
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));
        }

        $updated = 0;
        foreach($titleArr as $itemId => $title){
            if(empty($items[$itemId])) continue;

            $title = trim(cot_import($title, 'D', 'TXT')); // Фильтр ввода

            $needSave = false;
            if($title != '' && $items[$itemId]->title != $title){
                $items[$itemId]->title = $title;
                $needSave = true;
            }

            if($needSave && $items[$itemId]->save()){
                $updated++;
                cot_message(sprintf(cot::$L['personal_education_x_updated'], $items[$itemId]->title));
            }
        }

        if($updated > 0) cot_message(cot::$L['Updated'].': '.cot_declension($updated, $Ls['EducationLevel'], false, true));
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));
    }

    /**
     * Удаление уровня в штатном расписании
     */
    public function educationDeleteAction(){
        $id = cot_import('eid', 'G', 'INT');
        if(!$id){
            cot_message(cot::$L['personal_education_level_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));
        }

        $item = personal_model_EducationLevel::getById($id);
        if(!$item){
            cot_message(cot::$L['personal_education_level_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));
        }

        $title = $item->title;
        $item->delete();
        cot_message(sprintf(cot::$L['personal_education_x_deleted'], $title));
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'education'), '', true));
    }


    /**
     * Языки
     */
    public function languageAction(){
        global $adminpath, $adminsubtitle, $db_personal_languages;

        $adminpath[] = array(cot_url('admin', array('m' => cot::$env['ext'], 'a' => 'language')), cot::$L['Languages']);
        $adminsubtitle = cot::$L['Languages'].' - '.$adminsubtitle;

        $act = cot_import('act', 'P', 'ALP');

        if($act == 'addnew'){
            $count = cot::$db->query("SELECT COUNT(*) FROM $db_personal_languages")->fetchColumn();
            $title = trim(cot_import('title', 'P', 'TXT'));
            if(empty($title)) $title = cot::$L['personal_no_name'];

            $sort = trim(cot_import('sort', 'P', 'INT'));
            if(empty($sort)) $sort = $count + 1;

            cot::$db->insert($db_personal_languages, array('title'=> $title, 'sort' => $sort));
            if($sort <= $count){
                // Reorder items
                $items = cot::$db->query("SELECT * FROM $db_personal_languages ORDER BY sort ASC, title DESC")->fetchAll();
                if(!empty($items)){
                    $i = 1;
                    foreach($items as $itemRow) {
                        $itemRow['sort'] = $i;
                        cot::$db->update($db_personal_languages, array('sort'=>$i), "id={$itemRow['id']}");
                        $i++;
                    }
                }
            }
            cot_message(sprintf(cot::$L['personal_language_x_added'], $title));
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));
        }


        $items = cot::$db->query("SELECT * FROM $db_personal_languages ORDER BY  sort ASC, title DESC")->fetchAll();

        $tpl = new XTemplate(cot_tplfile(cot::$env['ext'].'.admin.language'));

        if(!empty($items)){
            $i = 1;
            foreach($items as $key => $itemRow) {
                $tpl->assign(array(
                    'LIST_ROW_NUM' => $i,
                    'LIST_ROW_ID' => $itemRow['id'],
                    'LIST_ROW_TITLE' => htmlspecialchars($itemRow['title']),
                    'LIST_ROW_SORT' => $itemRow['sort'],
                    'LIST_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'LIST_ROW_FORM_TITLE' => cot_inputbox('text', "title[{$itemRow['id']}]", htmlspecialchars($itemRow['title']),
                        array('class'=>'form-control', 'style'=>"width:97%; display: inline-block")),
                    'LIST_ROW_FORM_SORT' => cot_inputbox('text', "sort[{$itemRow['id']}]", $itemRow['sort'],
                        array('class'=>'form-control', 'style'=>"width:90%; display: inline-block")),
                    'LIST_ROW_DELETE_URL' => cot_confirm_url( cot_url('admin', array('m'=>'personal', 'lid'=>$itemRow['id'], 'a'=>'languageDelete')) )
                ));
                $tpl->parse('MAIN.LIST_ROW');
                $i++;
            }
            $tpl->assign(array(
                'LIST_FORM_URL' => cot_url('admin', array('m'=>'personal', 'a'=>'languageMassSave')),
            ));
        }else{
            $tpl->parse('MAIN.EMPTY');
        }

        $tpl->assign(array(
            'PAGE_TITLE' => cot::$L['Languages'],
            'LIST_TOTALLINES' => count($items),
            'ADDFORM_NAME' => cot_inputbox('text', 'title', '', array('class'=>'form-control width80')),
            'ADDFORM_SORT' => cot_inputbox('text', 'sort', '', array('class'=>'form-control width80')),
            'ADDFORM_URL'  => cot_url('admin', array('m'=>'personal', 'a'=>'language')),
        ));
        $tpl->parse('MAIN');

        return $tpl->text();

    }

    /**
     * Массовое языков
     */
    public function languageMassSaveAction(){
        global $Ls, $db_personal_languages;

        if(empty($_POST)) cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));

        $items = cot::$db->query("SELECT * FROM $db_personal_languages ORDER BY id ASC")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        $count = count($items);

        $titleArr = cot_import('title', 'P', 'ARR');
        $sortArr = cot_import('sort', 'P', 'ARR');

        if(empty($titleArr)){
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));
        }
        $sortArr = array_map('intval', $sortArr);

        $updated = 0;
        foreach($titleArr as $itemId => $title){
            if(empty($items[$itemId])) continue;

            $title = trim(cot_import($title, 'D', 'TXT')); // Фильтр ввода
            $sort  = !empty($sortArr[$itemId]) ? intval($sortArr[$itemId]) : $count + 1;
            if(empty($sort)) $sort = $count + 1;

            $needSave = false;
            if($title != '' && $items[$itemId]['title'] != $title){
                $items[$itemId]['title'] = $title;
                $needSave = true;
            }

            if($items[$itemId]['sort'] != $sort){
                $items[$itemId]['sort'] = $sort;
                $needSave = true;
            }

            if($needSave){
                cot::$db->update($db_personal_languages, $items[$itemId], "id={$itemId}");
                $updated++;
                cot_message(sprintf(cot::$L['personal_language_x_updated'], $items[$itemId]['title']));
            }
        }

        if($updated > 0){
            // Reorder items
            $items = cot::$db->query("SELECT * FROM $db_personal_languages ORDER BY sort ASC, title DESC")->fetchAll();
            if(!empty($items)){
                $i = 1;
                foreach($items as $itemRow) {
                    $itemRow['sort'] = $i;
                    cot::$db->update($db_personal_languages, array('sort'=>$i), "id={$itemRow['id']}");
                    $i++;
                }
            }
            cot_message(cot::$L['Updated'].': '.cot_declension($updated, $Ls['Languages'], false, true));
        }
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));
    }

    /**
     * Удаление языка
     */
    public function languageDeleteAction(){
        global $db_personal_languages;

        $id = cot_import('lid', 'G', 'INT');
        if(!$id){
            cot_message(cot::$L['personal_language_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));
        }

        $item = cot::$db->query("SELECT * FROM $db_personal_languages WHERE id=?", array($id))->fetch();
        if(!$item){
            cot_message(cot::$L['personal_language_not_found']);
            cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));
        }

        $title = $item['title'];
        cot::$db->delete($db_personal_languages, "id={$id}");
        cot_message(sprintf(cot::$L['personal_language_x_deleted'], $title));
        cot_redirect(cot_url('admin', array('m'=>'personal', 'a'=>'language'), '', true));
    }

}