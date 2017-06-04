<?php
defined('COT_CODE') or die('Wrong URL.');


/**
 * Personal Main Controller class
 *
 * @package Personal
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
class MainController
{

    public function indexAction()
    {
        $title = cot::$L['personal'];

        $crumbs = array($title);

        cot::$out['desc'] = $title;
        cot::$out['subtitle'] = $title;

        $tpl = cot_tplfile(array('personal'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),

            'FILTER_CITY' => rec_select2_city('f[city]',0,true, array('class'=>'form-control',
                'placeholder'=>cot::$L['personal_all_cities']))
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();

        return  $t->text();
	}

    public function resumeAction(){
        global $db_x, $db_users;

        $id = cot_import('id', 'G', 'INT');
        if(!empty($id)){
            return $this->resumeShow($id);
        }

        $title = cot::$L['personal_resumes'];

        $crumbs = array(
            array(cot_url('personal'), cot::$L['personal']),
            cot::$L['personal_resumes'],
        );

        cot::$out['desc'] = $title.' - '.cot::$L['personal'];
        cot::$out['subtitle'] = $title.' - '.cot::$L['personal'];

        $staffArr = array();
        $staffs = personal_model_Staff::findByCondition();
        if(!empty($staffs)){
            foreach($staffs as $staffRow){
                $staffArr[$staffRow->id] = $staffRow->title;
            }
        }

        $salaryArr = array(
            '0'      => cot::$L['personal_salary_all'],
            '15000'  => cot::$L['personal_to'].' 15 000 '.cot::$L['personal_money_per_month'],
            '20000'  => cot::$L['personal_to'].' 20 000 '.cot::$L['personal_money_per_month'],
            '25000'  => cot::$L['personal_to'].' 25 000 '.cot::$L['personal_money_per_month'],
            '30000'  => cot::$L['personal_to'].' 30 000 '.cot::$L['personal_money_per_month'],
            '40000'  => cot::$L['personal_to'].' 40 000 '.cot::$L['personal_money_per_month'],
            '50000'  => cot::$L['personal_to'].' 50 000 '.cot::$L['personal_money_per_month'],
            '60000'  => cot::$L['personal_to'].' 60 000 '.cot::$L['personal_money_per_month'],
            '80000'  => cot::$L['personal_to'].' 80 000 '.cot::$L['personal_money_per_month'],
            '100000' => cot::$L['personal_to'].' 100 000 '.cot::$L['personal_money_per_month'],
            '130000' => cot::$L['personal_to'].' 130 000 '.cot::$L['personal_money_per_month'],
            '150000' => cot::$L['personal_to'].' 150 000 '.cot::$L['personal_money_per_month'],
        );

        $eduArr = array();
        $edus = personal_model_EducationLevel::findByCondition();
        if(!empty($edus)){
            foreach($edus as $eduRow){
                $eduArr[$eduRow->id] = $eduRow->title;
            }
        }

        // Принимаем параметры поиска
        $filter          = cot_import('f', 'G', 'ARR');
        $filter['empty'] = true;
        $filter['kw']    = (!empty($filter['kw'])) ? trim(cot_import($filter['kw'], 'D', 'TXT')) : '';
        $filter['city']  = (!empty($filter['city'])) ? cot_import($filter['city'], 'D', 'INT') : 0;
        $filter['leaving']  = (!empty($filter['leaving'])) ? cot_import($filter['leaving'], 'D', 'BOL') : 0;
        if (!empty($filter['staff'])) {
            $filter['staff'] = array_map('intval', $filter['staff']);
        } else {
            $filter['staff'] = array();
        }
        $filter['salary']  = (!empty($filter['salary'])) ? cot_import($filter['salary'], 'D', 'INT') : 0;
        $filter['edu']  = (!empty($filter['edu'])) ? cot_import($filter['edu'], 'D', 'INT') : 0;
        $filter['gen']  = (!empty($filter['gen'])) ? cot_import($filter['gen'], 'D', 'TXT', 1) : '';
        $filter['age_f']  = (!empty($filter['age_f'])) ? cot_import($filter['age_f'], 'D', 'INT') : '';
        $filter['age_t']  = (!empty($filter['age_t'])) ? cot_import($filter['age_t'], 'D', 'INT') : '';
        $filter['period']  = (!empty($filter['period'])) ? cot_import($filter['period'], 'D', 'INT') : 0;
        if (!empty($filter['cat'])) {
            $filter['cat'] = array_map('intval', $filter['cat']);
        } else {
            $filter['cat'] = array();
        }

        // Пагинация
        $maxrowsperpage = cot::$cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
        if($pg > 1) cot::$out['subtitle'] .= cot_rc('code_title_page_num', array('num' => $pg));

        $urlParams = array('a'=>'resume');

        $condition = array(
            array('active', 1),
            array('status', 0),
        );

        $oldFetchСolumns = personal_model_Resume::$fetchСolumns;
        $oldFetchJoins   = personal_model_Resume::$fetchJoins;

        $now = getdate(cot::$sys['now']);

        $filterKw = '';
        if (!empty($filter['kw'])) {
            $filter['empty'] = false;
            $filterKw = $filter['kw'];
            $urlParams['f[kw]'] = $filter['kw'];
            $kw = cot::$db->quote("%{$filter['kw']}%");
            $condition[] = array('SQL', "((`title` LIKE $kw) OR (`text` LIKE $kw) OR (city_name LIKE $kw))
                OR (`district` LIKE $kw) OR (`skills` LIKE $kw)  OR (`other_contacts` LIKE $kw)");
        }
        $filterCity = '';
        if (!empty($filter['city'])) {
            $tmp = regioncity_model_City::getById($filter['city']);
            if ($tmp) {
                $filter['empty'] = false;
                $urlParams['f[city]'] = $filter['city'];
                if(!$filter['leaving']){
                    $condition[] = array('city', $filter['city']);
                    $urlParams['f[city]'] = $filter['city'];
                }else{
                    //возможен переезд
                    $urlParams['f[leaving]'] = 1;
                    $condition[] = array('SQL', "(city={$filter['city']} OR
                        id IN(SELECT DISTINCT {$db_x}personal_resumes_id FROM  {$db_x}personal_resumes_link_{$db_x}city
                        WHERE {$db_x}city_city_id={$filter['city']}) )");
                }
                $filterCity  = $tmp->city_title;
            } else {
                $filter['city'] = 0;
            }
        }

        // Уровень в штатном расписании
        if (!empty($filter['staff'])) {
            $filter['empty'] = false;
            $i = 0;
            foreach($filter['staff'] as $val){
                $urlParams['f[staff]['.$i.']'] = $val;
                $i++;
            }
            // ИЛИ
            $condition[] = array('SQL', "id IN(SELECT DISTINCT {$db_x}personal_resumes_id
                        FROM  {$db_x}personal_resumes_link_{$db_x}personal_staff
                        WHERE {$db_x}personal_staff_id IN (" . implode(', ', $filter['staff']) . "))");

        }

        // Оклады
        if ($filter['salary']) {
            $filter['empty'] = false;
            $urlParams['f[salary]'] = $filter['salary'];
            $condition[] = array('salary', $filter['salary'], '<=');
        }

        // уровень образования
        if(!empty($filter['edu'])){
            if(array_key_exists($filter['edu'], $eduArr)){
                $filter['empty'] = false;
                $urlParams['f[edu]'] = $filter['edu'];
                $condition[] = array('education_level', $filter['edu'], '<=');
            }else{
                $filter['edu'] = 0;
            }
        }

        if(!empty($filter['gen']) || !empty($filter['age_f']) || !empty($filter['age_t'])){
            personal_model_Resume::$fetchJoins[] = "\n LEFT JOIN {$db_users} ON {$db_x}personal_resumes.user_id={$db_users}.user_id";
        }

        //пол
        if(!empty($filter['gen'])){
            if($filter['gen'] == 'M' OR $filter['gen'] == 'F'){
                $urlParams['f[gen]'] = $filter['gen'];
                $condition[] = array('SQL', "{$db_users}.user_gender=".cot::$db->quote($filter['gen']));
            }else{
                $filter['gen'] = '';
            }
        }

        //возраст
        if(!empty($filter['age_f'])){
            if($filter['age_f'] > 0){
                $urlParams['f[age_f]'] = $filter['age_f'];
                $beg  = mktime(0, 0, 0, $now['mon'], $now["mday"], $now['year'] - $filter['age_f']);
                $condition[] = array('SQL', "{$db_users}.user_birthdate<='".date('Y-m-d 00:01:01', $beg)."'");
            }else{
                $filter['age_f'] = '';
            }
        }
        if(!empty($filter['age_t'])){
            if($filter['age_t'] > 0){
                $urlParams['f[age_t]'] = $filter['age_t'];
                $beg  = mktime(0, 0, 0, $now['mon'], $now["mday"] + 1, $now['year'] - $filter['age_t'] - 1);
                $condition[] = array('SQL', "{$db_users}.user_birthdate>'".date('Y-m-d 00:01:01', $beg)."'");
            }else{
                $filter['age_t'] = '';
            }
        }

        // Период
        if ($filter['period'] == -1) {
            $filter['empty'] = false;
            $urlParams['f[period]'] = -1;
            $beg             = mktime(0, 0, 0, $now['mon'], $now["mday"] - 1, $now['year']);
            $condition[] = array('created', date('Y-m-d H:i:s', $beg), '>=');
        } elseif ($filter['period'] > 0) {
            $filter['empty'] = false;
            $urlParams['f[period]'] = $filter['period'];
            $beg             = mktime(0, 0, 0, $now['mon'], $now["mday"] - $filter['period'], $now['year']);
            $condition[] = array('sort', date('Y-m-d H:i:s', $beg), '>=');
        }

        // Категории
        $filterCategory = array();
        if (!empty($filter['cat'])) {
            $filter['empty'] = false;
            $tmpCat          = personal_model_Category::findByCondition(array(array('id', $filter['cat'])), 0, 0, array(array('title', 'asc')));
            if ($tmpCat) {
                foreach ($filter['cat'] as $key => $cat) {
                    if (!array_key_exists($cat, $tmpCat)) {
                        unset($filter['cat'][$key]);
                        continue;
                    }
                    $filterCategory[] = $tmpCat[$cat]->title;
                }
                unset($tmpCat);
                if (!empty($filter['cat'])) {
                    $i = 0;
                    foreach($filter['cat'] as $val){
                        $urlParams['f[cat]['.$i.']'] = $val;
                        $i++;
                    }

                    // ИЛИ
                    $condition[] = array('SQL', "id IN(SELECT DISTINCT {$db_x}personal_resumes_id
                        FROM  {$db_x}personal_resumes_link_{$db_x}personal_categories
                        WHERE {$db_x}personal_categories_id IN (" . implode(', ', $filter['cat']) . "))");

                }else{
                    $filter['cat'] = array();
                }
            } else {
                $filter['cat'] = array();
            }
        }

        $totallines = personal_model_Resume::count($condition);
        $totalpages = ceil($totallines / $maxrowsperpage);
        if($totalpages == 0) $totalpages = 1;
        if($pg > 1 && $totalpages < $pg){
            if($totalpages > 1) $urlParams['d'] = $totalpages;
            cot_redirect(cot_url('personal', $urlParams, '', true));
        }

        $resumes = null;
        if($totallines > 0){
            $resumes = personal_model_Resume::findByCondition($condition, $maxrowsperpage, $d, array(
                array('hot', 'DESC'), array('sort', 'DESC'), array('activated', 'desc')
            ));
        }

        // Вернем на место
        personal_model_Resume::$fetchСolumns = $oldFetchСolumns;
        personal_model_Resume::$fetchJoins   = $oldFetchJoins;

        $pagenav = cot_pagenav('personal', $urlParams, $d, $totallines, $maxrowsperpage);

        $tpl = cot_tplfile(array('personal', 'resume', 'list'), 'module');
        $t = new XTemplate($tpl);

        if(!empty($resumes)){
            $i = 1;
            foreach($resumes as $resumeRow){
                $t->assign(personal_model_Resume::generateTags($resumeRow, 'RESUME_ROW_'));
                $t->assign(array(
                    'RESUME_ROW_NUM' => $i,
                    'RESUME_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'RESUME_ROW_RAW'     => $resumeRow,
                    'RESUME_ROW_CATEGORIES_COUNT' => (!empty($resumeRow->category)) ? count($resumeRow->category) : 0
                ));
                if(!empty($resumeRow->category)){
                    $j = 1;
                    foreach($resumeRow->category as $catRow){
                        $t->assign(personal_model_Category::generateTags($catRow, 'RESUME_ROW_CATEGORY_ROW_'));
                        $t->assign(array(
                            'RESUME_ROW_CATEGORY_ROW_NUM' => $j,
                            'RESUME_ROW_CATEGORY_ROW_ODDEVEN' => cot_build_oddeven($j),
                            'RESUME_ROW_CATEGORY_ROW_RAW'     => $catRow,
                        ));
                        $t->parse('MAIN.RESUME_ROW.CATEGORY_ROW');
                        $j++;
                    }
                }
                $t->parse('MAIN.RESUME_ROW');
                $i++;
            }
        }else{
            $t->parse('MAIN.EMPTY');
        }

        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),

            'FILTER_QUERY' => cot_inputbox('text', 'f[kw]', $filter['kw'], array('class'=>'form-control',
                'id'=>'keywords', 'placeholder'=>cot::$L['personal_search_query'])),
            'FILTER_CITY' => rec_select2_city('f[city]', $filter['city'], true, array('class'=>'form-control',
                'placeholder'=>cot::$L['personal_all_cities'])),
            'FILTER_LEAVING' => cot_checkbox($filter['leaving'], 'f[leaving]', cot::$L['personal_resume_leaving']),
            'FILTER_STAFF' => cot_checklistbox($filter['staff'], 'f[staff]', array_keys($staffArr), array_values($staffArr),
                '', "\n", false),
            'FILTER_SALARY' => cot_selectbox($filter['salary'], 'f[salary]', array_keys($salaryArr), array_values($salaryArr),
                false),
            'FILTER_EDUCATION' => cot_selectbox($filter['edu'], 'f[edu]', array_keys($eduArr), array_values($eduArr)),
            'FILTER_GENDER' => cot_selectbox($filter['gen'], 'f[gen]', array('M', 'F'),
                array(cot::$L['Gender_M'], cot::$L['Gender_F'])),
            'FILTER_AGE_FROM' => cot_inputbox('text', 'f[age_f]', $filter['age_f']),
            'FILTER_AGE_TO' => cot_inputbox('text', 'f[age_t]', $filter['age_t']),
            'FILTER_PERIOD' => cot_selectbox($filter['period'], 'f[period]', array_keys(cot::$L['personal_periods']),
                array_values(cot::$L['personal_periods']), false),
            'FILTER_CATEGORY' => personal_select_tree('f[cat]', $filter['cat'], 'personal_model_Category',
                array('id' => 'filter_resume_category')),

            'LIST_SUBMITNEW_URL' => cot::$usr['auth_write'] ? cot_url('personal', 'm=user&a=resumeEdit') : "",
            'LIST_PAGINATION' => $pagenav['main'],
            'LIST_PAGEPREV' => $pagenav['prev'],
            'LIST_PAGENEXT' => $pagenav['next'],
            'LIST_CURRENTPAGE' => $pagenav['current'],
            'LIST_TOTALLINES' => $totallines,
            'LIST_MAXPERPAGE' => $maxrowsperpage,
            'LIST_TOTALPAGES' => $pagenav['total']
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();

        return  $t->text();
    }


    /**
     * Отображение одного резюме
     * @param $rid id резюме
     * @return string
     */
    public function resumeShow($rid){
        if(!$rid) cot_die_message(404);

        $resume = personal_model_Resume::getById($rid);
        if(!$resume) cot_die_message(404);

        cot::$env['location'] = 'personal.resume';

        $uid  = $resume->user_id;
        $urr = cot_user_data($uid);

        $title = htmlspecialchars($resume->title);

        $crumbs = array(
            array(cot_url('personal'), cot::$L['personal']),
            array(cot_url('personal', array('a'=>'resume')), cot::$L['personal_resumes']),
            $resume->title
        );

        cot::$out['desc'] = $title .' - '.cot::$L['personal_resumes'].' - '.cot::$L['personal'];
        cot::$out['subtitle'] = $title.' - '.cot::$L['personal_resumes'].' - '.cot::$L['personal'];

        if(!empty($resume->category)){
            cot_rc_embed_footer('$("#treebox-'.$resume->id.'")
                // listen for event
                .on("ready.jstree", function (e) {
                    $("#treebox-'.$resume->id.'").jstree("open_all");
                })
                .on("select_node.jstree", function (e, data) {
                    $("#treebox-'.$resume->id.'").jstree("deselect_all");
                })
                // create the instance
                .jstree({ "core": { "data":'.personal_model_Category::array2jstree($resume->category).' } });');
        }

        $tpl = cot_tplfile(array('personal', 'resume'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(personal_model_Resume::generateTags($resume, 'RESUME_'));

        $t->assign(cot_generate_usertags($urr, 'USER_'));
        $t->assign(array(
            'RESUME_CAN_EDIT' => cot::$usr['isadmin'] || $uid = cot::$usr['id'],
            'RESUME_ID' => ($resume->id > 0) ? $resume->id : 0,

            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),
            'USER_GENDER_RAW' => $urr['user_gender'],
        ));

        if(!empty($resume->languages)){
            foreach($resume->languages as $langLevelId => $langRow){
                $t->assign(array(
                    'RESUME_LANG_ROW_ID' => $langLevelId,
                    'RESUME_LANG_ROW_LANG_ID' => $langRow['lang_id'],
                    'RESUME_LANG_ROW_LVL_ID' => $langRow['level_id'],
                    'RESUME_LANG_ROW_TITLE' => htmlspecialchars($langRow['lang_title']),
                    'RESUME_LANG_ROW_LVL_TITLE' => htmlspecialchars($langRow['level_title']),
                ));
                $t->parse('MAIN.RESUME_LANG_ROW');
            }
        }

        if(!empty($resume->education)){
            foreach($resume->education as $eduId => $eduRow){
                $t->assign(array(
                    'RESUME_EDU_ROW_ID' => $eduId,
                    'RESUME_EDU_ROW_TITLE' => htmlspecialchars($eduRow['education_title']),
                    'RESUME_EDU_ROW_FACULTY' => htmlspecialchars($eduRow['faculty']),
                    'RESUME_EDU_ROW_SPECIALTY' => htmlspecialchars($eduRow['specialty']),
                    'RESUME_EDU_ROW_YEAR' => $eduRow['year'],
                    'RESUME_EDU_ROW_LVL_ID' => $eduRow['level_id'],
                    'RESUME_EDU_ROW_LVL_TITLE' => htmlspecialchars($eduRow['level_title']),
                ));
                $t->parse('MAIN.RESUME_EDU_ROW');
            }
        }

        if(!empty($resume->recommendations)){
            foreach($resume->recommendations as $recId => $recRow){
                $t->assign(array(
                    'RESUME_RECOMMEND_ROW_ID' => $recId,
                    'RESUME_RECOMMEND_ROW_NAME' => htmlspecialchars($recRow['name']),
                    'RESUME_RECOMMEND_ROW_POSITION' => htmlspecialchars($recRow['position']),
                    'RESUME_RECOMMEND_ROW_ORGANIZATION' => htmlspecialchars($recRow['organization']),
                    'RESUME_RECOMMEND_ROW_PHONE' => htmlspecialchars($recRow['phone']),
                ));
                $t->parse('MAIN.RESUME_RECOMMEND_ROW');
            }
        }

        if(!empty($resume->experiences)){
            foreach($resume->experiences as $expId => $expRow){
                $begin = $beginDate = '';
                $beginStamp = 0;
                if(strtotime($expRow['begin']) > 10){
                    $beginStamp = strtotime($expRow['begin']);
                    $begin = cot_date('F Y', $beginStamp, false);
                    $beginDate = cot_date('date_full', $beginStamp, false);
                }
                $end = $endDate = '';
                $endStamp = 0;
                if(strtotime($expRow['end']) > 10){
                    $endStamp = strtotime($expRow['end']);
                    $end = cot_date('F Y', $endStamp, false);
                    $endDate = cot_date('date_full', $endStamp, false);
                }else{
                    $expRow['for_now'] = 1;
                }
                $t->assign(array(
                    'RESUME_EXP_ROW_ID' => $expId,
                    'RESUME_EXP_ROW_ORGANIZATION' => htmlspecialchars($expRow['organization']),
                    'RESUME_EXP_ROW_CITY_ID' => $expRow['city'],
                    'RESUME_EXP_ROW_CITY_TITLE' => htmlspecialchars($expRow['city_title']),
                    'RESUME_EXP_ROW_POSITION' => htmlspecialchars($expRow['position']),
                    'RESUME_EXP_ROW_WEBSITE' => htmlspecialchars($expRow['website']),
                    'RESUME_EXP_ROW_BEGIN' => $begin,
                    'RESUME_EXP_ROW_BEGIN_DATE' => $beginDate,
                    'RESUME_EXP_ROW_BEGIN_STAMP' => $beginStamp,
                    'RESUME_EXP_ROW_END' => ($expRow['for_now'] == 0) ? $end : cot::$L['personal_resume_for_now'],
                    'RESUME_EXP_ROW_END_DATE' => $endDate,
                    'RESUME_EXP_ROW_END_STAMP' => $endStamp,
                    'RESUME_EXP_ROW_FOR_NOW' => $expRow['for_now'],
                    'RESUME_EXP_ROW_ACHIEVEMENTS' => $expRow['achievements']
                ));
                $t->parse('MAIN.RESUME_EXP_ROW');
            }
        }

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }

    public function vacancyAction(){
        global $db_x, $db_users;

        $id = cot_import('id', 'G', 'INT');
        if(!empty($id)){
            return $this->vacancyShow($id);
        }

        $title = cot::$L['personal_vacancies'];

        $crumbs = array(
            array(cot_url('personal'), cot::$L['personal']),
            cot::$L['personal_vacancies'],
        );

        cot::$out['desc'] = $title.' - '.cot::$L['personal'];
        cot::$out['subtitle'] = $title.' - '.cot::$L['personal'];


        $staffArr = array();
        $staffs = personal_model_Staff::findByCondition();
        if(!empty($staffs)){
            foreach($staffs as $staffRow){
                $staffArr[$staffRow->id] = $staffRow->title;
            }
        }

        $salaryArr = array(
            '0'      => cot::$L['personal_salary_all'],
            '15000'  => cot::$L['personal_from'].' 15 000 '.cot::$L['personal_money_per_month'],
            '20000'  => cot::$L['personal_from'].' 20 000 '.cot::$L['personal_money_per_month'],
            '25000'  => cot::$L['personal_from'].' 25 000 '.cot::$L['personal_money_per_month'],
            '30000'  => cot::$L['personal_from'].' 30 000 '.cot::$L['personal_money_per_month'],
            '40000'  => cot::$L['personal_from'].' 40 000 '.cot::$L['personal_money_per_month'],
            '50000'  => cot::$L['personal_from'].' 50 000 '.cot::$L['personal_money_per_month'],
            '60000'  => cot::$L['personal_from'].' 60 000 '.cot::$L['personal_money_per_month'],
            '80000'  => cot::$L['personal_from'].' 80 000 '.cot::$L['personal_money_per_month'],
            '100000' => cot::$L['personal_from'].' 100 000 '.cot::$L['personal_money_per_month'],
            '130000' => cot::$L['personal_from'].' 130 000 '.cot::$L['personal_money_per_month'],
            '150000' => cot::$L['personal_from'].' 150 000 '.cot::$L['personal_money_per_month'],
        );

        $eduArr = array();
        $edus = personal_model_EducationLevel::findByCondition();
        if(!empty($edus)){
            foreach($edus as $eduRow){
                $eduArr[$eduRow->id] = $eduRow->title;
            }
        }

        // Принимаем параметры поиска
        $filter          = cot_import('f', 'G', 'ARR');
        $filter['empty'] = true;
        $filter['kw']    = (!empty($filter['kw'])) ? trim(cot_import($filter['kw'], 'D', 'TXT')) : '';
        $filter['city']  = (!empty($filter['city'])) ? cot_import($filter['city'], 'D', 'INT') : 0;
        if (!empty($filter['staff'])) {
            $filter['staff'] = array_map('intval', $filter['staff']);
        } else {
            $filter['staff'] = array();
        }
        $filter['salary']  = (!empty($filter['salary'])) ? cot_import($filter['salary'], 'D', 'INT') : 0;
        $filter['edu']     = (!empty($filter['edu'])) ? cot_import($filter['edu'], 'D', 'INT') : 0;
        $filter['period']  = (!empty($filter['period'])) ? cot_import($filter['period'], 'D', 'INT') : 0;
        if (!empty($filter['cat'])) {
            $filter['cat'] = array_map('intval', $filter['cat']);
        } else {
            $filter['cat'] = array();
        }

        // Пагинация
        $maxrowsperpage = cot::$cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
        if($pg > 1) cot::$out['subtitle'] .= cot_rc('code_title_page_num', array('num' => $pg));

        $urlParams = array('a'=>'resume');

        $condition = array(
            array('active', 1),
            array('active_to', date('Y-m-d H:i:s', cot::$sys['now']), '>='),
            array('status', 0),
        );

        $now = getdate(cot::$sys['now']);

        $filterKw = '';
        if (!empty($filter['kw'])) {
            $filter['empty'] = false;
            $filterKw = $filter['kw'];
            $urlParams['f[kw]'] = $filter['kw'];
            $kw = cot::$db->quote("%{$filter['kw']}%");
            $condition[] = array('SQL', "((`title` LIKE $kw) OR (`text` LIKE $kw) OR (city_name LIKE $kw))
                OR (`district` LIKE $kw) OR (`skills` LIKE $kw)  OR (`other_contacts` LIKE $kw)");
        }
        $filterCity = '';
        if (!empty($filter['city'])) {
            $tmp = regioncity_model_City::getById($filter['city']);
            if ($tmp) {
                $filter['empty'] = false;
                $urlParams['f[kw]'] = $filter['city'];
                $condition[] = array('city', $filter['city']);
                $filterCity  = $tmp->city_title;
            } else {
                $filter['city'] = 0;
            }
        }

        // Уровень в штатном расписании
        if (!empty($filter['staff'])) {
            $filter['empty'] = false;
            $i = 0;
            foreach($filter['staff'] as $val){
                $urlParams['f[staff]['.$i.']'] = $val;
                $i++;
            }
            // ИЛИ
            $condition[] = array('SQL', "id IN(SELECT DISTINCT {$db_x}personal_vacancies_id
                        FROM  {$db_x}personal_vacancies_link_{$db_x}personal_staff
                        WHERE {$db_x}personal_staff_id IN (" . implode(', ', $filter['staff']) . "))");

        }

        // Оклады
        if ($filter['salary']) {
            $filter['empty'] = false;
            $urlParams['f[salary]'] = $filter['salary'];
            $condition[] = array('salary', $filter['salary'], '>=');
        }

        // уровень образования
        if(!empty($filter['edu'])){
            if(array_key_exists($filter['edu'], $eduArr)){
                $filter['empty'] = false;
                $urlParams['f[edu]'] = $filter['edu'];
                $condition[] = array('education_level', $filter['edu'], '<=');
            }else{
                $filter['edu'] = 0;
            }
        }

        // Период
        if ($filter['period'] == -1) {
            $filter['empty'] = false;
            $urlParams['f[period]'] = -1;
            $beg             = mktime(0, 0, 0, $now['mon'], $now["mday"] - 1, $now['year']);
            $condition[] = array('created', date('Y-m-d H:i:s', $beg), '>=');
        } elseif ($filter['period'] > 0) {
            $filter['empty'] = false;
            $urlParams['f[period]'] = $filter['period'];
            $beg             = mktime(0, 0, 0, $now['mon'], $now["mday"] - $filter['period'], $now['year']);
            $condition[] = array('sort', date('Y-m-d H:i:s', $beg), '>=');
        }

        // Категории
        $filterCategory = array();
        if (!empty($filter['cat'])) {
            $filter['empty'] = false;
            $tmpCat          = personal_model_Category::findByCondition(array(array('id', $filter['cat'])), 0,
                0, array(array('title', 'asc')));
            if ($tmpCat) {
                foreach ($filter['cat'] as $key => $cat) {
                    if (!array_key_exists($cat, $tmpCat)) {
                        unset($filter['cat'][$key]);
                        continue;
                    }
                    $filterCategory[] = $tmpCat[$cat]->title;
                }
                unset($tmpCat);
                if (!empty($filter['cat'])) {
                    $i = 0;
                    foreach($filter['cat'] as $val){
                        $urlParams['f[cat]['.$i.']'] = $val;
                        $i++;
                    }

                    // ИЛИ
                    $condition[] = array('SQL', "id IN(SELECT DISTINCT {$db_x}personal_vacancies_id
                        FROM  {$db_x}personal_vacancies_link_{$db_x}personal_categories
                        WHERE {$db_x}personal_categories_id IN (" . implode(', ', $filter['cat']) . "))");

                }else{
                    $filter['cat'] = array();
                }
            } else {
                $filter['cat'] = array();
            }
        }

        $totallines = personal_model_Vacancy::count($condition);

        $totalpages = ceil($totallines / $maxrowsperpage);
        if($totalpages == 0) $totalpages = 1;
        if($pg > 1 && $totalpages < $pg){
            if($totalpages > 1) $urlParams['d'] = $totalpages;
            cot_redirect(cot_url('personal', $urlParams, '', true));
        }

        $vacancies = null;
        if($totallines > 0){
            $vacancies = personal_model_Vacancy::findByCondition($condition, $maxrowsperpage, $d, array(
                array('hot', 'DESC'), array('sort', 'DESC'), array('activated', 'desc')
            ));
        }
        $pagenav = cot_pagenav('personal', $urlParams, $d, $totallines, $maxrowsperpage);

        $tpl = cot_tplfile(array('personal', 'vacancy', 'list'), 'module');
        $t = new XTemplate($tpl);


        if(!empty($vacancies)){
            // Получить пользователей;
            $userIds = array();
            foreach ($vacancies as $key => $itemRow){
                if($itemRow->user_id > 0 && !in_array($itemRow->user_id, $userIds)) $userIds[] = $itemRow->user_id;
            }
            $users = array();
            if(!empty($userIds)){
                $res = cot::$db->query("SELECT * FROM $db_users WHERE user_id IN(".implode(',', $userIds).")");
            }
            while($userRow = $res->fetch()){
                $users[$userRow['user_id']] = $userRow;
            }

            $i = 1;
            foreach($vacancies as $vacancyRow){
                $t->assign(personal_model_Vacancy::generateTags($vacancyRow, 'VACANCY_ROW_'));
                $t->assign(array(
                    'VACANCY_ROW_NUM' => $i,
                    'VACANCY_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'VACANCY_ROW_RAW'     => $vacancyRow,
                    'VACANCY_ROW_CATEGORIES_COUNT' => (!empty($vacancyRow->category)) ? count($vacancyRow->category) : 0
                ));
                if(!empty($vacancyRow->category)){
                    $j = 1;
                    foreach($vacancyRow->category as $catRow){
                        $t->assign(personal_model_Category::generateTags($catRow, 'VACANCY_ROW_CATEGORY_ROW_'));
                        $t->assign(array(
                            'VACANCY_ROW_CATEGORY_ROW_NUM' => $j,
                            'VACANCY_ROW_CATEGORY_ROW_ODDEVEN' => cot_build_oddeven($j),
                            'VACANCY_ROW_CATEGORY_ROW_RAW'     => $catRow,
                        ));
                        $t->parse('MAIN.VACANCY_ROW.CATEGORY_ROW');
                        $j++;
                    }
                }

                if(isset($users[$vacancyRow->user_id])){
                    $t->assign(cot_generate_usertags($users[$vacancyRow->user_id], 'VACANCY_ROW_USER_'));
                }else{
                    $t->assign(cot_generate_usertags(array('user_id' => 0), 'VACANCY_ROW_USER_'));
                }

                if(!empty($vacancyRow->profile)){
                    $t->assign(personal_model_EmplProfile::generateTags($vacancyRow->profile, 'VACANCY_ROW_EMPL_PROFILE_'));
                }else{
                    $t->assign(array(
                        'VACANCY_ROW_EMPL_PROFILE_ID' => 0,
                        'VACANCY_ROW_EMPL_PROFILE_TITLE' => '',
                    ));
                }

                $t->parse('MAIN.VACANCY_ROW');
                $i++;
            }
        }else{
            $t->parse('MAIN.EMPTY');
        }

        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),

            'FILTER_QUERY' => cot_inputbox('text', 'f[kw]', $filter['kw'], array('class'=>'form-control',
                'id'=>'keywords', 'placeholder'=>cot::$L['personal_search_query'])),
            'FILTER_CITY' => rec_select2_city('f[city]', $filter['city'], true, array('class'=>'form-control',
                'placeholder'=>cot::$L['personal_all_cities'])),
            'FILTER_STAFF' => cot_checklistbox($filter['staff'], 'f[staff]', array_keys($staffArr), array_values($staffArr),
                '', "\n", false),
            'FILTER_SALARY' => cot_selectbox($filter['salary'], 'f[salary]', array_keys($salaryArr), array_values($salaryArr),
                false),
            'FILTER_EDUCATION' => cot_selectbox($filter['edu'], 'f[edu]', array_keys($eduArr), array_values($eduArr)),
            'FILTER_PERIOD' => cot_selectbox($filter['period'], 'f[period]', array_keys(cot::$L['personal_periods']),
                array_values(cot::$L['personal_periods']), false),
            'FILTER_CATEGORY' => personal_select_tree('f[cat]', $filter['cat'], 'personal_model_Category',
                array('id' => 'filter_vacancy_category')),

            'LIST_SUBMITNEW_URL' => cot::$usr['auth_write'] ? cot_url('personal', 'm=user&a=vacancyEdit') : "",
            'LIST_PAGINATION' => $pagenav['main'],
            'LIST_PAGEPREV' => $pagenav['prev'],
            'LIST_PAGENEXT' => $pagenav['next'],
            'LIST_CURRENTPAGE' => $pagenav['current'],
            'LIST_TOTALLINES' => $totallines,
            'LIST_MAXPERPAGE' => $maxrowsperpage,
            'LIST_TOTALPAGES' => $pagenav['total']
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }

    /**
     * Отображение одногой вакансии
     * @param $vid id резюме
     * @return string
     */
    public function vacancyShow($vid){
        if(!$vid) cot_die_message(404);

        $vacancy = personal_model_Vacancy::getById($vid);
        if(!$vacancy) cot_die_message(404);

        cot::$env['location'] = 'personal.vacancy';

        $uid  = $vacancy->user_id;
        $urr = cot_user_data($uid);

        $title = htmlspecialchars($vacancy->title);

        $crumbs = array(
            array(cot_url('personal'), cot::$L['personal']),
            array(cot_url('personal', array('a'=>'vacancy')), cot::$L['personal_vacancies']),
            $vacancy->title
        );

        cot::$out['desc'] = $title .' - '.cot::$L['personal_vacancies'].' - '.cot::$L['personal'];
        cot::$out['subtitle'] = $title.' - '.cot::$L['personal_vacancies'].' - '.cot::$L['personal'];

        if(!empty($vacancy->category)){
            cot_rc_embed_footer('$("#treebox-'.$vacancy->id.'")
                // listen for event
                .on("ready.jstree", function (e) {
                    $("#treebox-'.$vacancy->id.'").jstree("open_all");
                })
                .on("select_node.jstree", function (e, data) {
                    $("#treebox-'.$vacancy->id.'").jstree("deselect_all");
                })
                // create the instance
                .jstree({ "core": { "data":'.personal_model_Category::array2jstree($vacancy->category).' } });');
        }

        $tpl = cot_tplfile(array('personal', 'vacancy'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(personal_model_Vacancy::generateTags($vacancy, 'VACANCY_'));

        $t->assign(cot_generate_usertags($urr, 'VACANCY_USER_'));
        $t->assign(array(
            'VACANCY_CAN_EDIT' => cot::$usr['isadmin'] || $uid = cot::$usr['id'],
            'VACANCY_ID' => ($vacancy->id > 0) ? $vacancy->id : 0,

            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),
            'USER_GENDER_RAW' => $urr['user_gender'],
        ));

        if(!empty($vacancy->profile) && $vacancy->profile->locked == 0){
            $t->assign(personal_model_EmplProfile::generateTags($vacancy->profile, 'VACANCY_EMPL_PROFILE_'));
        }else{
            $t->assign(array(
                'VACANCY_EMPL_PROFILE_ID' => 0,
                'VACANCY_EMPL_PROFILE_TITLE' => '',
            ));
        }

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }


    public function employerAction(){
        $id = cot_import('id', 'G', 'INT');
        if(!empty($id)){
            return $this->employerShow($id);
        }

        cot_die_message(404);

        // тут возможныйсписок работодателей
    }

    public function employerShow($eid){
        if(!$eid) cot_die_message(404);

        $employer = personal_model_EmplProfile::getById($eid);
        if(!$employer) cot_die_message(404);

        cot::$env['location'] = 'personal.employer';

        $uid  = $employer->user_id;
        $urr = cot_user_data($uid);

        $title = htmlspecialchars($employer->title);

        $crumbs = array(
            array(cot_url('personal'), cot::$L['personal']),
//            array(cot_url('personal', array('a'=>'employer')), cot::$L['personal_vacancies']),
            $employer->title
        );

        cot::$out['desc'] = $title .' - '.cot::$L['personal_employer_info'].' - '.cot::$L['personal'];
        cot::$out['subtitle'] = $title.' - '.cot::$L['personal_employer_info'].' - '.cot::$L['personal'];


        $tpl = cot_tplfile(array('personal', 'employer'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(personal_model_EmplProfile::generateTags($employer, 'EMPLOYER_'));

        $t->assign(cot_generate_usertags($urr, 'EMPLOYER_USER_'));

        $canEdit = cot::$usr['isadmin'] || $uid = cot::$usr['id'];
        if($canEdit){
            $vUrlParams = array('m' => 'user', 'a'=>'vacancyEdit');
            if($uid != cot::$usr['id']){
                $vUrlParams['uid'] = $uid;
            }
        }
        $t->assign(array(
            'EMPLOYER_CAN_EDIT' => ($canEdit) ? 1 : 0,
            'EMPLOYER_ID' => ($employer->id > 0) ? $employer->id : 0,
            'ADD_VACANCY_URL' => ($canEdit) ? cot_url('personal', $vUrlParams) : '',

            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),
            'USER_GENDER_RAW' => $urr['user_gender'],
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }
}