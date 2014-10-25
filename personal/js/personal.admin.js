/**
 * module Personal for Cotonti Siena
 *
 * @package Personal
 * @author Kalnov Alexey
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
$(function() {
    if(typeof(jQuery.fn.select2) != 'undefined'){
        $("select.select2").select2({
            placeholder: "Кликните для выбора"
        });
    }
});