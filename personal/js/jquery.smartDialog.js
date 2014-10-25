/**
 * Smart Dialog jQuery Plugin
 *
 * Renders a Dialog.
 * Can use default Cotonti jQuery Modal, jQueryUI Dialog or Bootstrap Modal
 *
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright (c) 2014 Portal30 Studio http://portal30.ru
 */

/* === Buttons Example ===
buttons: [
    { text: 'Ok',
        click: function() {
            // ... some code ...
            $.smartDialog('close');
        } },
    { text: 'Close',
        click: function() { $.smartDialog('close'); } }
],
++======================== */

(function( $ ){
    'use strict';

    /**
     *  Настройки по умолчанию
     *  @type {{}}
     */
    var defaults = {
            /**
             * Тип диалога может быть '', 'ui' или 'bootstrap'
             */
            type: '',
            title : '',
            text  : '',
            'class': '',

            /**
             * This event fires immediately when the show instance method is called.
             */
            show: null,

            /**
             * This event is fired when the modal has been made visible to the user
             */
            shown: null,

            /**
             * This event is fired immediately when the close instance method has been called
             */
            hide: null,

            /**
             * This event is fired when the modal has finished being hidden from the user
             */
            hidden: null,

            /**
             * This event is fired when the modal has loaded content using the remote option.
             * @todo
             */
            //loaded: null,

            buttons: [
                {text: 'Close', click: function() { methods.close() } }
            ],
            closeBtnUrl : '/modules/personal/tpl/images/close.png'

        },

        /**
         * Актуальные настройки, глобальные
         * @type {{}}
         */
        options = {},

        /**
         * Данные по диалогу
         * @type {{}}
         */
        element = false,
        isShown = false,

        /**
         * Подключен ли jQueryUI
         * @returns {boolean}
         */
        isUI = function(){
            var included = true;
            if (jQuery.type(jQuery.ui) == 'undefined'){
                included = false;
            }else{
                if (jQuery.type(jQuery.ui.dialog) == 'undefined'){
                    included = false;
                }
            }
            return included;
        },

        /**
         * Подключен ли jQueryUI
         * @returns {boolean}
         */
        isBootstrap = function(){
            var included = true;
            if (jQuery.type(jQuery.fn.modal) == 'undefined'){
                included = false;
            }

            return included;
        },

        /**
         * Строим диалог на jQueryUI
         * @param params
         */
        uiDialog = function( params ){
            var text = '<div class="'+params.class+'">' + params.text + '</div>';

            element = $('<div>', { 'id': "confirmBox" }).css('overflow', 'visible').html(text);
            element.find('.jqmClose').click(function(e){
                e.preventDefault();
                methods.close();
            });
            var dialogOpts = {
                title: params.title,
                buttons: params.buttons,
                width: 'auto',
                modal: true,
                close: function(e){
                    if(element != null) element.remove();
                    element = null;
                    isShown = false;
                    if(params.hidden) { params.hidden(e);}
                }
            };
            if(params.show) {
                dialogOpts.create = function(e, ui){ params.shown(e, element);}
            }

            if(params.shown) {
                dialogOpts.open = function(e, ui){ params.show(e, element);}
            }

            if(params.hide) {
                dialogOpts.beforeClose = function(e, ui){ params.hide(e, element);}
            }

            element.dialog(dialogOpts);
            isShown = true;
        },

        /**
         * Строим диалог на Bootstrap Modal
         * @param params
         */
        bootstrapDialod = function ( params ){
            var text = '<div class="modal-body"><div class="'+params.class+'" style="margin-bottom: 0">' + params.text + '</div></div>';
            //var text = '<div class="modal-body '+params.class+'" style="margin-bottom: 0">' + params.text + '</div>';
            var modalContent = '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>' +
                            '<span class="sr-only">Close</span></button>'+
                        '<h4 class="modal-title">'+ params.title +'</h4>' +
                    '</div>' + text;
            var modalFooter = false;
            if (params.buttons != false){
                modalFooter = $('<div>', {'class': 'modal-footer'});
                $.each(params.buttons, function(index, value) {
                    var btnClass = '';
                    var btn = $('<button>', { 'id': "btn_"+index, 'class': btnClass+"btn btn-default" } ).css('overflow', 'visible').html(value.text);
                    // Поддержка события click для кнопки
                    if (typeof value.click === 'function') $(btn).click(function(){ return value.click() });

                    modalFooter.append(btn);
                });
            }
            modalContent = modalContent +  '</div></div>'

            element = $('<div>', { 'id': "confirmBox", 'class': 'modal' }).html(modalContent);
            if (params.buttons != false){
                element.find('.modal-content').append(modalFooter);
            }
            if(params.show) {
                element.on('show.bs.modal', function (e) {
                    params.show(e, element);
                })
            }
            if(params.shown) {
                element.on('shown.bs.modal', function (e) {
                    params.shown(e, element);
                })
            }
            if(params.hide) {
                element.on('hide.bs.modal', function (e) {
                    params.hide(e, element);
                })
            }

            element.on('hidden.bs.modal', function (e) {
                if(element != null) element.remove();
                element = null;
                isShown = false;
                if(params.hidden) { params.hidden(e);}
            });
            element.modal();
            isShown = true;
        },

        /**
         * Строим диалог на jQuery Modal
         * @param params
         */
        jqmDialog = function( params ){
            var title = '';
            if (params.title != ''){
                title = '<h2 class="info">'+params.title+'</h2>';
            }

            var closeBtn = '<div style="float: right; margin: -20px -20px 0 0"><img class="jqmClose" src="'+
                params.closeBtnUrl +'" style="cursor: pointer" /></div>';
            var text = closeBtn + title + '<div class="'+params.class+'">' + params.text + '</div>';

            element = $('<div>', { id: "confirmBox", 'class':  "jqmWindow" } ).css('overflow', 'visible').html(text);
            $('body').prepend(element);

            if (params.buttons != false){
                $.each(params.buttons, function(index, value) {
                    var btnClass = '';
                    value.action = value.action || false;

                    var btn = $('<button>', { 'id': "btn_"+index, 'class': btnClass+"btn btn-default" } ).css('overflow', 'visible').html(value.text);
                    // Поддержка события click для кнопки
                    if (typeof value.click === 'function') $(btn).click(function(){ return value.click() });

                    element.append(btn);
                });
            }

            element.jqm({
                modal:true,
                onShow:function(hash){
                    if(params.show) params.show(hash, element);

                    hash.w.show();
                    hash.w.css('margin-left', '-'+(hash.w.width()/2)+'px');
                    hash.w.css('margin-top', '-'+(hash.w.height()/2)+'px');

                    if(params.shown) params.shown(hash, element);
                    isShown = true;
                },
                onHide:function(hash){
                    if(params.hide) params.hide(hash, element);

                    hash.w.fadeOut('2000',function(){ hash.o.remove(); });
                    if (params.close) params.close();
                    if(element != null) element.remove();
                    element = null;
                    isShown = false;
                    if(params.hidden) params.hidden(hash);
                }
            });
            element.jqmShow();
            isShown = true;
        };

    var methods = {
        // инициализация плагина
        init : function( params ) {
            // при многократном вызове функции настройки будут сохранятся, и замещаться при необходимости
            options = $.extend({}, defaults, options, params);
            return this
        },

        show : function( params ) {
            if(isShown) return false;

            // Handle straight text
            if(typeof(params) == 'string'){
                params = {text:params};
            }

            // We might have some issues if we don't have a title or text!
            if(params.text === null){
                throw 'You must supply "text" parameter.';
            }

            options = $.extend({}, defaults, options);
            params = $.extend({}, defaults, options, params);

            options.type = params.type; // тип диалога сохраняем для последующего использования

            if(options.type == 'ui' && isUI()){
                uiDialog(params);

            }else if(options.type == 'bootstrap' && isBootstrap()) {
                bootstrapDialod(params);

            }else{
                jqmDialog(params);
            }

            return this
        },

        close : function (){
            if(!isShown) return false;
            if(options.type == 'ui' && isUI()){
                element.dialog("close");
                if(element != null) element.dialog("destroy");

            }else if(options.type == 'bootstrap' && isBootstrap()) {
                element.modal('hide');

            }else{
                element.jqmHide();
            }
            if(element != null) element.remove();
            element = null;
            isShown = false;

            return this
        }
    };


    /**
     * Renders a Dialog.
     *
     * Can use default Cotonti jQuery Modal, jQueryUI Dialog or Bootstrap Modal
     *
     * @param method
     * @returns {*}
     */
    $.smartDialog = function( method ) {
        // логика вызова метода
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            // Handle straight text
            if(typeof(method) == 'string'){
                return methods.show({text: method})
            }
            $.error( 'Метод с именем ' +  method + ' не существует для jQuery.smartDialog' );
        }
    };
})( jQuery );