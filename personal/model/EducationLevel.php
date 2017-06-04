<?php
defined('COT_CODE') or die('Wrong URL.');

/**
 * Education Level model
 *
 * @package Personal
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 *
 * @method static personal_model_EducationLevel getById($pk, $staticCache = true)
 * @method static personal_model_EducationLevel fetchOne($conditions = array(), $order = '');
 * @method static personal_model_EducationLevel[] findByCondition($conditions = array(), $limit = 0, $offset = 0, $order = '')
 *
 * @property int       $id
 * @property string    $title   Загловок
 * @property int       $order   Порядок для сортировки
 */
class personal_model_EducationLevel extends Som_Model_ActiveRecord
{
    /**
     * @var Som_Model_Mapper_Abstract
     */
    protected static $_db = null;
    protected static $_columns = null;
    protected static $_tbname = '';
    protected static $_primary_key = 'id';

    /**
     * Static constructor
     */
    public static function __init($db = 'db'){
        global $db_personal_education_levels;

        static::$_tbname = $db_personal_education_levels;
        parent::__init($db);
    }

    protected function beforeInsert(){
        if(empty($this->_data['order'])){
            $this->_data['order'] = ((int)static::$_db->query("SELECT MAX(".static::$_db->quoteIdentifier('order').")
                    FROM ".static::$_tbname)->fetchColumn()) + 1;
        }

        return true;
    }

    public static function fieldList()
    {
        return array(
            'id' =>
                array(
                    'name' => 'id',
                    'type' => 'int',
                    'nullable' => false,
                    'primary' => true,
                ),
            'title' =>
                array(
                    'title' => 'title',
                    'type' => 'varchar',
                    'length' => '255',
                    'nullable' => false,
                    'default' => '',
                    'description' => 'Наименование уровня образования',
                ),
            'order'=>
                array(
                    'title' => 'order',
                    'type' => 'int',
                    'default' => 0,
                    'description' => 'Порядок для сортировки',
                ),
        );
    }


    // === Методы для работы с шаблонами ===
    /**
     * Returns all Group tags for coTemplate
     *
     * @param personal_model_EducationLevel|int $item object or it's ID
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @return array|void
     */
    public static function generateTags($item, $tagPrefix = '', $cacheitem = true){
        global $usr;

        static $extp_first = null, $extp_main = null;
        static $cacheArr = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('personal.education_level.tags.first');
            $extp_main  = cot_getextplugins('personal.education_level.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( ($item instanceof personal_model_EducationLevel) && is_array($cacheArr[$item->id]) ) {
            $temp_array = $cacheArr[$item->id];
        }elseif (is_int($item) && is_array($cacheArr[$item])){
            $temp_array = $cacheArr[$item];
        }else{
            if (is_int($item) && $item > 0){
                $item = personal_model_EducationLevel::getById($item);
            }
            /** @var personal_model_EducationLevel $item  */
            if ($item && $item->id > 0){
                $itemDelUrl = '';
                if($usr['isadmin']){
                    $itemDelUrl  = cot_confirm_url( cot_url('admin', array('m'=>'personal', 'eid'=>$item->id, 'a'=>'educationDelete')) );
                }
                $temp_array = array(
                    'DELETE_URL' => $itemDelUrl,
                    'ID' => $item->id,
                    'TITLE' => htmlspecialchars($item->title),
                    'ORDER' => $item->order,
                );

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
personal_model_EducationLevel::__init();