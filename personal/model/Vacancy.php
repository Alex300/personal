<?php

/**
 * Модель personal_model_Vacancy
 *
 * Вакансии
 *
 * @method static personal_model_Vacancy getById($pk);
 * @method static personal_model_Vacancy fetchOne($conditions = array(), $order = '');
 * @method static personal_model_Vacancy[] find($conditions = array(), $limit = 0, $offset = 0, $order = '');
 *
 * @property int $id
 * @property personal_model_Category[] $category [relation=tomany;label=title] Категория
 * @property personal_model_EmplProfile $profile [relation=toone;label=title]  Идентификатор профиля под которым создавалась вакансия
 * @property int                    $user_id    id пользователя
 * @property string                 $title      Должность
 * @property string                 $alias      Транслитерированный URL
 * @property string                 $text       Подробное описание вакансии
 * @property regioncity_model_City  $city [relation=toone;label=city_title] Город
 * @property string                 $district   Район города
 * @property int                    $salary     Минимальный уровень зарплаты
 * @property string                 $vcontact_face Контактное лицо по вопросу предоставленной вакансии.
 * @property string                 $vemail      Адрес электронной почты
 * @property string                 $vphone      Контактный телефон
 * @property string                 $skills     Описание профессилнальх качеств которыми должен обладать соискатель
 * @property int                    $experience Профессиональный стаж соискателя
 * @property personal_model_EducationLevel  $education  Требуемый от соискателя уровень образования
 * @property personal_model_Staff   $staff      [relation=toone;label=name] Уровень в шт. расписании
 * @property int                    $status     Статус вакансии
 * @property bool                   $active     Активная?
 * @property string                 $active_to  Активна до
 * @property bool                   $hot        Горячая?
 * @property string                 $hot_to     Горячая до
 * @property int                    $views      Просмотров
 * @property string                 $activated  Дата последней активации
 * @property string                 $deactivated Дата полследнего отключения
 * @property string                 $sort       Поле для сортировки
 * @property string                 $created    Дата создания
 * @property int                    $created_by Кем создана запись
 * @property string                 $updated    Дата последнего обновления
 * @property int                    $updated_by Кем обновлено последний раз
 *
 *
 * @property string                 $city_name      Город
 * @property array                  $employment     Занятость
 * @property array                  $schedule       График работы
 *
 * @property string                 $contactFace    Контактное лицо по вопросу предоставленной вакансии,
 *                                                  если не указано, то используется ФИО разместившего менеджера
 * @property string                 $email          Адрес электронной почты,
 *                                                  если не указано, то используется email профиля работодателя
 * @property string                 $phone          Контактный телефон,
 *                                                  если не указано, то используется телефон профиля работодателя
 */
class personal_model_Vacancy extends Som_Model_Abstract
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
    protected $userName  = null;

    protected $_cityName;

    /**
     * @var array Занятость
     */
    protected $_employment = null;

    /**
     * @var array График работы
     */
    protected $_schedule = null;

//    public static $experiences = array(
//        6   => 'не менее полугода',
//        12  => 'не менее года',
//        18  => 'не менее полутора лет',
//        24  => 'не менее 2-х лет',
//        36  => 'не менее 3-х лет',
//        48  => 'не менее 4-х лет',
//        60  => 'не менее 5-и лет',
//        72  => 'не менее 6-и лет',
//        84  => 'не менее 7-и лет',
//        96  => 'не менее 8-и лет',
//        108 => 'не менее 9-и лет',
//        120 => 'не менее 10-и лет',
//    );

    /**
     * Static constructor
     */
    public static function __init($db = 'db'){
        global $db_personal_vacancies, $db_rec_city;

        static::$_tbname = $db_personal_vacancies;

        static::$fetchСolumns[] = "{$db_rec_city}.city_title as city_name";
        static::$fetchJoins[]   = "LEFT JOIN $db_rec_city ON {$db_personal_vacancies}.city={$db_rec_city}.city_id";

        parent::__init($db);
    }

    public function init(&$data = array()){
        if($data['city_name'] != '') $this->_cityName = $data['city_name'];

        parent::init($data);
    }

    protected function userData(){
        global $db_users, $cot_extrafields;

        if(empty($this->_data['user_id'])) return '';

        $urr = cot_user_data($this->_data['user_id']);
        if(empty($urr)) return '';

        $this->userEmail = $urr['user_email'];
        $this->hideEmail = $urr['user_hideemail'];
        if(!empty($urr['user_phone'])){
            $this->userPhone = $urr['user_phone'];
        }else{
            $this->userPhone = '';
        }
        $this->userName = cot_user_full_name($urr);
    }

    public function getCity_name(){
        if($this->_cityName != '') return $this->_cityName;
    }

    public function getContactFace(){
        if(!empty($this->_data['vcontact_face'])) return $this->_data['vcontact_face'];

        if(is_null($this->userName)) $this->userData();

        return $this->userName;
    }

    public function issetContactFace(){
        $tmp = $this->getContactFace();
        return !empty($tmp);
    }

    public function getEmail(){
        if(!empty($this->_data['vemail'])) return $this->_data['vemail'];
        if(!empty($this->profile)) return $this->profile->email;

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

        if(!empty($this->_data['vphone'])) return $this->_data['vphone'];
        if(!empty($this->profile)) return $this->profile->phone;

        if(!isset($cot_extrafields[$db_users]['phone']))  return '';

        if(is_null($this->userPhone)) $this->userData();

        if(!empty($this->userPhone)) return $this->userPhone;
        return '';
    }

    public function issetPhone(){
        $tmp = $this->getPhone();
        return !empty($tmp);
    }

    public function getEmployment(){
        global $db_personal_vacancies_employment;

        if(!is_null($this->_employment)) return $this->_employment;
        if(empty($this->_data['id'])) return null;

        $sql = "SELECT empl_id
                FROM $db_personal_vacancies_employment
                WHERE vacancy_id=".$this->_data['id'];

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
        global $db_personal_vacancies_schedule;

        if(!is_null($this->_schedule)) return $this->_schedule;
        if(empty($this->_data['id'])) return null;

        $sql = "SELECT sche_id
                FROM $db_personal_vacancies_schedule
                WHERE vacancy_id=".$this->_data['id'];

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

    public function getActive(){
        if($this->_data['active'] == 1 &&
                    (empty($this->_data['active_to']) || strtotime($this->_data['active_to']) <= cot::$sys['now'])){
            $this->deactivate();
            $this->save();
        }
        return ($this->_data['active'] == 1);
    }

    public function activate($to = 0){

        if($to == 0){
            $today = getdate(cot::$sys['now']);
            $to = mktime($today['hours'], $today['minutes'], $today['seconds'], $today['mon'] + 1, $today['mday'],
                $today['year']);
        }

        if (is_int($to) || ctype_digit($to)) $to = date('Y-m-d H:i:s', $to);

        $this->_data['active_to'] = $to;
        $this->_data['active'] = 1;
        $this->_data['activated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['sort'] = date('Y-m-d H:i:s', cot::$sys['now']);
    }

    public function deactivate(){
        $this->_data['hot'] = 0;
        if(!empty($this->_data['hot_to']) && strtotime($this->_data['hot_to']) > cot::$sys['now']){
            $this->_data['hot_to'] = date('Y-m-d H:i:s', cot::$sys['now']);
        }
        if(!empty($this->_data['active_to']) && strtotime($this->_data['active_to']) > cot::$sys['now']){
            $this->_data['active_to'] = date('Y-m-d H:i:s', cot::$sys['now']);
            $this->_data['deactivated'] = date('Y-m-d H:i:s', cot::$sys['now']);
            $this->_data['active'] = 0;
        }
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

        if(!$this->getActive()) return false;

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

    /**
     * Может ли данная вакансия быть активирована
     * @return bool
     */
    public function canBeActivated(){
        return true;
    }

    /**
     * Может ли данная вакансия быть горячей
     * @return bool
     */
    public function canBeHot(){
        return true;
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
        if($this->_data['id'] > 0) cot_files_linkFiles('personal_vacancy', $this->_data['id']);
    }

    protected function beforeUpdate(){
        $this->_data['updated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['updated_by'] = cot::$usr['id'];

        return parent::beforeUpdate();
    }

    protected function afterSave(){
        global $db_personal_vacancies_employment, $db_personal_vacancies_schedule;

        $this->saveXData($db_personal_vacancies_employment, 'empl_id', $this->_employment);
        $this->saveXData($db_personal_vacancies_schedule,   'sche_id', $this->_schedule);
    }

    protected function beforeDelete(){
        global $db_personal_vacancies_employment, $db_personal_vacancies_schedule;

        // Удалить график работы
        static::$_db->delete($db_personal_vacancies_schedule,   "vacancy_id={$this->_data['id']}");

        // Удалить занятость
        static::$_db->delete($db_personal_vacancies_employment, "vacancy_id={$this->_data['id']}");

        // Remove all files
        if(cot_module_active('files')){
            $files = files_model_File::find(array(
                array('file_source', 'personal_vacancy'),
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

        $pKey = 'vacancy_id';
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
            'profile' =>
                array(
                    'name' => 'profile',
                    'type' => 'link',
                    'default' => 0,
                    'link' =>
                        array (
                            'model' => 'personal_model_EmplProfile',
                            'relation' => 'toonenull',
                            'label' => 'title',
                        ),
                    'description' => 'Идентификатор профиля под которым создавалась вакансия',
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
                    'description' => 'Должность',
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
                    'description' => 'Подробное описание вакансии',
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
            'vcontact_face' =>
                array(
                    'type' => 'varchar',
                    'length' => '255',
                    'nullable' => true,
                    'default' => '',
                    'description' => 'Контактное лицо по вопросу предоставленной вакансии',
                ),
            'vemail' =>
                array(
                    'name' => 'email',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Адрес электронной почты',
                ),
            'vphone' =>
                array(
                    'name' => 'phone',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Контактный телефон',
                ),
            'skills' =>
                array(
                    'name' => 'skills',
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Описание профессилнальх качеств которыми должен обладать соискатель',
                ),
            'experience' =>
                array(
                    'name' => 'experience',
                    'type' => 'smallint',
                    'default' => 0,
                    'description' => 'Профессиональный стаж соискателя',
                ),
            'education' =>
                array(
                    'name' => 'education',
                    'type' => 'link',
                    'nullable' => true,
                    'default' => 0,
                    'description' => 'Уровень образования',
                    'link' =>
                        array(
                            'model' => 'personal_model_EducationLevel',
                            'relation' => 'toone',
                            'label' => 'title',
                        ),
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
                    'description' => 'Статус вакансии',
                ),
            'active' => array(
                'name' => 'active',
                'type' => 'tinyint',
                'nullable' => true,
                'default' => 0,
                'description' => 'Активная?',
            ),
            'active_to' =>
                array(
                    'name' => 'active_to',
                    'type' => 'datetime',
                    'default' => '1970-01-01 00:00:00',
                    'description' => 'Активна до',
                ),
            'hot' =>
                array(
                    'name' => 'hot',
                    'type' => 'tinyint',
                    'nullable' => true,
                    'default' => 0,
                    'description' => 'Горячая?',
                ),
            'hot_to' =>
                array(
                    'name' => 'hot_to',
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => NULL,
                    'description' => 'Горячая до',
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
     * Returns all Vacancy tags for coTemplate
     *
     * @param personal_model_Vacancy|int $item object or it's ID
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @return array|void
     */
    public static function generateTags($item, $tagPrefix = '', $cacheitem = true){
        global $usr;

        static $extp_first = null, $extp_main = null;
        static $cacheArr = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('personal.vacancy.tags.first');
            $extp_main  = cot_getextplugins('personal.vacancy.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( ($item instanceof personal_model_Vacancy) && is_array($cacheArr[$item->id]) ) {
            $temp_array = $cacheArr[$item->id];
        }elseif (is_int($item) && is_array($cacheArr[$item])){
            $temp_array = $cacheArr[$item];
        }else{
            if (is_int($item) && $item > 0){
                $item = personal_model_Vacancy::getById($item);
            }
            /** @var personal_model_Vacancy $item  */
            if ($item && $item->id > 0){
                $tmp = array('a'=>'vacancy', 'id' => $item->id);
                if(!empty($item->alias)) $tmp['al'] = $item->alias;
                $itemUrl = cot_url('personal', $tmp);

                $itemEditUrl = '';
                $itemDelUrl = '';
                if(($usr['auth_write'] && $usr['id'] == $item->user_id) || $usr['isadmin']){
                    $itemEditUrl = cot_url('personal', array('m'=>'user','a'=>'vacancyEdit', 'vid'=>$item->id));
                    $itemDelUrl  = cot_confirm_url(cot_url('personal',
                            array('m'=>'user', 'a'=>'vacancyDelete', 'vid'=>$item->id)));
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

                $staff = '';
                if(!empty($item->staff)){
                    $staff = array();
                    foreach($item->staff as $staffRow){
                        $staff[$staffRow->id] = $staffRow->title;
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
                    'EDIT_URL' => $itemEditUrl,
                    'DELETE_URL' => $itemDelUrl,
                    'ID' => $item->id,
                    'ACTIVE' => $item->active,
                    'TITLE' => htmlspecialchars($item->title),
                    'VIEWS' => $item->views,
                    'USER_ID' => $item->user_id,
                    'EMPLOYMENT' => (!empty($employment))? implode(', ', $employment) : '',
                    'EMPLOYMENT_RAW' => $item->employment,
                    'SCHEDULE' => (!empty($schedule))?   implode(', ', $schedule)   : '',
                    'SCHEDULE_RAW' => $item->schedule,
                    'CITY' => $item->rawValue('city'),
                    'CITY_NAME' => htmlspecialchars($item->city_name),
                    'SALARY' => $item->salary,
                    'DISTRICT' => htmlspecialchars($item->district),
                    'PHONE' => $item->phone,
                    'EMAIL' => $item->email,
                    'CONTACT_FACE' => htmlspecialchars($item->contactFace),
                    'CATEGORY_RAW' => (!empty($item->category)) ? $item->category : '',
                    'STAFF' => (!empty($staff)) ? '<ul class="list-unstyled"><li>'.implode('<li></li>',$staff).'</li></ul>' : '',
                    'STAFF_RAW' => $staff,
                    'EDUCATION_LEVEL' => htmlspecialchars($item->education->title),
                    'EXPERIENCE' => (!empty($item->experience)) ?
                            htmlspecialchars(cot::$L['personal_vacancy_experiences'][$item->experience]) : "",
                    'EXPERIENCE_RAW' => $item->experience,
                    'TEXT' => $item->text,
                    'SKILLS' => $item->skills,

                    'SORT' => $item->sort,
                    'SORT_DATE' => cot_date($date_format, strtotime($item->sort)),
                    'SORT_TEXT' => $sortText,
                    'SORT_RAW' => strtotime($item->sort),


                    'ACTIVE_TO' => $item->active_to,
                    'ACTIVE_TO_DATE' => cot_date($date_format, strtotime($item->active_to)),
                    'ACTIVE_TO_RAW' => strtotime($item->active_to),

                    'HOT' => $item->hot,

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

personal_model_Vacancy::__init();