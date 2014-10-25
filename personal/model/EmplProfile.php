<?php

/**
 * Модель personal_model_EmplProfile
 *
 * Профили (организации) работодателей
 *
 * @method static personal_model_EmplProfile getById($pk);
 * @method static personal_model_EmplProfile fetchOne($conditions = array(), $order = '');
 * @method static personal_model_EmplProfile[] find($conditions = array(), $limit = 0, $offset = 0, $order = '');
 *
 * @property int $id                Идентификатор профиля компании
 * @property int $user_id           id пользователя
 * @property int $locked            Индикатор блокирования профиля.
 *                                      0 - не заблокирован;
 *                                      1 - заблокирован владельцем счета;
 *                                      2 - заблокирован системой.
 * @property string $title          Название организации профиля.
 * @property string $alias          Транслитерированный URL
 * @property string $text           Описание организации-профиля.
 * @property string $address        Адрес организации.
 * @property string $pphone         Контактный телефон профиля компании.
 * @property string $pemail         Адрес электронной почты компании.
 * @property string $site           Адрес сайта для этого профиля.
 * @property bool $is_default       Флаг профиля поумолчанию для данного аккаунта
 * @property int $type              Тип профиля. 0 - прямой, 1 - кадровое агентство
 * @property bool $anonim           Флаг анонимного профиля
 * @property string $in_main_to     Время выключения услуги "в центре внимания
 * @property string $brand_bg       Бекграунд для страницы банка. Брендирование
 * @property string $brand_tpl
 * @property string $brand_css      Дополнительный файл CSS для банка
 * @property string $brand_bg_bot
 * @property string $created        Дата/время создания счета
 * @property string $updated        Дата/время обновления записи
 *
 *
 * @property User_Model_User $created_by [relation=toonenull;label=login] Кем создана запись
 * @property User_Model_User $updated_by [relation=toonenull;label=login] Кем обновлена запись
 *
 * @property string $phone         Контактный телефон профиля компании.
 *                                 если не указано, то используется телефон пользователя-владельца
 * @property string $email         Адрес электронной почты компании.
 *                                 если не указано, то используется email пользователя-владельца
 *
 * @todo правильное удаление профиля
 *
 */
class personal_model_EmplProfile extends Som_Model_Abstract
{
    /** @var Som_Model_Mapper_Abstract $db */
    protected static $_db = null;
    protected static $_columns = null;
    protected static $_tbname = '';
    protected static $_primary_key = 'id';

    const TYPE_STRAIGHT = 0;
    const TYPE_AGENCY = 1;

    protected $userPhone = null;
    protected $userEmail = null;
    protected $hideEmail = false;

    /**
     * Static constructor
     */
    public static function __init($db = 'db'){
        global $db_personal_empl_profiles;

        static::$_tbname = $db_personal_empl_profiles;

        parent::__init($db);
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

        if(!empty($this->_data['pemail'])) return $this->_data['pemail'];
        if(empty($this->_data['user_id'])) return '';

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

        if(!empty($this->_data['pphone'])) return $this->_data['pphone'];

        if(empty($this->_data['user_id'])) return '';

        if(!isset($cot_extrafields[$db_users]['phone']))  return '';

        if(is_null($this->userPhone)) $this->userData();

        if(!empty($this->userPhone)) return $this->userPhone;

        return '';
    }

    public function issetPhone(){
        $tmp = $this->getPhone();
        return !empty($tmp);
    }
//
//    public function logoHtml($width = 109, $height = 109, $frame = 'inside', $attrs = ''){
//        if ($this->_data['id'] == 0) return '';
//
//        $list = File_Image::getFileList('emplProfile', 'logo', $this->_data['id']);
//        if (!empty($list)) return $list[0]->getHTML($width, $height, $frame, null, $attrs);
//
//        return '';
//    }

    public function logoInMainHtml($w = 109, $h = 109, $sp = 'inside', $attrs = ''){

    }

    protected function beforeSave(&$data = null) {

//        if($this->_data['account'] > 0){
//            $profileCnt = Personal_Model_EmplProfile::count(array(
//                array('account', $this->_data['account']),
//                array('id', (int)$this->_data['id'], '!=')
//
//            ));
//
//            $onlyProfile = empty($profileCnt);
//
//            // Единственный аккаунт может быть только по-умолчанию
//            if($onlyProfile){
//                $this->_data['default'] = 1;
//            }
//
//            // Только один профиль может быть по-умолчанию
//            if(!$onlyProfile && $this->_data['default']){
//                static::$_db->update(static::$_tbname, array('default'=>0), 'id!='.(int)$this->_data['id']);
//                unset(parent::$_stCache['Personal_Model_EmplProfile']);
//            }
//        }

        return parent::beforeSave($data);
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

    protected function beforeUpdate(){
        $this->_data['updated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['updated_by'] = cot::$usr['id'];

        return parent::beforeUpdate();
    }


    protected function beforeDelete(){
        // Remove all files
        if(cot_module_active('files')){
            $files = files_model_File::find(array(
                array('file_source', 'personal_empl_profile'),
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

    public static function fieldList()
    {
        return array(
            'id' =>
                array(
                    'name' => 'id',
                    'type' => 'int',
                    'description' => 'Идентификатор профиля компании',
                    'default' => 0,
                    'primary' => true,
                ),
            'user_id' =>
                array(
                    'name' => 'user_id',
                    'type' => 'int',
                    'description' => 'Идентификатор профиля компании',
                ),
            'locked' =>
                array(
                    'name' => 'locked',
                    'type' => 'tinyint',
                    'default' => 0,
                    'description' => 'Индикатор блокирования профиля. 0 - не заблокирован; 1 - заблокирован владельцем счета; 2 - заблокирован системой',
                ),
            'title' =>
                array(
                    'name' => 'title',
                    'type' => 'varchar',
                    'nullable' => false,
                    'default' => '',
                    'description' => 'Название организации',
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
                    'description' => 'Описание организации-профиля',
                ),
            'address' =>
                array(
                    'name' => 'address',
                    'type' => 'varchar',
                    'default' => '',
                    'description' => 'Адрес организации',
                ),
            'pphone' =>
                array(
                    'name' => 'pphone',
                    'type' => 'varchar',
                    'default' => '',
                    'description' => 'Контактный телефон профиля компании',
                ),
            'pemail' =>
                array(
                    'name' => 'pemail',
                    'type' => 'varchar',
                    'default' => '',
                    'description' => 'Адрес электронной почты компании',
                ),
            'site' =>
                array(
                    'name' => 'site',
                    'type' => 'varchar',
                    'default' => '',
                    'description' => 'Адрес сайта для этого профиля',
                ),

            'is_default' =>
                array(
                    'name' => 'is_default',
                    'type' => 'tinyint',
                    'default' => 0,
                    'description' => 'Флаг профиля поумолчанию для данного аккаунта',
                ),
            'type' =>
                array(
                    'name' => 'type',
                    'type' => 'tinyint',
                    'default' => 0,
                    'description' => 'Тип профиля. 0 - прямой, 1 - кадровое агентство',
                ),
            'anonim' =>
                array(
                    'name' => 'anonim',
                    'type' => 'tinyint',
                    'default' => 0,
                    'description' => 'Флаг анонимного профиля',
                ),
            'in_main_to' =>
                array(
                    'name' => 'in_main_to',
                    'type' => 'datetime',
                    'default' => "1970-01-01 00:00:00",
                    'description' => 'Время выключения услуги "в центре внимания',
                ),
            'brand_bg' =>
                array(
                    'name' => 'prof_brand_bg',
                    'type' => 'varchar',
                    'length' => '255',
                    'nullable' => true,
                    'default' => '',
                    'description' => 'Бекграунд для страницы банка. Брендирование',
                ),
            'brand_tpl' =>
                array(
                    'name' => 'prof_brand_tpl',
                    'type' => 'varchar',
                    'length' => '255',
                    'nullable' => true,
                    'default' => '',
                ),
            'brand_css' =>
                array(
                    'name' => 'prof_brand_css',
                    'type' => 'varchar',
                    'length' => '255',
                    'nullable' => true,
                    'default' => '',
                    'description' => 'Дополнительный файл CSS для банка',
                ),
            'brand_bg_bot' =>
                array(
                    'name' => 'prof_brand_bg_bot',
                    'type' => 'varchar',
                    'length' => '255',
                    'nullable' => true,
                    'default' => '',
                ),
            'created' =>
                array(
                    'name' => 'create_ts',
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => date('Y-m-d H:i:s', cot::$sys['now']),
                    'description' => 'Дата/время создания профиля',
                ),
            'created_by' =>
                array(
                    'name' => 'created_by',
                    'type' => 'int',
                    'description' => 'Кем создана запись',
                    'default' => '0',
                ),
            'updated' =>
                array(
                    'name' => 'create_ts',
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => date('Y-m-d H:i:s',  cot::$sys['now']),
                    'description' => 'Дата/время обновления записи',
                ),
            'updated_by' =>
                array(
                    'name' => 'updated_by',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Кем обновлена запись',
                ),
        );
    }


    // === Методы для работы с шаблонами ===
    /**
     * Returns all Employer Profile tags for coTemplate
     *
     * @param personal_model_EmplProfile|int $item object or it's ID
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @return array|void
     */
    public static function generateTags($item, $tagPrefix = '', $cacheitem = true){
        global $usr;

        static $extp_first = null, $extp_main = null;
        static $cacheArr = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('personal.emplprofile.tags.first');
            $extp_main  = cot_getextplugins('personal.emplprofile.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( ($item instanceof personal_model_EmplProfile) && is_array($cacheArr[$item->id]) ) {
            $temp_array = $cacheArr[$item->id];
        }elseif (is_int($item) && is_array($cacheArr[$item])){
            $temp_array = $cacheArr[$item];
        }else{
            if (is_int($item) && $item > 0){
                $item = personal_model_EmplProfile::getById($item);
            }
            /** @var personal_model_EmplProfile $item  */
            if ($item && $item->id > 0){
                $tmp = array('a'=>'employer', 'id' => $item->id);
                if(!empty($item->alias)) $tmp['al'] = $item->alias;
                $itemUrl = cot_url('personal', $tmp);

                $itemEditUrl = '';
                $itemDelUrl = '';
//                if(($usr['auth_write'] && $usr['id'] == $item->user_id) || $usr['isadmin']){
//                    $itemEditUrl = cot_url('personal', array('m'=>'user','a'=>'vacancyEdit', 'vid'=>$item->id));
//                    $itemDelUrl  = cot_confirm_url(cot_url('personal',
//                        array('m'=>'user', 'a'=>'vacancyDelete', 'vid'=>$item->id)));
//                }

                $siteUrl = '';
                if(!empty($item->site)){
                    $siteUrl  = str_replace('&amp;', '&', $item->site);
                    // Если в начале переданной строки указан протокол
                    if( (mb_strpos($siteUrl, "http://") !== 0) && (mb_strpos($siteUrl, "https://") !== 0) ){
                        $siteUrl = 'http://'.$siteUrl;
                    }
                }

                $date_format = 'datetime_full';
                $temp_array = array(
                    'URL' => $itemUrl,
                    'EDIT_URL' => $itemEditUrl,
                    'DELETE_URL' => $itemDelUrl,
                    'ID' => $item->id,
                    'TITLE' => htmlspecialchars($item->title),
                    'USER_ID' => $item->user_id,
                    'ADDRESS' => $item->address,
                    'PHONE' => $item->phone,
                    'EMAIL' => $item->email,
                    'SITE' => $item->site,
                    'SITE_URL' => $siteUrl,
                    'TYPE' => $item->type,
                    'TEXT' => $item->text,


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

personal_model_EmplProfile::__init();