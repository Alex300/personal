<?php
defined('COT_CODE') or die('Wrong URL.');

if(!function_exists('cot_user_data')) require_once cot_incfile('users', 'module');

/**
 * Personal User Controller class for the Personal module
 *
 * @package Personal
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
class UserController
{
    /**
     * @return string
     */
    public function indexAction()
    {
        // Error page
        cot_die_message(404);
        exit;
    }

    public function vacancyAction(){
        global $usr, $db_users;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('vuz', 'a');
        cot_block($usr['auth_read']);

        Resources::linkFile(cot::$cfg['modules_dir'].'/personal/js/jquery.smartDialog.js');

        $title = cot::$L['personal_my_vacancies'];

        $uid = cot_import('uid', 'G', 'INT');  // user ID or 0
        if($uid === null) $uid = $usr['id'];

        $urr = cot_user_data($uid);

        $crumbs = array(
            array(cot_url('users', array('m' => 'details')), cot::$L['personal_mypage']),
            $title,
        );

        $userName = cot_user_full_name($urr);

        cot::$out['desc'] = $title .' - '.$userName;
        cot::$out['subtitle'] = $title.' - '.$userName;

        $condition = array(
            array('user_id', $uid),
        );

        // Фильтры
        $filter = cot_import('f', 'G', 'ARR');
//        $filter['title'] = cot_import($filter['title'], 'D', 'TXT');
//        if(!empty($filter['title'])){
//            $urlParams["f[title]"] = $filter['title'];
//            $condition[] = array('st_title', '*'.$filter['title'].'*');
//        }


        // Пагинация
        $maxrowsperpage = cot::$cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
        if($pg > 1) cot::$out['subtitle'] .= cot_rc('code_title_page_num', array('num' => $pg));

        $vacancies = personal_model_Vacancy::findByCondition($condition, $maxrowsperpage, $d, array(
            array('active', 'DESC'), array('hot', 'DESC'), array('sort', 'DESC'), array('activated', 'desc')
        ));
        $totallines = personal_model_Vacancy::count($condition);

        $submitNew = '';
        if(($usr['auth_write'] && $uid == $usr['id']) || $usr['isadmin']){
            $tmp = array('m'=>'user','a'=>'vacancyEdit');
            if($uid != $usr['id']) $tmp['uid'] = $uid;
            $submitNew = cot_url('personal', $tmp);
        }
        $urlParams = array('m' => 'user', 'a' => 'vacancy');
        $pagenav = cot_pagenav('personal', $urlParams, $d, $totallines, $maxrowsperpage);

        $tpl = cot_tplfile(array('personal', 'user', 'vacancy', 'list'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(cot_generate_usertags($urr, 'USER_'));

        if(!empty($vacancies)){
            $i = 1;
            foreach($vacancies as $vacancyRow){
                $t->assign(personal_model_Vacancy::generateTags($vacancyRow, 'VACANCY_ROW_'));
                $t->assign(array(
                    'VACANCY_ROW_NUM' => $i
                ));
                $t->parse('MAIN.VACANCY_ROW');
                $i++;
            }
        }else{
            $t->parse('MAIN.EMPTY');
        }

        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),
            'LIST_SUBMITNEW_URL' => $submitNew,
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

    public function vacancyEditAction(){
        global $usr, $db_users;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('personal', 'a');
        cot_block($usr['id'] > 0);
        cot_block($usr['auth_write']);
        cot_block($usr['auth_read']);

        $vid = cot_import('vid', 'G', 'INT');
        $a = cot_import('a', 'G', 'ALP');
        $uid = cot_import('uid', 'G', 'INT');  // user ID or 0
        if($uid === null) $uid = $usr['id'];

        $profileId = 0;
        $profile = personal_model_EmplProfile::fetchOne(array(
            array('user_id', $uid),
            array('locked', 0),
        ), array(
            array('is_default', 'DESC')
        ));
        if(!empty($profile)) $profileId = $profile->id;

        $urr = cot_user_data($uid);

        if ($vid) {
            $vacancy = personal_model_Vacancy::getById($vid);
            $uid  = $vacancy->user_id;
            if(!$usr['isadmin'] && $uid != $usr['id']) cot_die_message(403);
            //$title = cot::$L['personal_vacancy_edit'];
            $title = htmlspecialchars($vacancy->title." [".cot::$L['Edit']."]");
        } else {
            $vacancy = new personal_model_Vacancy();
            $title = cot::$L['personal_vacancy_add'].' - ';
            if(!empty($profileId)){
                $title .= $profile->title;
            }else{
                $title .= cot_user_full_name($urr);
            }
        }

        $crumbs = array(
            array(cot_url('users', array('m' => 'details')), cot::$L['personal_mypage']),
            array(cot_url('personal', array('m' => 'user', 'a'=>'vacancy')), cot::$L['personal_my_vacancies']),
            $title,
        );

        $userName = cot_user_full_name($urr);

        cot::$out['desc'] = $title .' - '.$userName;
        cot::$out['subtitle'] = $title.' - '.$userName;


        $staffArr = array();
        $staffs = personal_model_Staff::findByCondition();
        if(!empty($staffs)){
            foreach($staffs as $staffRow){
                $staffArr[$staffRow->id] = $staffRow->title;
            }
        }

        $eduArr = array();
        $edus = personal_model_EducationLevel::findByCondition();
        if(!empty($edus)){
            foreach($edus as $eduRow){
                $eduArr[$eduRow->id] = $eduRow->title;
            }
        }

        // Предзаполнение данными
        if(empty($vacancy->profile)) $vacancy->profile   = $profileId;
        if(empty($vacancy->user_id)) $vacancy->user_id = $uid;

        $act = cot_import('act', 'P', 'ALP');
        if($act == 'save'){
            $oldVacData = $vacancy->toArray();

            $today = getdate(cot::$sys['now']);
            $makeActive = cot_import('makeactive', 'P', 'BOL');

            unset($_POST['id'], $_POST['user_id'], $_POST['profile'], $_POST['active_to'], $_POST['hot_to'],
                  $_POST['makeactive']);
            $vacancy->setData($_POST);

            cot_check(mb_strlen($vacancy->title) < 2, cot::$L['personal_titletooshort'], 'title');
            cot_check(empty($vacancy->city), cot::$L['personal_city_required'], 'city');
            cot_check(empty($vacancy->category), cot::$L['personal_category_required'], 'category');
            cot_check(empty($vacancy->staff), cot::$L['personal_staff_required'], 'staff');
            cot_check(empty($vacancy->text), cot::$L['personal_vacancy_text_required'], 'text');
            if(!empty($vacancy->vemail)){
                cot_check(!cot_check_email($vacancy->vemail), cot::$L['aut_emailtooshort'], 'email');
            }
            cot_check((empty($vacancy->phone)) , cot::$L['personal_phone_required'], 'phone');

            if(!$makeActive){
                // Отключение вакансии
                $vacancy->deactivate();
            }else{
                // Включить
                if(empty($oldVacData['active_to']) || strtotime($oldVacData['active_to']) <= cot::$sys['now']){
                    if($vacancy->canBeActivated()){
                        $active_to = mktime($today['hours'], $today['minutes'], $today['seconds'],
                            $today['mon'] + 1, $today['mday'], $today['year']);
                        $vacancy->activate($active_to);
                    }else{
                        cot_error(cot::$L['personal_vacancy_on_error']);
                    }
                }
            }

            if(!cot_error_found()){
                $vacancy->save();
                cot_message(cot::$L['personal_vacancy_saved']);
                if($vacancy->id > 0){
                    cot_redirect(cot_url('personal',array('a'=>'vacancy', 'id'=>$vacancy->id), '', 'true'));
                }else{
                    cot_redirect(cot_url('personal',array('m'=>'user', 'a'=>'vacancy'), '', 'true'));
                }
            }
        }

        $formAction = array('m'=>'user', 'a'=>'vacancyEdit');
        if($vacancy->id > 0){
            $formAction['vid'] = $vacancy->id;
        }
        if($uid != $usr['id']){
            $formAction['uid'] = $uid;
        }
        $formAction = cot_url('personal', $formAction);

        $phone = '';
        if(!empty($vacancy->profile)){
            $phone = $vacancy->profile->phone;
        }elseif(isset($urr['user_phone']) && !empty($urr['user_phone'])){
            $phone = $urr['user_phone'];
        }

        $email = '';
        if(!empty($vacancy->profile)){
            $email = $vacancy->profile->email;
        }else{
            $email = $urr['user_email'];
        }


        $tpl = cot_tplfile(array('personal', 'user', 'vacancy', 'edit'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(cot_generate_usertags($urr, 'USER_'));
        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),

            'FORM_ACTION' => $formAction,
            'FORM_HIDDEN' => cot_inputbox('hidden', 'act', 'save'),
            'FORM_TITLE' => cot_inputbox('text', 'title', $vacancy->title),
            'FORM_CITY' => rec_select2_city('city', $vacancy->city, false),
            'FORM_DISTRICT' => cot_inputbox('text', 'district', $vacancy->district),
            'FORM_CATEGORY' =>  personal_select_tree('category', $vacancy->category, 'personal_model_Category'),
            'FORM_STAFF' => cot_checklistbox($vacancy->staff, 'staff', array_keys($staffArr), array_values($staffArr),
                    '', "\n", false),
            'FORM_EXPERIENCE' => cot_selectbox($vacancy->experience, 'experience', array_keys(cot::$L['personal_vacancy_experiences']),
                    array_values(cot::$L['personal_vacancy_experiences']), true, array('class'=>'form-control select2')),
            'FORM_EDUCATION' => cot_selectbox($vacancy->education, 'education', array_keys($eduArr),
                array_values($eduArr), true, array('class'=>'form-control select2')),
            'FORM_SALARY' => cot_inputbox('text', 'salary', $vacancy->salary,  array('class'=>'form-control',
                'placeholder'=> cot::$L['personal_negotiated'])) ,
            'FORM_TEXT' => cot_textarea('text', $vacancy->text, 8, 120, '', 'input_textarea_editor'),
            'FORM_SKILLS' => cot_textarea('skills', $vacancy->skills, 8, 120, '', 'input_textarea_editor'),
            'FORM_CONTACT_FACE' => cot_inputbox('text', 'contact_face', $vacancy->vcontact_face, array('class'=>'form-control',
                'placeholder'=> $userName)),
            'FORM_PHONE' => cot_inputbox('text', 'phone', $vacancy->vphone, array('class'=>'form-control',
                'placeholder'=>$phone)),
            'FORM_EMAIL' => cot_inputbox('text', 'email', $vacancy->vemail, array('class'=>'form-control',
                'placeholder'=>$email)),
            'FORM_EMPLOYMENT' => cot_checklistbox($vacancy->employment, 'employment', array_keys(cot::$L['personal_employment_levels']),
                array_values(cot::$L['personal_employment_levels']), '', "\n", false),
            'FORM_SCHEDULE' => cot_checklistbox($vacancy->schedule, 'schedule', array_keys(cot::$L['personal_schedule_levels']),
                array_values(cot::$L['personal_schedule_levels']), '', "\n", false),
            'FORM_ACTIVATE' => cot_checkbox($vacancy->active, 'makeactive', cot::$L['personal_vacancy_on']),

            'EMPLOYER_EMAIL' => $email,
            'EMPLOYER_PHONE' => $phone,
            'VACANCY_ID' => ($vacancy->id > 0) ? $vacancy->id : 0,
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }

    public function ajxVacancyEditAction(){
        global $usr;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('vuz', 'a');
        cot_block($usr['id'] > 0);
        cot_block($usr['auth_write']);
        cot_block($usr['auth_read']);

        $ret = array('error' => '', 'act' => '');

        $vid = cot_import('vid', 'P', 'INT');
        if (!$vid) {
            $ret['error'] = cot::$L['personal_vacancy_not_found'];
            echo json_encode($ret);
            exit;
        }
        $vacancy = personal_model_Vacancy::getById($vid);
        if (!$vacancy) {
            $ret['error'] = cot::$L['personal_vacancy_not_found'];
            echo json_encode($ret);
            exit;
        }

        if(!$usr['isadmin'] && ($vacancy->user_id != $usr['id'])){
            $ret['error'] = cot::$L['personal_vacancy_not_found'];
            echo json_encode($ret);
            exit;
        }

        $today = getdate(cot::$sys['now']);
        $act = cot_import('act', 'P', 'ALP');
        switch ($act) {
            case 'deactivate':
                $vacancy->deactivate();
                $vacancy->save();
                cot_message(sprintf(cot::$L['personal_vacancy_x_off'], $vacancy->title));
                $ret['act'] = 'reload';
                break;

            case 'activate':
                if(empty($vacancy->active_to) || strtotime($vacancy->active_to) <= cot::$sys['now']){
                    if($vacancy->canBeActivated()){
                        $active_to = mktime($today['hours'], $today['minutes'], $today['seconds'],
                            $today['mon'] + 1, $today['mday'], $today['year']);
                        $vacancy->activate($active_to);
                        $vacancy->save();
                        cot_message(sprintf(cot::$L['personal_vacancy_x_on'], $vacancy->title, cot_date('date_text', strtotime($vacancy->active_to))));
                        $ret['act'] = 'reload';
                    }else{
                        $ret['error'] = 'Не удалось активировать вакансию';
                        echo json_encode($ret);
                        exit;
                    }
                }
                break;

            case 'makehot':
                if($vacancy->active){
                    if(empty($vacancy->hot_to) || strtotime($vacancy->hot_to) <= cot::$sys['now']){
                        if($vacancy->canBeHot()){
                            $hot_to = mktime($today['hours'], $today['minutes'], $today['seconds'],
                                $today['mon'] + 1, $today['mday'], $today['year']);
                            $vacancy->makeHot($hot_to);
                            $vacancy->save();
                            cot_message("Вакансия «{$vacancy->title}» горячая до ".cot_date('date_text', strtotime($vacancy->hot_to)));
                            $ret['act'] = 'reload';
                        }else{
                            $ret['error'] = 'Не удалось сделать вакансию горяче';
                            echo json_encode($ret);
                            exit;
                        }
                    }
                }else{
                    $ret['error'] = 'Только активную вакансию можно сделать горячей';
                    echo json_encode($ret);
                    exit;
                }
                break;

            case 'makeunhot':
                $vacancy->makeUnHot();
                $vacancy->save();
                cot_message("Статус горячей с вакансии «{$vacancy->title}» снят");
                $ret['act'] = 'reload';
                break;

            case 'up':
                $vacancy->sort = date('Y-m-d H:i:s', cot::$sys['now']);
                $vacancy->save();
                cot_message(sprintf(cot::$L['personal_vacancy_x_up'], $vacancy->title));
                $ret['act'] = 'reload';
                break;

            case 'delete':
                $title = $vacancy->title;
                $vacancy->delete();
                cot_message(sprintf(cot::$L['personal_vacancy_x_deleted'], $title));
                $ret['act'] = 'reload';
                break;

            default:
                $ret['error'] = 'Неизвестная задача';
                echo json_encode($ret);
                exit;
        }

        echo json_encode($ret);
        exit;

    }

    /**
     * todo alias через модель
     * @return string
     */
    public function profileEditAction(){
        global $usr, $db_users;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('vuz', 'a');
        cot_block($usr['id'] > 0);
        cot_block($usr['auth_write']);
        cot_block($usr['auth_read']);

        $title = cot::$L['personal_employer_info'];

        $uid = cot_import('uid', 'G', 'INT');  // user ID or 0
        if($uid === null) $uid = $usr['id'];

        if($uid != $usr['id'] && !$usr['isadmin']){
            cot_die_message(404);
            exit;
        }

        $sql = cot::$db->query("SELECT * FROM $db_users WHERE user_id = ? LIMIT 1", $uid);
        $urr = $sql->fetch();

        $crumbs = array(
            array(cot_url('users', array('m' => 'details')), cot::$L['personal_mypage']),
            $title,
        );

        $userName = cot_user_full_name($urr);

        cot::$out['desc'] = $title .' - '.$userName;
        cot::$out['subtitle'] = $title.' - '.$userName;

        $profile = personal_model_EmplProfile::fetchOne(array(array('user_id', $uid)));
        if(!$profile){
            $profile = new personal_model_EmplProfile();
            $profile->user_id = $uid;
            $profile->is_default = 1;
        }
        $act = cot_import('act', 'P', 'ALP');
        if($act == 'save'){
            $item = array(
                'title' => cot_import('title', 'P', 'TXT'),
                'type' => cot_import('type', 'P', 'INT'),
                'text' => cot_import('text', 'P', 'HTM'),
                'site' => cot_import('site', 'P', 'TXT'),
                'address' => cot_import('address', 'P', 'TXT'),
                'pphone' => cot_import('phone', 'P', 'TXT'),
                'pemail' => cot_import('email', 'P', 'TXT'),
            );
            $profile->setData($item);
            cot_check(mb_strlen($profile->title) < 2, cot::$L['personal_titletooshort'], 'title');
            if(!empty($profile->pemail)){
                cot_check(!cot_check_email($profile->pemail), cot::$L['aut_emailtooshort'], 'email');
            }
            cot_check((empty($profile->phone)) , cot::$L['personal_phone_required'], 'phone');

            if(!cot_error_found()){
                $profile->save();
                cot_message(cot::$L['personal_profile_saved']);
                cot_redirect(cot_url('personal',array('m'=>'user', 'a'=>'profileEdit'), '', 'true'));
            }
        }

        $tpl = cot_tplfile(array('personal', 'user', 'profile', 'edit'), 'module');
        $t = new XTemplate($tpl);

        $formAction = cot_url('personal', array('m'=>'user', 'a'=>'profileEdit'));
        if($uid != $usr['id']){
            $formAction = cot_url('personal', array('m'=>'user', 'a'=>'profileEdit', 'uid'=>$uid));
        }
        $types = array(
            personal_model_EmplProfile::TYPE_STRAIGHT => cot::$L['personal_profile_type_0'],
            personal_model_EmplProfile::TYPE_AGENCY => cot::$L['personal_profile_type_1'],
        );

        $phone = '';
        if(isset($urr['user_phone']) && !empty($urr['user_phone'])) $phone = $urr['user_phone'];
//        $t->assign(personal_model_EmplProfile::generateTags($profile, 'PROFILE_'));
        $t->assign(cot_generate_usertags($urr, 'USER_'));
        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),
            'USER_EMAIL' => $urr['user_email'],
            'FORM_ACTION' => $formAction,
            'FORM_HIDDEN' => cot_inputbox('hidden', 'act', 'save'),
            'FORM_TITLE' => cot_inputbox('text', 'title', $profile->title),
            'FORM_TYPE' => cot_selectbox($profile->type, 'type', array_keys($types), array_values($types), false),
            'FORM_TEXT' => cot_textarea('text', $profile->text, 8, 120, '', 'input_textarea_editor'),
            'FORM_SITE' => cot_inputbox('text', 'site', $profile->site, array('class'=>'form-control',
                'placeholder'=> cot::$L['personal_example'].': http://mysite.com')),
            'FORM_ADDRESS' => cot_inputbox('text', 'address', $profile->address),
            'FORM_PHONE' => cot_inputbox('text', 'phone', $profile->pphone, array('class'=>'form-control',
                'placeholder'=>$phone)),
            'FORM_EMAIL' => cot_inputbox('text', 'email', $profile->pemail, array('class'=>'form-control',
                'placeholder'=>$urr['user_email'])),


            'PROFILE_ID' => ($profile->id > 0) ? $profile->id : 0,
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }

    public function resumeAction()
    {
        global $usr;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('vuz', 'a');
        cot_block($usr['auth_read']);

        Resources::linkFile(cot::$cfg['modules_dir'].'/personal/js/jquery.smartDialog.js');

        $title = cot::$L['personal_my_resumes'];

        $uid = cot_import('uid', 'G', 'INT');  // user ID or 0
        if($uid === null) $uid = $usr['id'];

        $urr = cot_user_data($uid);

        $crumbs = array(
            array(cot_url('users', array('m' => 'details')), cot::$L['personal_mypage']),
            $title,
        );

        $userName = cot_user_full_name($urr);

        cot::$out['desc'] = $title .' - '.$userName;
        cot::$out['subtitle'] = $title.' - '.$userName;

        $condition = array(
            array('user_id', $uid),
        );

        // Фильтры
        $filter = cot_import('f', 'G', 'ARR');
//        $filter['title'] = cot_import($filter['title'], 'D', 'TXT');
//        if(!empty($filter['title'])){
//            $urlParams["f[title]"] = $filter['title'];
//            $condition[] = array('st_title', '*'.$filter['title'].'*');
//        }


        // Пагинация
        $maxrowsperpage = cot::$cfg['maxrowsperpage'];
        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
        if($pg > 1) cot::$out['subtitle'] .= cot_rc('code_title_page_num', array('num' => $pg));

        $resumes = personal_model_Resume::findByCondition($condition, $maxrowsperpage, $d, array(
            array('active', 'DESC'), array('hot', 'DESC'), array('sort', 'DESC'), array('activated', 'desc')
        ));
        $totallines = personal_model_Resume::count($condition);

        $submitNew = '';
        if(($usr['auth_write'] && $uid == $usr['id']) || $usr['isadmin']){
            $tmp = array('m'=>'user','a'=>'resumeEdit');
            if($uid != $usr['id']) $tmp['uid'] = $uid;
            $submitNew = cot_url('personal', $tmp);
        }
        $urlParams = array('m' => 'user', 'a' => 'resume');
        $pagenav = cot_pagenav('vuz', $urlParams, $d, $totallines, $maxrowsperpage);

        $tpl = cot_tplfile(array('personal', 'user', 'resume', 'list'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(cot_generate_usertags($urr, 'USER_'));

        if(!empty($resumes)){
            $i = 1;
            foreach($resumes as $resumeRow){
                $t->assign(personal_model_Resume::generateTags($resumeRow, 'RESUME_ROW_'));
                $t->assign(array(
                    'RESUME_ROW_NUM' => $i,
                    'RESUME_ROW_ODDEVEN' => cot_build_oddeven($i),
                    'RESUME_ROW_RAW'     => $resumeRow
                ));
                $t->parse('MAIN.RESUME_ROW');
                $i++;
            }
        }else{
            $t->parse('MAIN.EMPTY');
        }

        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),
            'LIST_SUBMITNEW_URL' => $submitNew,
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

    public function resumeViewAction()
    {
        global $usr, $db_personal_languages, $db_personal_resumes_lang_levels, $db_personal_education_levels;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('personal', 'a');
        cot_block($usr['id'] > 0);
        cot_block($usr['auth_read']);

        $rid = cot_import('rid', 'G', 'INT');
        if(!$rid) cot_die_message(404);

        cot::$env['location'] = 'personal.resume_preview';

        Resources::linkFileFooter('modules/personal/js/personal.resume.js');

        $a = cot_import('a', 'G', 'ALP');
        $uid = cot_import('uid', 'G', 'INT');  // user ID or 0
        if($uid === null) $uid = $usr['id'];

        $urr = cot_user_data($uid);
        $resume = personal_model_Resume::getById($rid);
        if(!$resume) cot_die_message(404);

        $uid  = $resume->user_id;
        if(!$usr['isadmin'] && $uid != $usr['id']) cot_die_message(403);
        $title = htmlspecialchars($resume->title);

        $crumbs = array(
            array(cot_url('users', array('m' => 'details')), cot::$L['personal_mypage']),
            array(cot_url('personal', array('m' => 'user', 'a'=>'resume')), cot::$L['personal_my_resumes']),
            $resume->title
        );

        $userName = cot_user_full_name($urr);

        cot::$out['desc'] = $title .' - '.$userName;
        cot::$out['subtitle'] = $title.' - '.$userName;

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

        // WHERE NOT IN (языки, уже привязанные к резюме)
        $langs = cot::$db->query("SELECT id, title FROM $db_personal_languages
            WHERE id NOT IN (SELECT lang_id FROM $db_personal_resumes_lang_levels WHERE resume_id=".$resume->id.")
            ORDER BY  sort ASC, title DESC")->fetchAll(PDO::FETCH_KEY_PAIR);

        // Уровни образования
        $eduLevels = cot::$db->query("SELECT id, title FROM $db_personal_education_levels ORDER BY `order` ASC")->fetchAll(PDO::FETCH_KEY_PAIR);
        if(empty($eduLevels)) $eduLevels = array();

        $years = range(date('Y', cot::$sys['now']) - 60, date('Y', cot::$sys['now']) + 10, 1);

        $tpl = cot_tplfile(array('personal', 'resume', 'preview'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(personal_model_Resume::generateTags($resume, 'RESUME_'));

        $canEdit = (($usr['auth_write'] && $usr['id'] == $resume->user_id) || $usr['isadmin']) ? 1 : 0;
        $t->assign(cot_generate_usertags($urr, 'USER_'));
        $t->assign(array(
            'RESUME_CAN_EDIT' => $canEdit,
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

        if($canEdit){
            $t->assign(array(
                'ADD_LANG_LANG' => cot_selectbox(0, 'add_lang_lang', array_keys($langs), array_values($langs), false,
                    array('class'=>'form-control select2', 'id'=>'add_lang_lang')),
                'ADD_LANG_LEVEL' => cot_selectbox(0, 'add_lang_level', array_keys(cot::$L['personal_lang_levels']),
                    array_values(cot::$L['personal_lang_levels']), false, array('class'=>'form-control select2', 'id'=>'add_lang_level')),

                'ADD_EDU_LEVEL' => cot_selectbox(0, 'add_edu_level', array_keys($eduLevels),  array_values($eduLevels), false,
                    array('class'=>'form-control select2', 'id'=>'add_edu_level')),
                'ADD_EDU_TITLE' => cot_inputbox('text', 'add_edu_title', '', array('class'=>'form-control', 'id'=>'add_edu_title')),
                'ADD_EDU_FACULTY' => cot_inputbox('text', 'add_edu_faculty', '', array('class'=>'form-control', 'id'=>'add_edu_faculty')),
                'ADD_EDU_SPECIALTY' => cot_inputbox('text', 'add_edu_specialty', '', array('class'=>'form-control', 'id'=>'add_edu_specialty')),
                'ADD_EDU_YEAR' => cot_selectbox(date('Y', cot::$sys['now']), 'add_edu_year', $years,  $years, false,
                    array('class'=>'form-control select2', 'id'=>'add_edu_year')),

                'ADD_RECOMMEND_NAME' => cot_inputbox('text', 'add_recommend_name', '', array('class'=>'form-control',
                    'id'=>'add_recommend_name')),
                'ADD_RECOMMEND_POSITION' => cot_inputbox('text', 'add_recommend_position', '', array('class'=>'form-control',
                    'id'=>'add_recommend_position')),
                'ADD_RECOMMEND_ORGANIZATION' => cot_inputbox('text', 'add_recommend_organization', '', array('class'=>'form-control',
                    'id'=>'add_recommend_organization')),
                'ADD_RECOMMEND_PHONE' => cot_inputbox('text', 'add_recommend_phone', '', array('class'=>'form-control',
                    'id'=>'add_recommend_phone')),

                'ADD_EXPERIENCE_ORGANIZATION' => cot_inputbox('text', 'add_experience_organization', '', array('class'=>'form-control',
                    'id'=>'add_experience_organization')),
                'ADD_EXPERIENCE_CITY' => rec_select2_city('add_experience_city', 0, false,
                    array('id'=>'add_experience_city')),
                'ADD_EXPERIENCE_WEBSITE' => cot_inputbox('text', 'add_experience_website', '', array('class'=>'form-control',
                    'id'=>'add_experience_website')),
                'ADD_EXPERIENCE_POSITION' => cot_inputbox('text', 'add_experience_position', '', array('class'=>'form-control',
                    'id'=>'add_experience_position')),
                'ADD_EXPERIENCE_BEGIN' => cot_selectbox_date(0, 'short', 'add_experience_begin'),
                'ADD_EXPERIENCE_END' => cot_selectbox_date(0, 'short', 'add_experience_end'),
                'ADD_EXPERIENCE_FOR_NOW' => cot_checkbox(0, 'add_experience_for_now', cot::$L['personal_resume_for_now'],
                    array('id'=>'add_experience_for_now')),
                'ADD_EXPERIENCE_ACHIEVEMENTS' => cot_textarea('add_experience_achievements', '', 3, 10,
                    array('style'=>'width: 100%', 'id'=>'add_experience_achievements'), 'input_textarea_minieditor'),

            ));
        }

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }

    public function resumeEditAction(){
        global $usr;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('personal', 'a');
        cot_block($usr['id'] > 0);
        cot_block($usr['auth_write']);
        cot_block($usr['auth_read']);

        $rid = cot_import('rid', 'G', 'INT');
        $a = cot_import('a', 'G', 'ALP');
        $uid = cot_import('uid', 'G', 'INT');  // user ID or 0
        if($uid === null) $uid = $usr['id'];

        $urr = cot_user_data($uid);
        if ($rid) {
            $resume = personal_model_Resume::getById($rid);
            if(!$rid) cot_die_message(404);

            $uid  = $resume->user_id;
            if(!$usr['isadmin'] && $uid != $usr['id']) cot_die_message(403);
            //$title = cot::$L['personal_vacancy_edit'];
            $title = htmlspecialchars($resume->title." [".cot::$L['Edit']."]");
        } else {
            $resume = new personal_model_Resume();
            $resume->user_id = $uid;
            $title = cot::$L['personal_resume_add'];
        }


        $crumbs = array(
            array(cot_url('users', array('m' => 'details')), cot::$L['personal_mypage']),
            array(cot_url('personal', array('m' => 'user', 'a'=>'resume')), cot::$L['personal_my_resumes']),
        );
        if($resume->id > 0){
            $crumbs[] = array(cot_url('personal', array('m' => 'user', 'a'=>'resumeView', 'rid'=>$resume->id)),
                $resume->title);
        }
        $crumbs[] = ($rid > 0) ? cot::$L['Edit'] : cot::$L['personal_resume_add'];

        $userName = cot_user_full_name($urr);

        cot::$out['desc'] = $title .' - '.$userName;
        cot::$out['subtitle'] = $title.' - '.$userName;

        $staffArr = array();
        $staffs = personal_model_Staff::findByCondition();
        if(!empty($staffs)){
            foreach($staffs as $staffRow){
                $staffArr[$staffRow->id] = $staffRow->title;
            }
        }

        $eduArr = array();
        $edus = personal_model_EducationLevel::findByCondition();
        if(!empty($edus)){
            foreach($edus as $eduRow){
                $eduArr[$eduRow->id] = $eduRow->title;
            }
        }

        // Предзаполнение данными
        if(empty($resume->user_id)) $resume->user_id = $uid;

        $act = cot_import('act', 'P', 'ALP');
        if($act == 'save'){

            unset($_POST['id'], $_POST['user_id'], $_POST['hot_to'], $_POST['hot'], $_POST['makeactive']);
            if(!empty($_POST['leaving'])){
                $_POST['leaving'] = explode(',', trim($_POST['leaving']));
                if(!empty($_POST['leaving'])) $_POST['leaving'] = array_map('intval', $_POST['leaving']);
            }
            if(!empty($_POST['salary'])){
                $_POST['salary'] = intval(str_replace(' ', '', $_POST['salary']));
            }
            $resume->setData($_POST);

            $resume->skills = trim($resume->skills);
            cot_check(mb_strlen($resume->title) < 2, cot::$L['personal_titletooshort'], 'title');
            cot_check(empty($resume->city), cot::$L['personal_city_required'], 'city');
            cot_check(empty($resume->category), cot::$L['personal_category_required'], 'category');
            cot_check(empty($resume->staff), cot::$L['personal_staff_required'], 'staff');
            cot_check(empty($resume->education_level), cot::$L['personal_education_level_required'], 'education_level');
            cot_check(empty($resume->skills), cot::$L['personal_resume_skills_required'], 'skills');

            if($resume->id == 0) $resume->activate();

            if(!cot_error_found()){
                $resume->save();
                cot_message(cot::$L['personal_resume_saved']);
                cot_redirect(cot_url('personal',array('m'=>'user', 'a'=>'resumeView', 'rid'=>$resume->id), '', 'true'));
            }
        }

        $formAction = array('m'=>'user', 'a'=>'resumeEdit');
        if($resume->id > 0){
            $formAction['rid'] = $resume->id;
        }
        if($uid != $usr['id']){
            $formAction['uid'] = $uid;
        }
        $formAction = cot_url('personal', $formAction);

        $phone = '';
        if(isset($urr['user_phone']) && !empty($urr['user_phone'])){
            $phone = $urr['user_phone'];
        }

        $email = '';
        if($urr['user_hideemail'] == 1){
            $email = $urr['user_email'];
        }


        $tpl = cot_tplfile(array('personal', 'user', 'resume', 'edit'), 'module');
        $t = new XTemplate($tpl);

        $t->assign(cot_generate_usertags($urr, 'USER_'));
        $t->assign(array(
            'PAGE_TITLE'  =>  $title,
            'BREADCRUMBS' =>  cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']),

            'FORM_ACTION' => $formAction,
            'FORM_HIDDEN' => cot_inputbox('hidden', 'act', 'save'),
            'FORM_TITLE' => cot_inputbox('text', 'title', $resume->title),
            'FORM_SALARY' => cot_inputbox('text', 'salary', ($resume->salary > 0 ? $resume->salary : ''),
                                array('class'=>'form-control', 'placeholder'=> cot::$L['personal_negotiated'])) ,
            'FORM_CITY' => rec_select2_city('city', $resume->city, false),
            'FORM_DISTRICT' => cot_inputbox('text', 'district', $resume->district),
            'FORM_LEAVING' => rec_select2_city('leaving', $resume->leaving, true, array('multiple'=>'multiple')),
            'FORM_CATEGORY' =>  personal_select_tree('category', $resume->category, 'personal_model_Category'),
            'FORM_STAFF' => cot_checklistbox($resume->staff, 'staff', array_keys($staffArr), array_values($staffArr),
                '', "\n", false),
            'FORM_EDUCATION' => cot_selectbox($resume->education_level, 'education_level', array_keys($eduArr),
                array_values($eduArr), true, array('class'=>'form-control select2')),
            'FORM_SKILLS' => cot_textarea('skills', $resume->skills, 8, 120, '', 'input_textarea_editor'),
            'FORM_TEXT' => cot_textarea('text', $resume->text, 8, 120, '', 'input_textarea_editor'),
            'FORM_EMPLOYMENT' => cot_checklistbox($resume->employment, 'employment', array_keys(cot::$L['personal_employment_levels']),
                array_values(cot::$L['personal_employment_levels']), '', "\n", false),
            'FORM_SCHEDULE' => cot_checklistbox($resume->schedule, 'schedule', array_keys(cot::$L['personal_schedule_levels']),
                array_values(cot::$L['personal_schedule_levels']), '', "\n", false),

            /*
            'FORM_CONTACT_FACE' => cot_inputbox('text', 'contact_face', $vacancy->vcontact_face, array('class'=>'form-control',
                'placeholder'=> $userName)),
            'FORM_PHONE' => cot_inputbox('text', 'phone', $vacancy->vphone, array('class'=>'form-control',
                'placeholder'=>$phone)),
            'FORM_EMAIL' => cot_inputbox('text', 'email', $vacancy->vemail, array('class'=>'form-control',
                'placeholder'=>$email)),
            'FORM_ACTIVATE' => cot_checkbox($vacancy->active, 'makeactive', cot::$L['personal_vacancy_on']),

            'EMPLOYER_EMAIL' => $email,
            'EMPLOYER_PHONE' => $phone,
            */

            'RESUME_ID' => ($resume->id > 0) ? $resume->id : 0,
        ));

        // Error and message handling
        cot_display_messages($t);

        $t->parse();
        return  $t->text();
    }

    public function ajxResumeEditAction(){
        global $usr, $db_personal_resumes_lang_levels, $db_personal_resumes_education, $db_personal_education_levels,
               $db_personal_resumes_recommend, $db_personal_resumes_experience;

        list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('vuz', 'a');
        cot_block($usr['id'] > 0);
        cot_block($usr['auth_write']);
        cot_block($usr['auth_read']);

        $ret = array('error' => '', 'act' => '');

        $rid = cot_import('rid', 'P', 'INT');
        if (!$rid) {
            $ret['error'] = cot::$L['personal_resume_not_found'];
            echo json_encode($ret);
            exit;
        }
        $item = personal_model_Resume::getById($rid);
        if (!$item) {
            $ret['error'] = cot::$L['personal_resume_not_found'];
            echo json_encode($ret);
            exit;
        }

        if(!$usr['isadmin'] && ($item->user_id != $usr['id'])){
            $ret['error'] = cot::$L['personal_resume_not_found'];
            echo json_encode($ret);
            exit;
        }

        $today = getdate(cot::$sys['now']);
        $act = cot_import('act', 'P', 'ALP');

        switch ($act) {
            case 'add_lang':
                $ret['result'] = '';
                $lang = cot_import('lang', 'P', 'INT');
                $level = cot_import('level', 'P', 'INT');

                if(!$lang || !$level){
                    $ret['error'] = cot::$L['personal_language_not_found'];
                    echo json_encode($ret);
                    exit;
                }

                // Проверка на наличие существующего
                $lvl = cot::$db->query("SELECT * FROM $db_personal_resumes_lang_levels
                    WHERE resume_id=".$item->id." AND lang_id = $lang")->fetch();

                if($lvl){
                    if($lvl['level_id'] != $level){
                        cot::$db->update($db_personal_resumes_lang_levels, array('level_id'=>$level),
                         "resume_id=".$item->id." AND lang_id = $lang");
                        $ret['result'] = 'updated';
                    }
                }else{
                    $data = array(
                        'resume_id' => $item->id,
                        'lang_id'   => $lang,
                        'level_id'  => $level,
                    );
                    cot::$db->insert($db_personal_resumes_lang_levels, $data);
                    $ret['lang_lvl_id'] = cot::$db->lastInsertId();
                    $ret['result'] = 'added';
                }

                break;

            case 'delete_lang':
                $lang_lvl_id = cot_import('lang_lvl_id', 'P', 'INT');
                if(!$lang_lvl_id){
                    $ret['error'] = cot::$L['personal_language_not_found'];
                    echo json_encode($ret);
                    exit;
                }
                $res = cot::$db->delete($db_personal_resumes_lang_levels, "id={$lang_lvl_id} AND resume_id={$rid}");
                if($res == 0){
                    $ret['error'] = cot::$L['personal_language_not_found'];
                    echo json_encode($ret);
                    exit;
                }
                break;

            case 'save_edu':
                $eid = cot_import('eid', 'P', 'INT');
                $data = array(
                    'faculty'   => cot_import('add_edu_faculty', 'P', 'TXT'),
                    'level_id'  => cot_import('add_edu_level', 'P', 'INT'),
                    'specialty' => cot_import('add_edu_specialty', 'P', 'TXT'),
                    'title'     => cot_import('add_edu_title', 'P', 'TXT'),
                    'year'      => cot_import('add_edu_year', 'P', 'INT'),
                    'resume_id' => $item->id,
                );

                if(empty($data['title'])) $data['title'] = cot::$L['personal_no_name'];

                if($eid > 0){
                    $tmp = cot::$db->query("SELECT count(*) FROM $db_personal_resumes_education
                        WHERE id={$eid} AND resume_id={$item->id}")->fetchColumn();
                    if($tmp == 0) $eid = 0;
                }

                if($eid > 0){
                    cot::$db->update($db_personal_resumes_education, $data, "resume_id=".$item->id." AND id = $eid");
                    $ret['result'] = 'updated';
                    $ret['eid'] = $eid;
                }else{
                    cot::$db->insert($db_personal_resumes_education, $data);
                    $ret['eid'] = cot::$db->lastInsertId();
//                    $ret['eid'] = 1;
                    $ret['result'] = 'added';
                }
                $ret['level_title'] = cot::$db->query("SELECT title FROM $db_personal_education_levels WHERE id=?",
                    $data['level_id'])->fetchColumn();

                $ret = array_merge($ret, $data);

                break;

            case 'delete_edu':
                $eid = cot_import('eid', 'P', 'INT');
                if(!$eid){
                    $ret['error'] = cot::$L['Noitemsfound'];
                    echo json_encode($ret);
                    exit;
                }
                $res = cot::$db->delete($db_personal_resumes_education, "id={$eid} AND resume_id={$rid}");
                if($res == 0){
                    $ret['error'] = cot::$L['Noitemsfound'];
                    echo json_encode($ret);
                    exit;
                }
                break;


            case 'save_recommend':
                $recommend_id = cot_import('recommend_id', 'P', 'INT');
                $data = array(
                    'name'         => cot_import('add_recommend_name', 'P', 'TXT'),
                    'position'     => cot_import('add_recommend_position', 'P', 'TXT'),
                    'organization' => cot_import('add_recommend_organization', 'P', 'TXT'),
                    'phone'        => cot_import('add_recommend_phone', 'P', 'TXT'),
                    'resume_id' => $item->id,
                );

                if(empty($data['name'])) $data['name'] = cot::$L['personal_no_name'];

                if($recommend_id > 0){
                    $tmp = cot::$db->query("SELECT count(*) FROM $db_personal_resumes_recommend
                        WHERE id={$recommend_id} AND resume_id={$item->id}")->fetchColumn();
                    if($tmp == 0) $recommend_id = 0;
                }

                if($recommend_id > 0){
                    cot::$db->update($db_personal_resumes_recommend, $data, "resume_id=".$item->id." AND id = $recommend_id");
                    $ret['result'] = 'updated';
                    $ret['recommend_id'] = $recommend_id;
                }else{
                    cot::$db->insert($db_personal_resumes_recommend, $data);
                    $ret['recommend_id'] = cot::$db->lastInsertId();
                    $ret['result'] = 'added';
                }
                $data['organization'] = htmlspecialchars($data['organization']);
                $data['name'] = htmlspecialchars($data['name']);
                $data['position'] = htmlspecialchars($data['position']);
                $data['phone'] = htmlspecialchars($data['phone']);

                $ret = array_merge($ret, $data);

                break;

            case 'delete_recommend':
                $recommend_id = cot_import('recommend_id', 'P', 'INT');
                if(!$recommend_id){
                    $ret['error'] = cot::$L['Noitemsfound'];
                    echo json_encode($ret);
                    exit;
                }
                $res = cot::$db->delete($db_personal_resumes_recommend, "id={$recommend_id} AND resume_id={$rid}");
                if($res == 0){
                    $ret['error'] = cot::$L['Noitemsfound'];
                    echo json_encode($ret);
                    exit;
                }
                break;


            case 'save_experience':
                $experience_id = cot_import('experience_id', 'P', 'INT');
                $begin = (int)cot_import_date('add_experience_begin', false);
                $end   = (int)cot_import_date('add_experience_end', false);
                $begin = ($begin > 0) ? $begin : cot::$sys['now'];
                $data = array(
                    'organization'  => cot_import('add_experience_organization', 'P', 'TXT'),
                    'city'          => cot_import('add_experience_city', 'P', 'INT'),
                    'website'       => trim(cot_import('add_experience_website', 'P', 'TXT')),
                    'position'      => cot_import('add_experience_position', 'P', 'TXT'),
                    'begin'         => date('Y-m-d', $begin),
                    'end'           => ($end > 0) ? date('Y-m-d', $end) : null,
                    'for_now'       => cot_import('add_experience_for_now', 'P', 'BOL'),
                    'achievements'  => cot_import('add_experience_achievements', 'P', 'HTM'),
                    'resume_id'     => $item->id,
                );
                if($end == 0) $data['for_now'] = 1;
                if($data['for_now'] == 1){
                    $end = 0;
                    $data['end'] = null;
                }

                if(empty($data['organization'])) $data['organization'] = cot::$L['personal_no_name'];
                if(empty($data['position'])) $data['position'] = cot::$L['personal_no_name'];

                if(!empty($data['website'])){
                    // Если в начале переданной строки не указан протокол
                    if( (mb_strpos($data['website'], "http") !== 0)){
                        $data['website'] = 'http://'.$data['website'];
                    }
                }

                if($experience_id > 0){
                    $tmp = cot::$db->query("SELECT count(*) FROM $db_personal_resumes_experience
                        WHERE id={$experience_id} AND resume_id={$item->id}")->fetchColumn();
                    if($tmp == 0) $experience_id = 0;
                }

                if($experience_id > 0){
                    cot::$db->update($db_personal_resumes_experience, $data, "resume_id=".$item->id." AND id = $experience_id",
                        array(), true);
                    $ret['result'] = 'updated';
                    $ret['experience_id'] = $experience_id;
                }else{
                    cot::$db->insert($db_personal_resumes_experience, $data);
                    $ret['experience_id'] = cot::$db->lastInsertId();
                    $ret['result'] = 'added';
                }

                $ret['city_title'] = '';
                if($data['city'] > 0){
                    $ret['city_title'] = cot_import('add_experience_city_name', 'P', 'TXT');
                    $ret['city_title'] = htmlspecialchars($ret['city_title']);
                }

                $ret['begin_str'] = $ret['begin_date'] = '';
                $ret['begin_stamp'] = $begin;
                if($begin > 0){
                    $ret['begin_str'] = cot_date('F Y', $begin, false);
                    $ret['begin_date'] = cot_date('date_full', $begin, false);
                }
                $ret['end_str'] = $ret['end_date'] = '';
                $ret['end_stamp'] = $end;
                if($end > 0){
                    $ret['end_str'] = cot::$L['personal_to'].' '.cot_date('F Y', $end, false);
                    $ret['end_date'] = cot_date('date_full', $end, false);
                }else{
                    $ret['end_str'] = cot::$L['personal_resume_for_now'];
                }


                // Расчет опыта работы в годах и месяцах

                $data['organization'] = htmlspecialchars($data['organization']);
                $data['website'] = htmlspecialchars($data['website']);
                $data['position'] = htmlspecialchars($data['position']);

                $ret = array_merge($ret, $data);

                break;

            case 'delete_experience':
                $experience_id = cot_import('experience_id', 'P', 'INT');
                if(!$experience_id){
                    $ret['error'] = cot::$L['Noitemsfound'];
                    echo json_encode($ret);
                    exit;
                }
                $res = cot::$db->delete($db_personal_resumes_experience, "id={$experience_id} AND resume_id={$rid}");
                if($res == 0){
                    $ret['error'] = cot::$L['Noitemsfound'];
                    echo json_encode($ret);
                    exit;
                }
                break;

            case 'deactivate':
                $item->deactivate();
                $item->save();
                cot_message(sprintf(cot::$L['personal_resume_x_off'], $item->title));
                $ret['act'] = 'reload';
                break;

            case 'activate':
                $item->activate();
                $item->save();
                cot_message(sprintf(cot::$L['personal_resume_x_on'], $item->title));
                $ret['act'] = 'reload';

                break;

            case 'makehot':
                if($item->active){
                    if(empty($item->hot_to) || strtotime($item->hot_to) <= cot::$sys['now']){
                        if($item->canBeHot()){
                            $hot_to = mktime($today['hours'], $today['minutes'], $today['seconds'],
                                $today['mon'] + 1, $today['mday'], $today['year']);
                            $item->makeHot($hot_to);
                            $item->save();
                            cot_message("Резюме «{$item->title}» горячее до ".cot_date('date_text', strtotime($item->hot_to)));
                            $ret['act'] = 'reload';
                        }else{
                            $ret['error'] = 'Не удалось сделать резюме горячим';
                            echo json_encode($ret);
                            exit;
                        }
                    }
                }else{
                    $ret['error'] = 'Только активное резюме можно сделать горячим';
                    echo json_encode($ret);
                    exit;
                }
                break;

            case 'makeunhot':
                $item->makeUnHot();
                $item->save();
                cot_message("Статус горячего с резюме «{$item->title}» снят");
                $ret['act'] = 'reload';
                break;

            case 'up':
                $item->sort = date('Y-m-d H:i:s', cot::$sys['now']);
                $item->save();
                cot_message(sprintf(cot::$L['personal_resume_x_up'], $item->title));
                $ret['act'] = 'reload';
                break;

            case 'delete':
                $title = $item->title;
                $item->delete();
                cot_message(sprintf(cot::$L['personal_resume_x_deleted'], $title));
                $ret['act'] = 'reload';
                break;

            default:
                $ret['error'] = 'Неизвестная задача';
                echo json_encode($ret);
                exit;
        }

        echo json_encode($ret);
        exit;

    }

}