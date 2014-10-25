<?php

/**
 * Модель Резюме
 *
 * @method static personal_model_Resume getById($pk);
 * @method static personal_model_Resume fetchOne($conditions = array(), $order = '');
 * @method static personal_model_Resume[] find($conditions = array(), $limit = 0, $offset = 0, $order = '');
 *
 *
 * @property int                       $id
 * @property personal_model_Category[] $category
 * @property int                       $user_id             id пользователя владельца
 * @property string                    $title               Заголовок. Специализация соискателя.
 * @property string                    $alias               Транслитерированный заголовок
 * @property string                    $text                Подробное описание резюме
 * @property regioncity_model_City     $city                Город
 * @property string                    $city_name           Кешированное название города
 * @property regioncity_model_City[]   $leaving             Города для перезда
 * @property string                    $district            Район города
 * @property int                       $salary              Уровень зарплаты на которую притендует соискатель
 * @property string                    $remail              Адрес электронной почты
 * @property string                    $rphone              Контактный телефон
 * @property string                    $other_contacts      Дополнение к контактной информации.
 * @property string                    $skills              Профессиональные навыки соискателя.
 * @property personal_model_EducationLevel $education_level     Уровень образования / ученная степень.
 * @property int                       $experience          Опыт работы в месяцах.
 * @property personal_model_Staff[]    $staff               Уровни в штатном расписании
 * @property int                       $status              Статус резюме
 * @property bool                      $active              Активно?
 * @property bool                      $hot                 Горячее?
 * @property string                    $hot_to              Горячее до
 * @property int                       $views               Просмотров
 * @property string                    $activated           Дата время последней активации резюме.
 * @property string                    $deactivated         Дата время последнего отключения резюме.
 * @property string                    $sort                Поле для сортировки
 * @property string                    $deny_unregister     Запретить для незарегистрированных
 * @property string                    $note                Сообщение модератора
 * @property string                    $created    Дата создания
 * @property int                       $created_by Кем создана запись
 * @property string                    $updated    Дата последнего обновления
 * @property int                       $updated_by Кем обновлено последний раз
 *
 * @property array                     $languages       Владение языками
 * @property array                     $education       Образование
 * @property array                     $recommendations Рекомендации
 * @property array                     $experiences     Места работы
 * @property array                     $employment      Занятость
 * @property array                     $schedule        График работы
 *
 * @property string                    $email      Адрес электронной почты,
 *                                                 если не указано, то используется email пользователя
 * @property string                    $phone      Контактный телефон,
 *                                                 если не указано, то используется телефон пользователя
 */
class personal_model_Resume extends Som_Model_Abstract
{
    /**
     * @var Som_Model_Mapper_Abstract $db
     */
    protected static $_db = null;
    protected static $_columns = null;
    protected static $_tbname = '';
    protected static $_primary_key = 'id';

    public static $fetchСolumns = array();
    public static $fetchJoins = array();

    protected $userPhone = null;
    protected $userEmail = null;
    protected $hideEmail = false;
    protected $_languages = null;
    protected $_education = null;
    protected $_recommendations = null;
    protected $_experiences = null;

    /**
     * @var array Занятость
     */
    protected $_employment = null;

    /**
     * @var array График работы
     */
    protected $_schedule = null;

    // Статусы резюме
//    const STATUS_PUBLISHED      = 1;
//    const STATUS_MODERATING     = 2;
//    const STATUS_BANNED         = 3;
//    const STATUS_BLOKED_BY_USER = 4;
//    const STATUS_LOW_DATA       = 5;
//
//    const UNKNOWN              = 0;
//    const INCOMPLETE_SECONDARY = 1;
//    const COMPLETE_SECONDARY   = 2;
//    const SPECIAL_SECONDARY    = 3;
//    const HIGH                 = 4;
//    const CANDIDATE            = 5;
//    const PROFESSOR            = 6;


    /**
     * Static constructor
     */
    public static function __init($db = 'db'){
        global $db_personal_resumes;

        static::$_tbname = $db_personal_resumes;
        parent::__init($db);
    }

    function validators()
    {
        return array(
//                array('salary', new Validator_Int(0, 9999999)),
//                array('specialization,birthdate,city,category,staff,skills,education', 'required'),
            array('category', function ($value) {
                return (count($value) < 9) ? true : false;
            }),
            array('staff', function ($value) {
                $ret = (count($value) < 3) ? true : false;

                return $ret;
            }),
        );
    }

    public function ava($w = 109, $h = 109, $sp = 'inside', $attrs = '')
    {
//        if ($this->getId()) {
//            $list = File_Image::getFileList('Resume', 'ava', $this->getId());
//            if (!empty($list)) return $list[0]->getHTML($w, $h, $sp, null, $attrs);
//        }
//        $attrs = " style='width:109px;height:109px;' ";
//        return '<img ' . $attrs . ' src="/site/themes/personal/img/nophoto.jpg">';
    }

    protected function userData(){
        global $db_users, $cot_extrafields;

        if(empty($this->_data['user_id'])) return '';

        $sql = "SELECT user_email, user_hideemail";
        if(isset($cot_extrafields[$db_users]['phone'])) $sql .= ', user_phone';
        $sql .= " FROM ".static::$_db->quoteIdentifier($db_users)." WHERE user_id={$this->_data['user_id']}";
        $tmp = static::$_db->query($sql)->fetch();
        if(empty($tmp)) return '';

        $this->userEmail = $tmp['user_email'];
        $this->hideEmail = $tmp['user_hideemail'];
        if(!empty($tmp['user_phone'])){
            $this->userPhone = $tmp['user_phone'];
        }else{
            $this->userPhone = '';
        }
    }

    public function getEmail(){
        global $cot_extrafields, $db_users;

        if(!empty($this->_data['remail'])) return $this->_data['remail'];

        if(is_null($this->userEmail)) $this->userData();

        // Проверить разрешено ли показывать e-mail пользователям
        if(!$this->hideEmail && !empty($this->userEmail)) return $this->userEmail;
        return '';
    }

    public function issetEmail(){
        $tmp = $this->getEmail();
        return !empty($tmp);
    }

    public function getPhone(){
        global $cot_extrafields, $db_users;

        if(!empty($this->_data['rphone'])) return $this->_data['rphone'];

        if(!isset($cot_extrafields[$db_users]['phone']))  return '';

        if(is_null($this->userPhone)) $this->userData();

        if(!empty($this->userPhone)) return $this->userPhone;

        return '';
    }

    public function issetPhone(){
        $tmp = $this->getPhone();
        return !empty($tmp);
    }

    public function getLanguages(){
        global $db_personal_languages, $db_personal_resumes_lang_levels;

        if(!is_null($this->_languages)) return $this->_languages;

        $sql = "SELECT lvl.id as lang_level_id,  lvl.lang_id, lvl.level_id, l.title as lang_title
                FROM $db_personal_resumes_lang_levels as lvl
                LEFT JOIN $db_personal_languages as l ON lvl.lang_id=l.id
                WHERE lvl.resume_id=".$this->_data['id'];

        $langs = static::$_db->query($sql)->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        if($langs){
            foreach($langs as $key => $val){
                $langs[$key]['lang_level_id'] = $key;
                $langs[$key]['level_title'] = cot::$L['personal_lang_levels'][$val['level_id']];
            }
            $this->_languages = $langs;
        }else{
            $this->_languages = false;
        }

        return $this->_languages;
    }

    public function issetLanguages(){
        $tmp = $this->getLanguages();
        return !empty($tmp);
    }

    public function getEducation(){
        global $db_personal_resumes_education, $db_personal_education_levels;

        if(!is_null($this->_education)) return $this->_education;

        $sql = "SELECT e.id as education_id,  e.level_id, e.title as education_title, e.faculty, e.specialty, e.year,
                l.title as level_title
                FROM $db_personal_resumes_education as e
                LEFT JOIN $db_personal_education_levels as l ON e.level_id=l.id
                WHERE e.resume_id=".$this->_data['id']."
                ORDER BY e.year";

        $items = static::$_db->query($sql)->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        if($items){
            foreach($items as $key => $val){
                $items[$key]['education_id'] = $key;
                //$items[$key]['level_title'] = cot::$L['personal_lang_levels'][$val['level_id']];
            }
            $this->_education = $items;
        }else{
            $this->_education = false;
        }

        return $this->_education;
    }

    public function issetEducation(){
        $tmp = $this->getEducation();
        return !empty($tmp);
    }

    public function getRecommendations(){
        global $db_personal_resumes_recommend;

        if(!is_null($this->_recommendations)) return $this->_recommendations;

        $sql = "SELECT *
                FROM $db_personal_resumes_recommend
                WHERE resume_id=".$this->_data['id'];

        $items = static::$_db->query($sql)->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        if($items){
            foreach($items as $key => $val){
                $items[$key]['id'] = $key;
                //$items[$key]['level_title'] = cot::$L['personal_lang_levels'][$val['level_id']];
            }
            $this->_recommendations = $items;
        }else{
            $this->_recommendations = false;
        }

        return $this->_recommendations;
    }

    public function issetRecommendations(){
        $tmp = $this->getRecommendations();
        return !empty($tmp);
    }


    public function getExperiences(){
        global $db_personal_resumes_experience, $db_rec_city;

        if(!is_null($this->_experiences)) return $this->_experiences;

        $sql = "SELECT e.*, c.city_title
                FROM $db_personal_resumes_experience as e
                LEFT JOIN $db_rec_city as c ON e.city=c.city_id
                WHERE resume_id=".$this->_data['id'];

        $items = static::$_db->query($sql)->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

        if($items){
            foreach($items as $key => $val){
                $items[$key]['id'] = $key;
            }
            $this->_experiences = $items;
        }else{
            $this->_experiences = false;
        }

        return $this->_experiences;
    }

    public function issetExperiences(){
        $tmp = $this->getExperiences();
        return !empty($tmp);
    }


    public function getEmployment(){
        global $db_personal_resumes_employment;

        if(!is_null($this->_employment)) return $this->_employment;
        if(empty($this->_data['id'])) return null;

        $sql = "SELECT empl_id
                FROM $db_personal_resumes_employment
                WHERE resume_id=".$this->_data['id'];

        $items = static::$_db->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        if($items){
            $this->_employment = $items;
        }else{
            $this->_employment = false;
        }

        return $this->_employment;
    }

    public function setEmployment($values){

        if(empty($values)) $this->_employment = null;

        if(!is_array($values)){
            $values = (int)$values;
            if(empty($this->_employment)){
                $this->_employment = array($values);
            }elseif(!in_array($values, $this->_employment)){
                $this->_employment[] = $values;
            }
        }else{
            $values = array_map('intval', $values);
            $values = array_unique($values);
            foreach($values as $key => $val){
                if($values[$key] < 1) unset($values[$key]);
            }
            $this->_employment = $values;
        }
    }

    public function issetEmployment(){
        $tmp = $this->getEmployment();
        return !empty($tmp);
    }

    public function unsetEmployment()
    {
        $this->_employment = null;
    }

    public function getSchedule(){
        global $db_personal_resumes_schedule;

        if(!is_null($this->_schedule)) return $this->_schedule;
        if(empty($this->_data['id'])) return null;

        $sql = "SELECT sche_id
                FROM $db_personal_resumes_schedule
                WHERE resume_id=".$this->_data['id'];

        $items = static::$_db->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        if($items){
            $this->_schedule = $items;
        }else{
            $this->_schedule = false;
        }

        return $this->_schedule;
    }

    public function setSchedule($values){
        if(empty($values)) $this->_schedule = null;

        if(!is_array($values)){
            $values = (int)$values;
            if(empty($this->_schedule)){
                $this->_schedule = array($values);
            }elseif(!in_array($values, $this->_schedule)){
                $this->_schedule[] = $values;
            }
        }else{
            $values = array_map('intval', $values);
            $values = array_unique($values);
            foreach($values as $key => $val){
                if($values[$key] < 1) unset($values[$key]);
            }
            $this->_schedule = $values;
        }
    }

    public function issetSchedule(){
        $tmp = $this->getSchedule();
        return !empty($tmp);
    }

    public function unsetSchedule()
    {
        $this->_schedule = null;
    }

    public function activate(){
        $this->_data['active'] = 1;
        $this->_data['activated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['sort'] = date('Y-m-d H:i:s', cot::$sys['now']);
    }

    public function deactivate(){
        $this->_data['hot'] = 0;
        if(!empty($this->_data['hot_to']) && strtotime($this->_data['hot_to']) > cot::$sys['now']){
            $this->_data['hot_to'] = date('Y-m-d H:i:s', cot::$sys['now']);
        }
        $this->_data['active'] = 0;
    }

    public function getHot(){
        if($this->_data['hot'] == 1 &&
            (empty($this->_data['hot_to']) || strtotime($this->_data['hot_to']) <= cot::$sys['now'])){
            $this->makeUnHot();
            $this->save();
        }
        return ($this->_data['hot'] == 1);
    }

    public function makeHot($to = 0){

        if($this->_data['active'] == 1) return false;

        if($to == 0){
            $today = getdate(cot::$sys['now']);
            $to = mktime($today['hours'], $today['minutes'], $today['seconds'], $today['mon'] + 1, $today['mday'],
                $today['year']);
        }

        if (is_int($to) || ctype_digit($to)) $to = date('Y-m-d H:i:s', $to);

        $this->_data['hot_to'] = $to;
        $this->_data['hot'] = 1;
    }

    public function makeUnHot(){
        $this->_data['hot'] = 0;
        if(!empty($this->_data['hot_to']) && strtotime($this->_data['hot_to']) > cot::$sys['now']){
            $this->_data['hot_to'] = date('Y-m-d H:i:s', cot::$sys['now']);
        }
    }

    protected function beforeInsert(){
        if(empty($this->_data['created'])){
            $this->_data['created'] = date('Y-m-d H:i:s', cot::$sys['now']);
        }

        if(empty($this->_data['created_by'])){
            $this->_data['created_by'] = cot::$usr['id'];
        }

        if(empty($this->_data['updated'])){
            $this->_data['updated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        }

        if(empty($this->_data['updated_by'])){
            $this->_data['updated_by'] = cot::$usr['id'];
        }

        return parent::beforeInsert();
    }

    protected function afterInsert(){
       if($this->_data['id'] > 0) cot_files_linkFiles('personal_resume', $this->_data['id']);
    }

    protected function beforeUpdate(){
        $this->_data['updated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['updated_by'] = cot::$usr['id'];

        return parent::beforeUpdate();
    }

    protected function afterSave(){
        global $db_personal_resumes_employment, $db_personal_resumes_schedule;

        $this->saveXData($db_personal_resumes_employment, 'empl_id', $this->_employment);
        $this->saveXData($db_personal_resumes_schedule,   'sche_id', $this->_schedule);
    }

    protected function beforeDelete(){
        global $db_personal_resumes_lang_levels, $db_personal_resumes_education, $db_personal_resumes_recommend,
               $db_personal_resumes_experience, $db_personal_resumes_schedule, $db_personal_resumes_employment;

        // Удалить уровни влядения языками
        static::$_db->delete($db_personal_resumes_lang_levels, "resume_id={$this->_data['id']}");

        // Удалить уровни образования
        static::$_db->delete($db_personal_resumes_education, "resume_id={$this->_data['id']}");

        // Удалить места работы
        static::$_db->delete($db_personal_resumes_experience, "resume_id={$this->_data['id']}");

        // Удалить рекомендации
        static::$_db->delete($db_personal_resumes_recommend, "resume_id={$this->_data['id']}");

        // Удалить график работы
        static::$_db->delete($db_personal_resumes_schedule, "resume_id={$this->_data['id']}");

        // Удалить занятость
        static::$_db->delete($db_personal_resumes_employment, "resume_id={$this->_data['id']}");

        // Remove all files
        if(cot_module_active('files')){
            $files = files_model_File::find(array(
                array('file_source', 'personal_resume'),
                array('file_item', $this->_data['id'])
            ));
            if(!empty($files)){
                foreach($files as $fileRow){
                    $fileRow->delete();
                }
            }
        }
        return parent::beforeDelete();

    }

    /**
     * Сохранить связи
     * @param string $xTableName
     * @param string $xPKey
     * @param array  $data
     * @return bool
     * @throws Exception
     */
    protected function saveXData($xTableName, $xPKey, $data)
    {
        if(empty($this->_data['id'])) return false;

        $pKey = 'resume_id';
        $id = $this->_data['id'];
        $tq = static::$_db->getQuoteIdentifierSymbol();

        // Сохраняем связи
        $query = "SELECT ".static::$_db->quoteIdentifier($xPKey)." FROM ".static::$_db->quoteIdentifier($xTableName)."
                  WHERE ".static::$_db->quoteIdentifier($pKey)."={$id}";
        $old_xRefs = static::$_db->query($query)->fetchAll(PDO::FETCH_COLUMN);

        if (!$old_xRefs) $old_xRefs = array();
        $kept_xRefs = array();
        $new_xRefs = array();

        // Find new links, count old links that have been left
        $cnt = 0;
        $isstr = false;

        if ($data)
            foreach ($data as $item) {
                $p = array_search($item, $old_xRefs);
                if ($p !== false) {
                    $kept_xRefs[] = $old_xRefs[$p];
                    $cnt++;
                } else {
                    $new_xRefs[] = $item;
                }
            }
        // Remove old links that have been removed
        $rem_xRefs = array_diff($old_xRefs, $kept_xRefs);
        if (count($rem_xRefs) > 0) {
            $inCond = "(" . implode(",", $rem_xRefs) . ")";
            static::$_db->delete($xTableName, "{$pKey}=$id AND {$xPKey} IN $inCond");
        }
        // Add new xRefs
        foreach ($new_xRefs as $item) {
            if ((!$isstr && $item > 0) || ($isstr && $item != '')) {
                $upData = array(
                    $pKey => $id,
                    $xPKey => $item,
                );
                $res = static::$_db->insert($xTableName, $upData);
                if ($res === false) {
//                    $error = static::$_db->_adapter->errorInfo();
//                    throw new Exception("SQL Error {$error[0]}: {$error[2]}");
                    throw new Exception("SQL Error");
                };
            }
        }

        return true;

    }

    /**
     * @return array
     */
    public static function fieldList()
    {
        return array(
            'id' =>
                array(
                    'name' => 'id',
                    'type' => 'bigint',
                    'primary' => true,
                ),
            'category' =>
                array(
                    'name' => 'category',
                    'type' => 'link',
                    'default' => NULL,
                    'description' => 'Категория',
                    'link' =>
                        array(
                            'model' => 'personal_model_Category',
                            'relation' => 'tomany',
                            'label' => 'title',
                        ),
                ),
            'user_id' =>
                array(
                    'name' => 'user_id',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'id пользователя владельца',
                ),
            'title' =>
                array(
                    'name' => 'title',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Желаемая должность',
                ),
            'alias' =>
                array(
                    'name' => 'alias',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Транслитерированный URL',
                ),
            'text' =>
                array(
                    'name' => 'text',
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Подробное описание резюме',
                ),
            'city' =>
                array(
                    'name' => 'city',
                    'type' => 'link',
                    'nullable' => true,
                    'default' => NULL,
                    'link' =>
                        array(
                            'model' => 'regioncity_model_City',
                            'relation' => 'toone',
                            'label' => 'city_title',
                        ),
                ),
            'city_name' =>
                array(
                    'name' => 'city_name',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Название города',
                ),
            'leaving' =>
                array(
                    'name' => 'leaving',
                    'type' => 'link',
                    'description' => 'Переезд в город',
                    'link' =>
                        array(
                            'model' => 'regioncity_model_City',
                            'relation' => 'tomanynull',
                            'label' => 'name',
                            //'remote' => array('host' => '/geo/suggest/city'),
                        ),
                ),
            'district' =>
                array(
                    'name' => 'district',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Район города',
                ),
            'salary' =>
                array(
                    'name' => 'salary',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Минимальный уровень зарплаты',
                ),
            'remail' =>
                array(
                    'name' => 'remail',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Адрес электронной почты',
                ),
            'rphone' =>
                array(
                    'name' => 'rphone',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Контактный телефон',
                ),
            'other_contacts' =>
                array(
                    'name' => 'other_contacts',
                    'type' => 'text',
                    'nullable' => true,
                    'default' => '',
                    'description' => 'Дополнение к контактной информации.',
                ),
            'skills' =>
                array(
                    'name' => 'skills',
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Профессиональные навыки соискателя.',
                ),
            'education_level' =>
                array(
                    'name' => 'education_level',
                    'type' => 'link',
                    'nullable' => true,
                    'default' => 0,
                    'description' => 'Уровень образования / ученная степень.',
                    'link' =>
                        array(
                            'model' => 'personal_model_EducationLevel',
                            'relation' => 'toone',
                            'label' => 'title',
                        ),
                ),
            'experience' =>
                array(
                    'name' => 'experience',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Опыт работы в месяцах.',
                ),
            'staff' =>
                array(
                    'name' => 'staff',
                    'type' => 'link',
                    'description' => 'Уровень в шт. расписании',
                    'link' =>
                        array(
                            'model' => 'personal_model_Staff',
                            'relation' => 'tomanynull',
                            'label' => 'name',
                        ),
                ),
            'status' =>
                array(
                    'name' => 'status',
                    'type' => 'tinyint',
                    'default' => 0,
                    'description' => 'Статус резюме',
                ),
            'active' => array(
                'name' => 'active',
                'type' => 'tinyint',
                'nullable' => true,
                'default' => 0,
                'description' => 'Активно?',
            ),
            'hot' =>
                array(
                    'name' => 'hot',
                    'type' => 'tinyint',
                    'nullable' => true,
                    'default' => 0,
                    'description' => 'Горячее?',
                ),
            'hot_to' =>
                array(
                    'name' => 'hot_to',
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => NULL,
                    'description' => 'Горячее до',
                ),
            'views' =>
                array(
                    'name' => 'views',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Просмотров',
                ),
            'activated' =>
                array(
                    'name' => 'activated',
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => NULL,
                    'description' => 'Дата последней активации',
                ),
            'deactivated' =>
                array(
                    'name' => 'deactivated',
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => NULL,
                    'description' => 'Дата полследнего отключения',
                ),
            'sort' =>
                array(
                    'name' => 'sort',
                    'type' => 'datetime',
                    'default' => date('Y-m-d H:i:s', cot::$sys['now']),
                    'description' => 'Поле для сортировки',
                ),
            'deny_unregister' =>
                array(
                    'name' => 'deny_unregister',
                    'type' => 'tinyint',
                    'default' => 0,
                    'description' => 'Запретить для незарегистрированных',
                ),
            'note' =>
                array(
                    'name' => 'note',
                    'type' => 'text',
                    'description' => 'Сообщение модератора',
                ),
            'created' =>
                array(
                    'name' => 'created',
                    'type' => 'datetime',
                    'default' => date('Y-m-d H:i:s', cot::$sys['now']),
                    'description' => 'Дата создания',
                ),
            'created_by' =>
                array(
                    'name'        => 'created_by',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Кем создана запись',
                ),
            'updated' =>
                array(
                    'name' => 'updated',
                    'type' => 'datetime',
                    'default' => date('Y-m-d H:i:s', cot::$sys['now']),
                    'description' => 'Дата последнего обновления',
                ),
            'updated_by' =>
                array(
                    'name'        => 'updated_by',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Кем обновлено последний раз',
                ),
        );
    }

    // === Методы для работы с шаблонами ===
    /**
     * Returns all Resume tags for coTemplate
     *
     * @param personal_model_Resume|int $item object or it's ID
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @return array|void
     */
    public static function generateTags($item, $tagPrefix = '', $cacheitem = true){
        global $usr;

        static $extp_first = null, $extp_main = null;
        static $cacheArr = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('personal.resume.tags.first');
            $extp_main  = cot_getextplugins('personal.resume.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( ($item instanceof personal_model_Resume) && is_array($cacheArr[$item->id]) ) {
            $temp_array = $cacheArr[$item->id];
        }elseif (is_int($item) && is_array($cacheArr[$item])){
            $temp_array = $cacheArr[$item];
        }else{
            if (is_int($item) && $item > 0){
                $item = personal_model_Resume::getById($item);
            }
            /** @var personal_model_Resume $item  */
            if ($item && $item->id > 0){
                $tmp = array('a'=>'resume', 'id' => $item->id);
                if(!empty($item->alias)) $tmp['al'] = $item->alias;
                $itemUrl = cot_url('personal', $tmp);

                $itemEditUrl = '';
                $itemDelUrl = '';
                $itemPreviewUrl = $itemUrl;
                if(($usr['auth_write'] && $usr['id'] == $item->user_id) || $usr['isadmin']){
                    $tmp = array('m'=>'user','a'=>'resumeView', 'rid'=>$item->id);
                    if($usr['isadmin'] && $usr['id'] != $item->user_id) $tmp['uid'] = $item->user_id;
                    $itemPreviewUrl = cot_url('personal', $tmp);

                    $tmp = array('m'=>'user','a'=>'resumeEdit', 'rid'=>$item->id);
                    if($usr['isadmin'] && $usr['id'] != $item->user_id) $tmp['uid'] = $item->user_id;
                    $itemEditUrl = cot_url('personal', $tmp);

                    $itemDelUrl  = cot_confirm_url(cot_url('personal',
                        array('m'=>'user', 'a'=>'resumeDelete', 'rid'=>$item->id)));
                }

                $leaving = '';
//                $leavingName = '';
                if(!empty($item->leaving)){
                    $leaving = array();
                    foreach($item->leaving as $lCity){
                        $leaving[$lCity->city_id] = $lCity->city_title;
                    }
                }

                $staff = '';
                if(!empty($item->staff)){
                    $staff = array();
                    foreach($item->staff as $staffRow){
                        $staff[$staffRow->id] = $staffRow->title;
                    }
                }

                $employment = array();
                $schedule = array();
                if(!empty($item->employment)){
                    foreach($item->employment as $key => $val){
                        if(isset(cot::$L['personal_employment_levels'][$val])){
                            $employment[] = htmlspecialchars(cot::$L['personal_employment_levels'][$val]);
                        }
                    }
                }
                if(!empty($item->schedule)){
                    foreach($item->schedule as $key => $val){
                        if(isset(cot::$L['personal_schedule_levels'][$val])){
                            $schedule[] = htmlspecialchars(cot::$L['personal_schedule_levels'][$val]);
                        }
                    }
                }

                $sortText = '';
                if(!empty($item->sort)){
                    $sort = strtotime($item->sort);
                    if(date('Y-m-d', $sort) == date('Y-m-d', cot::$sys['now'])){
                        $sortText = cot::$L['Today'];
                    }elseif(date('Y', $sort) < date('Y', cot::$sys['now'])){
                        $sortText = cot_date('date_text', $sort);
                    }else{
                        $sortText = cot_date('d F', $sort);
                    }
                }

                $date_format = 'datetime_full';
                $temp_array = array(
                    'URL' => $itemUrl,
                    'PREVIEW_URL' => $itemPreviewUrl,
                    'EDIT_URL' => $itemEditUrl,
                    'DELETE_URL' => $itemDelUrl,
                    'ID' => $item->id,
                    'ACTIVE' => $item->active ? 1 : 0,
                    'HOT' => $item->hot ? 1 : 0,
                    'DENY_UNREGISTER' => $item->deny_unregister ? 1 : 0,
                    'TITLE' => htmlspecialchars($item->title),
                    'VIEWS' => $item->views,
                    'USER_ID' => $item->user_id,
                    'CITY' => $item->rawValue('city'),
                    'CITY_NAME' => htmlspecialchars($item->city_name),
                    'DISTRICT' => htmlspecialchars($item->district),
                    'LEAVING' => $leaving,
                    'LEAVING_NAME' => htmlspecialchars(implode(', ', $leaving)),
                    'PHONE' => $item->phone,
                    'EMAIL' => $item->email,
                    'CATEGORY_RAW' => (!empty($item->category)) ? $item->category : '',
                    'SALARY' => $item->salary,
                    'STAFF' => (!empty($staff)) ? '<ul class="list-unstyled"><li>'.implode('<li></li>',$staff).'</li></ul>' : '',
                    'STAFF_RAW' => $staff,
                    'EMPLOYMENT' => (!empty($employment))? implode(', ', $employment) : '',
                    'EMPLOYMENT_RAW' => $item->employment,
                    'SCHEDULE' => (!empty($schedule))?   implode(', ', $schedule)   : '',
                    'SCHEDULE_RAW' => $item->schedule,
                    'SKILLS' => $item->skills,
                    'TEXT' => $item->text,
                    'OTHER_CONTACTS' => $item->other_contacts,
                    'EDUCATION_LEVEL' => htmlspecialchars($item->education_level->title),
                    'EXPERIENCE' => ($item->experience > 0) ? personal_friendlyExperience($item->experience) : '',
                    'EXPERIENCE_RAW' => $item->experience,

                    'SORT' => $item->sort,
                    'SORT_DATE' => cot_date($date_format, strtotime($item->sort)),
                    'SORT_TEXT' => $sortText,
                    'SORT_RAW' => strtotime($item->sort),

                    'ACTIVATED' => $item->activated,
                    'ACTIVATED_DATE' => cot_date($date_format, strtotime($item->activated)),
                    'ACTIVATED_RAW' => strtotime($item->activated),

                    'CREATED' => $item->created,
                    'CREATE_DATE' => cot_date($date_format, strtotime($item->created)),
                    'CREATED_RAW' => strtotime($item->created),

                    'UPDATED' => $item->updated,
                    'UPDATE_DATE' => cot_date($date_format, strtotime($item->updated)),
                    'UPDATED_RAW' => strtotime($item->updated),

                );

                // Extrafields
//                if (isset($cot_extrafields[$db_pages])){
//                    foreach ($cot_extrafields[$db_pages] as $row) {
//                        $tag = mb_strtoupper($row['field_name']);
//                        $temp_array[$tag.'_TITLE'] = isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
//                        $temp_array[$tag] = cot_build_extrafields_data('page', $row, $order["page_{$row['field_name']}"], $order['page_parser']);
//                    }
//                }

                /* === Hook === */
                foreach ($extp_main as $pl)
                {
                    include $pl;
                }
                /* ===== */
                $cacheitem && $cacheArr[$item->st_id] = $temp_array;
            }else{

            }
        }

        $return_array = array();
        foreach ($temp_array as $key => $val){
            $return_array[$tagPrefix . $key] = $val;
        }

        return $return_array;
    }

}

personal_model_Resume::__init();