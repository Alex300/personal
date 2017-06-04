<?php
defined('COT_CODE') or die('Wrong URL.');

/**
 * Category Model
 *
 * @package Personal
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 *
 * @method static personal_model_Category getById($pk, $staticCache = true)
 * @method static personal_model_Category fetchOne($conditions = array(), $order = '')
 * @method static personal_model_Category[] findByCondition($conditions = array(), $limit = 0, $offset = 0, $order = '')
 *
 * @property int                     $id
 * @property string                  $title         Загловок.
 * @property personal_model_Category $parent        [relation=toonenull;label=title] Родитель
 * @property string                  $FullPathTitle Полный путь для элемента     "1/2/3/4"
 *
 * @property int $level Уровень вложенности
 * @property int $parent_id id родителя
 */
class personal_model_Category extends Som_Model_ActiveRecord
{
    protected static $_db = null;
    protected static $_columns = null;
    protected static $_tbname = '';
    protected static $_primary_key = 'id';

    protected static $_structure;
    protected static $_structureTree;
    protected static $_categories;


    /**
     * Static constructor
     */
    public static function __init($db = 'db'){
        global $db_personal_categories;

        static::$_tbname = $db_personal_categories;
        parent::__init($db);
    }

    protected static function structureTree()
    {
        if (empty(static::$_structureTree)) {

            $cats = personal_model_Category::findByCondition(array(), 0, 0, array(array('title', 'ASC')));
            if (empty($cats)) return null;

            $tmpArr = array();
            foreach ($cats as $cat) {
                static::$_categories[strval($cat->id)] = $cat;

                $arr = array(
                    'id' => $cat->id,
                    'title' => $cat->title,
                    'level' => 0,
                    'pathId' => $cat->id,
                );
                $parent = 0;
                if (!empty($cat->parent)) $parent = $cat->parent->id;
                $tmpArr[strval($parent)][] = $arr;
            }

            $ret = self::buildTree($tmpArr, 0, 0);

            static::$_structureTree = $ret;
        }
        return static::$_structureTree;
    }

    protected static function buildTree($arr, $parentId, $level, $path = '')
    {
        if (isset($arr[$parentId])) { //Если категория с таким parent_id существует
            $ret = array();
            foreach ($arr[$parentId] as $key => $value) { //Обходим ее

                $value['level'] = $level;
                if (!empty($path)) $value['pathId'] = $path . '.' . $value['pathId'];
                $ret[strval($value['id'])] = $value;

                $level++; //Увеличиваем уровень вложености

                // Рекурсивно вызываем этот же метод, но с новым $parent_id и $level
                $children = self::buildTree($arr, $value['id'], $level, $value['pathId']);
                if (!empty($children)) $ret[strval($value['id'])]['items'] = $children;
                $level--; //Уменьшаем уровень вложености
            }

            return $ret;
        }
    }

    public static function getAll()
    {
        return static::structureTree();
    }

    /**
     * @param  Personal_Model_Category $child
     *
     * @return Personal_Model_Category[]
     * @var Personal_Model_Category    $ptr
     */
    public static function GetParentsArray($child)
    {
        $ret = Array();
        $ptr = $child;
        while ($ptr = $ptr->parent)
            $ret[] = $ptr;
        return $ret;
    }

    public function getParent_id(){
        return intval($this->_data['parent']);
    }

    /**
     * Рекурсивная функция построения плоского массива структуры категорий
     *
     * @param null $items
     *
     * @return null
     */
    protected static function loadStructure($items = null)
    {
        static $ret = array();
        if (empty($items)) {
            $items = static::structureTree();
            if (empty($items)) return null;
        }
        foreach ($items as $key => $item) {
            $ret[strval($key)] = $item;
            unset($ret[strval($key)]['items']);
            if (!empty($items[$key]['items'])) {
                self::loadStructure($items[$key]['items']);
            }
        }

        return $ret;
    }

    protected static function structure()
    {
        if (empty(static::$_structure)) {
            static::$_structure = self::loadStructure();
            if (empty(static::$_structure)) return null;

            $oldCats = static::$_categories;
            static::$_categories = array();
            // Отсортируем массив категорий по порядку
            foreach (static::$_structure as $key => $val) {
                static::$_categories[strval($key)] = $oldCats[$key];
            }
        }
        return static::$_structure;
    }

    /**
     * Уровень вложенности
     *
     * @return int
     */
    public function getLevel()
    {
        $structure = self::structure();

        if (!empty($structure[$this->_data['id']]['level'])) {
            return $structure[$this->_data['id']]['level'];
        }
        return 0;
    }

    /**
     * Получить список всех категорий, отсортированный по алфавиту и родителям
     * Если у категории есть потомки, то они выводятся по-алфавиту вслед за своим родителем
     *
     * @return Personal_Model_Category[] массив, где ключ - id категории, а значение объект
     */
    public static function getAllFlat()
    {
        self::structure();
        if (empty(static::$_structure)) return null;

        return static::$_categories;
    }

    /**
     * Получить список вложенных всех категорий,
     *
     * @param bool $allsublev Потомки всех уровней?
     * @return Personal_Model_Category[] массив, где ключ - id категории, а значение объект
     */
    public function children($allsublev = true)
    {
        $structure = self::structure();

        $mtch = $structure[$this->id]['pathId'] . '.';
        $mtchlen = mb_strlen($mtch);
        $mtchlvl = mb_substr_count($mtch, ".");

        $catsub = array();
        foreach ($structure as $i => $x) {
            if ((mb_substr($x['pathId'], 0, $mtchlen) == $mtch)) {
                if ($allsublev || (!$allsublev && mb_substr_count($x['path'], ".") == $mtchlvl)) {
                    $catsub[strval($i)] = static::$_categories[$i];
                }
            }
        }
        return ($catsub);
    }


    /**
     * @param  Personal_Model_Category[] $arr
     *
     * @return string
     */
    public static function array2jstree($arr = null)
    {
        $fullarray = array();
        $modarray = array();
        $ret = array();

        if(!defined('PERSONAL_JSTREE')) {
            Resources::linkFileFooter(cot::$cfg['modules_dir'].'/personal/js/jstree/jstree.min.js');
            Resources::linkFileFooter(cot::$cfg['modules_dir'].'/personal/js/jstree/themes/default/style.min.css');
            define('PERSONAL_JSTREE', 1);
        }

        /**
         * @var Personal_Model_Category $cat
         */
        if (empty($arr)){
            $fullarray = static::getAllFlat();
            
        } else {
            foreach ($arr as $cat) {
                $fullarray = array_merge($fullarray, static::GetParentsArray($cat));
                $fullarray[] = $cat;
            }

        }

        foreach ($fullarray as $cat) {
            $modarray[$cat->getId()] = $cat;
        }
        $arr = $modarray;
        foreach ($arr as $id => $cat) {
            if ($cat->parent)
                $parent = $cat->parent->getId();
            else
                $parent = '#';

            $ret[] = array('id' => $id, 'parent' => $parent, 'text' => $cat->title);
        }

        return json_encode($ret);
    }

    public static function fieldList()
    {
        return array(
            'id' =>
                array(
                    'name' => 'res_id',
                    'type' => 'bigint',
                    'nullable' => false,
                    'primary' => true,
                ),
            'title' =>
                array(
                    'name' => 'title',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => 'Загловок.',
                ),
            'parent' =>
                array(
                    'name' => 'parent',
                    'type' => 'link',
                    'description' => 'Родитель',
                    'default' => '0',
                    'link' =>
                        array(
                            'relation' => 'toonenull',
                            'model' => 'Personal_Model_Category',
                            'label' => 'title',
                        ),
                ),
        );
    }

    /**
     * @return string
     * @var personal_model_Category $ptr
     */
    public function getFullPathTitle()
    {
        $ret = $this->title;
        $ptr = $this;
        while ($ptr = $ptr->parent)
            $ret = $ptr->title . ' / ' . $ret;
        return $ret;
    }

    // === Методы для работы с шаблонами ===
    /**
     * Returns all Group tags for coTemplate
     *
     * @param personal_model_Category|int $item object or it's ID
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @return array|void
     */
    public static function generateTags($item, $tagPrefix = '', $cacheitem = true){
        global $usr;

        static $extp_first = null, $extp_main = null;
        static $cacheArr = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('personal.category.tags.first');
            $extp_main  = cot_getextplugins('personal.category.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( ($item instanceof personal_model_Category) && is_array($cacheArr[$item->id]) ) {
            $temp_array = $cacheArr[$item->id];
        }elseif (is_int($item) && is_array($cacheArr[$item])){
            $temp_array = $cacheArr[$item];
        }else{
            if (is_int($item) && $item > 0){
                $item = personal_model_Category::getById($item);
            }
            /** @var personal_model_Category $item  */
            if ($item && $item->id > 0){
                $itemDelUrl = '';
                if($usr['isadmin']){
                    $itemDelUrl  = cot_confirm_url(cot_url('admin',
                            array('m'=>'personal', 'cid'=>$item->id, 'a'=>'categoryDelete')),
                        'personal', 'personal_category_deleteConfirm');
                }
                $temp_array = array(
                    'DELETE_URL' => $itemDelUrl,
                    'ID' => $item->id,
                    'TITLE' => htmlspecialchars($item->title),
                    'PARENT_ID' => $item->parent_id,
                    'LEVEL' => $item->level
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

personal_model_Category::__init();