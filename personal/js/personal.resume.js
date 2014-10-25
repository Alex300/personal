/**
 * module Personal for Cotonti Siena
 *
 * @package Personal
 * @author Kalnov Alexey
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
'use strict'

var resProcessing = false;

$('#add-lang-save').click(function(e){
    e.preventDefault();

    if(resProcessing) return false;

    var parent = $('#languageModal .modal-dialog');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    var act = 'add_lang';
    var lang = $('select#add_lang_lang').val();
    var level = $('select#add_lang_level').val();
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    var langTitle = $('select#add_lang_lang option[value="'+lang+'"]').html();
    var levelTitle = $('select#add_lang_level option[value="'+level+'"]').html();

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit",
        { rid: resume_id, act: act, lang: lang, level: level,  x:x }, function( data ) {
            resProcessing = false;
            if(data.error != ''){
                alert(data.error);
                parent.css('opacity', '1');
                bgspan.remove();
            }else{
                if(data.act == 'reload') window.location.reload();

                parent.css('opacity', '1');
                bgspan.remove();
                $('#languageModal').modal('hide');

                $('select#add_lang_lang option[value="'+lang+'"]').remove();
                if (window.Select2 !== undefined) {
                    if($('select#add_lang_lang option').length > 0){
                        var nextVal = $('select#add_lang_lang option').attr('value');
                        $("#add_lang_lang").select2("val", nextVal);
                    }else{
                        $("#add_lang_lang").select2("val", "");
                    }
                }

                // Добавление строки с языком
                if(data.result == 'added'){
                    var newRow = $('#langLevel-tpl').clone().attr('id', 'langLevel-row-'+data.lang_lvl_id);
                    newRow.children('.langLevel-lang').attr('data-id', lang);
                    newRow.find('.langLevel-lang-title').html(langTitle);
                    newRow.children('.langLevel-level').html(levelTitle);
                    newRow.find('.delete-lang').attr('data-id', data.lang_lvl_id);
                    newRow.appendTo('#langLevelContainer').slideDown('slow');
                }else if(data.result == 'updated'){
                    $('#langLevel-row-'+lang+' .langLevel-level').html(levelTitle);
                }
                return;
            }
        }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });

});

$( document ).on( "click", ".delete-lang", function(e) {
    e.preventDefault();

    if(resProcessing) return false;

    var parent = $(this).parents('.langLevel-row');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    var langRow = parent.children('.langLevel-lang');
    var langId = langRow.attr('data-id');
    var langTitle = langRow.children('.langLevel-lang-title').html();
    var lang_lvl_id = $(this).attr('data-id');
    var act = 'delete_lang';
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit",
        { rid: resume_id, lang_lvl_id: lang_lvl_id, act: act, x:x }, function( data ) {
            resProcessing = false;
            if(data.error != ''){
                alert(data.error);
                parent.css('opacity', '1');
                bgspan.remove();
            }else{
                if(data.act == 'reload') window.location.reload();
                parent.css('opacity', '1');
                bgspan.remove();

                // Удаление строки с языком
                parent.slideUp( "slow", function() { parent.remove(); });

                // Вернуть в селект строку с языком
                $('select#add_lang_lang').prepend('<option value="'+langId+'">'+langTitle+'</option>');

                return;
            }
        }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });
});

// ==== Указать место обучения ====
$('#add_edu_title').blur(function(e){
    if($(this).val() == ''){
        $(this).parents('.form-group').addClass('has-error');
    }else{
        $(this).parents('.form-group').removeClass('has-error');
    }
});

$('#add-edu').click(function(e){
    e.preventDefault();

    // Заполним форму редактирования
    $('#add_edu_id').val(0);
    $('#add_edu_title').val('').parents('.form-group').removeClass('has-error');
//    $('#add_edu_level').val('');
//    if (window.Select2 !== undefined) {
//        $("#add_edu_level").select2("val", eduLevel);
//    }
    $('#add_edu_faculty').val('');
    $('#add_edu_specialty').val('');
//    $('#add_edu_year').val('');

    $('#educationModal').modal('show');

    return false;
});

$( document ).on( "click", ".edit-edu", function(e) {
    e.preventDefault();

    var parent = $(this).parents('.education-row');

    var eduLevel = $(this).attr('data-level_id');
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    // Заполним форму редактирования
    $('#add_edu_id').val($(this).attr('data-id'));
    $('#add_edu_title').val(parent.find('.edu-title').html());
    $('#add_edu_level').val(eduLevel);
    if (window.Select2 !== undefined) {
        $("#add_edu_level").select2("val", eduLevel);
    }
    $('#add_edu_faculty').val(parent.find('.edu-faculty').html());
    $('#add_edu_specialty').val(parent.find('.edu-specialty').html());
    $('#add_edu_year').val(parent.find('.edu-year').html());

    if($('#add_edu_title').val() != ''){
        $('#add_edu_title').removeClass('has-error');
    }

    $('#educationModal').modal('show');

    return false;
});


$('#add-edu-save').click(function(e){
    e.preventDefault();

    if(resProcessing) return false;

    var x = $('input[name=x]').val();
    var resume_id = $('input[name="rid"]').val();
    var title = $('#add_edu_title').val();

    if(title == ''){
        $('#add_edu_title').parent('.form-group').addClass('has-error');
        return;
    }

    var parent = $('#educationModal .modal-dialog');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit", $('#educationForm').serialize(), function( data ) {
            resProcessing = false;
            if(data.error != ''){
                alert(data.error);
                parent.css('opacity', '1');
                bgspan.remove();
            }else{
                if(data.act == 'reload') window.location.reload();

                  parent.css('opacity', '1');
                bgspan.remove();
                $('#educationModal').modal('hide');

                // Добавление строки с учебным заведением
                if(data.result == 'added'){
                    var eduRow = $('#edu-row-tpl').clone().attr('id', 'edu-row-'+data.eid);
                    eduRow.find('.edit-edu').attr('data-id', data.eid);
                    eduRow.find('.delete-edu').attr('data-id', data.eid);
                }else if(data.result == 'updated'){
                    var eduRow = $('#edu-row-'+data.eid);
                }
                eduRow.find('.edu-title').html(data.title);
                eduRow.find('.edu-year').html(data.year);
                eduRow.find('.edu-level-title').html(data.level_title);
                eduRow.find('.edit-edu').attr('data-level_id', data.level_id);
                eduRow.find('.edu-faculty').html(data.faculty);
                eduRow.find('.edu-specialty').html(data.specialty);

                if(data.result == 'added'){
                    eduRow.appendTo('#eduContainer').slideDown('slow');
                }

                return;
            }
        }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });

});

$( document ).on( "click", ".delete-edu", function(e) {
    e.preventDefault();

    if(resProcessing) return false;

    var parent = $(this).parents('.education-row');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    var eid = $(this).attr('data-id');
    var act = 'delete_edu';
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit",
        { rid: resume_id, eid: eid, act: act, x:x }, function( data ) {
            resProcessing = false;
            if(data.error != ''){
                alert(data.error);
                parent.css('opacity', '1');
                bgspan.remove();
            }else{
                if(data.act == 'reload') window.location.reload();
                parent.css('opacity', '1');
                bgspan.remove();

                // Удаление строки с местом обучения
                parent.slideUp( "slow", function() { parent.remove(); });
                return;
            }
        }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });
});
// ==== /Указать место обучения ====


// ==== Рекомендации ====
$('#add_recommend_name, #add_recommend_position, #add_recommend_organization, #add_recommend_phone').blur(function(e){
    if($(this).val() == ''){
        $(this).parents('.form-group').addClass('has-error');
    }else{
        $(this).parents('.form-group').removeClass('has-error');
    }
});

$('#add-recommend').click(function(e){
    e.preventDefault();

    // Заполним форму редактирования
    $('#add-recommend_id').val('0');
    $('#add_recommend_name').val('').parents('.form-group').removeClass('has-error');
    $('#add_recommend_position').val('').parents('.form-group').removeClass('has-error');
    $('#add_recommend_organization').val('').parents('.form-group').removeClass('has-error');
    $('#add_recommend_phone').val('').parents('.form-group').removeClass('has-error');

    $('#recommendModal').modal('show');

    return false;
});

$( document ).on( "click", ".edit-recommend", function(e) {
    e.preventDefault();

    var parent = $(this).parents('.recommend-row');

    var eduLevel = $(this).attr('data-level_id');
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    // Заполним форму редактирования
    $('#add-recommend_id').val($(this).attr('data-id'));
    $('#add_recommend_name').val(parent.find('.recommend-name').html()).parents('.form-group').removeClass('has-error');
    $('#add_recommend_position').val(parent.find('.recommend-position').html()).parents('.form-group').removeClass('has-error');
    $('#add_recommend_organization').val(parent.find('.recommend-organization').html()).parents('.form-group').removeClass('has-error');
    $('#add_recommend_phone').val(parent.find('.recommend-phone').html()).parents('.form-group').removeClass('has-error');

    $('#recommendModal').modal('show');

    return false;
});

$('#add_recommend-save').click(function(e){
    e.preventDefault();

    if(resProcessing) return false;

    var x = $('input[name=x]').val();
    var resume_id = $('input[name="rid"]').val();
    var rName         = $('#add_recommend_name');
    var rPosition     = $('#add_recommend_position');
    var rOrganization = $('#add_recommend_organization');
    var rPhone        = $('#add_recommend_phone');

    var isOk = true;
    if(rName.val() == ''){
        rName.parent('.form-group').addClass('has-error');
        isOk = false;
    }
    if(rOrganization.val() == ''){
        rOrganization.parent('.form-group').addClass('has-error');
        isOk = false;
    }
    if(rPosition.val() == ''){
        rPosition.parent('.form-group').addClass('has-error');
        isOk = false;
    }
    if(rPhone.val() == ''){
        rPhone.parent('.form-group').addClass('has-error');
        isOk = false;
    }
    if(!isOk) return false;

    var parent = $('#recommendModal .modal-dialog');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit", $('#recommendForm').serialize(), function( data ) {
        resProcessing = false;
        if(data.error != ''){
            alert(data.error);
            parent.css('opacity', '1');
            bgspan.remove();
        }else{
            if(data.act == 'reload') window.location.reload();

            parent.css('opacity', '1');
            bgspan.remove();
            $('#recommendModal').modal('hide');

            // Добавление строки с учебным заведением
            if(data.result == 'added'){
                var itemRow = $('#recommend-row-tpl').clone().attr('id', 'recommend-row-'+data.recommend_id);
                itemRow.find('.edit-recommend').attr('data-id', data.recommend_id);
                itemRow.find('.delete-recommend').attr('data-id', data.recommend_id);
            }else if(data.result == 'updated'){
                var itemRow = $('#recommend-row-'+data.recommend_id);
            }
            itemRow.find('.recommend-name').html(data.name);
            itemRow.find('.recommend-position').html(data.position);
            itemRow.find('.recommend-organization').html(data.organization);
            itemRow.find('.recommend-phone').html(data.phone);

            if(data.result == 'added'){
                itemRow.appendTo('#recommendContainer').slideDown('slow');
            }

            return;
        }
    }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });

});

$( document ).on( "click", ".delete-recommend", function(e) {
    e.preventDefault();

    if(resProcessing) return false;

    var parent = $(this).parents('.recommend-row');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    var recommend_id = $(this).attr('data-id');
    var act = 'delete_recommend';
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit",
        { rid: resume_id, recommend_id: recommend_id, act: act, x:x }, function( data ) {
            resProcessing = false;
            if(data.error != ''){
                alert(data.error);
                parent.css('opacity', '1');
                bgspan.remove();
            }else{
                if(data.act == 'reload') window.location.reload();
                parent.css('opacity', '1');
                bgspan.remove();

                // Удаление строки с местом обучения
                parent.slideUp( "slow", function() { parent.remove(); });
                return;
            }
        }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });
});
// ==== /Рекомендации ====


// ==== Опыт работы ====
$('#add_experience_organization, #add_experience_city, #add_experience_position, #rdpick_add_experience_begin').blur(function(e){
    if($(this).val() == ''){
        $(this).parents('.form-group').addClass('has-error');
    }else{
        $(this).parents('.form-group').removeClass('has-error');
    }
});
$( document ).on( "change", "#rdpick_add_experience_begin", function(e) {
    if($(this).val() == ''){
        $(this).parents('.form-group').addClass('has-error');
    }else{
        $(this).parents('.form-group').removeClass('has-error');
    }
});
$('#add_experience_city').change(function(e){
    if($(this).val() < 1){
        $(this).parents('.form-group').addClass('has-error');
    }else{
        $(this).parents('.form-group').removeClass('has-error');
    }
});

$('#add_experience_for_now').change(function(e){
    if($(this).prop('checked')){
        $('#rdpick_add_experience_end, select[name="add_experience_end[day]"], select[name="add_experience_end[month]"], select[name="add_experience_end[year]"]').attr('disabled', 'disabled');
    }else{
        $('#rdpick_add_experience_end, select[name="add_experience_end[day]"], select[name="add_experience_end[month]"], select[name="add_experience_end[year]"]').removeAttr('disabled');
    }
});

$('#add-experience').click(function(e){
    e.preventDefault();

    // Заполним форму редактирования
    $('#add-experience_id').val(0);
    $('#add_experience_organization').val('').parents('.form-group').removeClass('has-error');
    $('#add_experience_city').val(0).parents('.form-group').removeClass('has-error');
    if (window.Select2 !== undefined) {
        //$("#add_experience_city").select2("data", {id: 0, text: parent.find('.experience-city').html()});
        $("#add_experience_city").select2("val", 0);
    }

    $('#add_experience_website').val('').parents('.form-group').removeClass('has-error');
    $('#add_experience_position').val('').parents('.form-group').removeClass('has-error');

    $('#add_experience_for_now').removeAttr('checked');

    $('#rdpick_add_experience_begin').val('');
    $('#rdpick_add_experience_end').val('').removeAttr('disabled', 'disabled');

    $('select[name="add_experience_end[day]"], select[name="add_experience_end[month]"], select[name="add_experience_end[year]"]').removeAttr('disabled', 'disabled').val(0);

    $('select[name="add_experience_begin[day]"]').val(0);
    $('select[name="add_experience_begin[month]"]').val(0);
    $('select[name="add_experience_begin[year]"]').val(0);

    if (CKEDITOR.instances.add_experience_achievements != undefined) {
        CKEDITOR.instances.add_experience_achievements.setData('');
    } else {
        $('#add_experience_achievements').html('');
    }

    $('#experienceModal').modal('show');

    return false;
});

$( document ).on( "click", ".edit-experience", function(e) {
    e.preventDefault();

    var parent = $(this).parents('.experience-row');

    var cityId = $(this).attr('data-city_id');
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    // Заполним форму редактирования
    $('#add-experience_id').val($(this).attr('data-id'));
    $('#add_experience_organization').val(parent.find('.experience-organization').html()).
        parents('.form-group').removeClass('has-error');
    $('#add_experience_city').val(cityId).parents('.form-group').removeClass('has-error');
    if (window.Select2 !== undefined) {
        $("#add_experience_city").select2("data", {id: cityId, text: parent.find('.experience-city').html()});
    }
    $('#add_experience_city_name').val(parent.find('.experience-city').html());
    $('#add_experience_website').val($(this).attr('data-website')).parents('.form-group').removeClass('has-error');
    $('#add_experience_position').val(parent.find('.experience-position').html()).parents('.form-group').removeClass('has-error');

    var begin_stamp = parseInt($(this).attr('data-begin-stamp'));
    var end_stamp = parseInt($(this).attr('data-end-stamp'));
    var forNow = $(this).attr('data-for-now');
    if(forNow == 1){
        end_stamp = 0;
        $('#add_experience_for_now').attr('checked', 'checked');
        $('#rdpick_add_experience_end, select[name="add_experience_end[day]"], select[name="add_experience_end[month]"], select[name="add_experience_end[year]"]').attr('disabled', 'disabled');
    }

    $('#rdpick_add_experience_begin').val($(this).attr('data-begin-date'));
    if(begin_stamp > 0){
        var begin = new Date(begin_stamp * 1000);   // В JS оно в миллисекундах
        $('select[name="add_experience_begin[day]"]').val(begin.getDate());
        $('select[name="add_experience_begin[month]"]').val(begin.getMonth() + 1);
        $('select[name="add_experience_begin[year]"]').val(begin.getFullYear());
    }else{
        $('select[name="add_experience_begin[day]"]').val(0);
        $('select[name="add_experience_begin[month]"]').val(0);
        $('select[name="add_experience_begin[year]"]').val(0);
    }

    $('#rdpick_add_experience_end').val($(this).attr('data-end-date'));
    if(end_stamp > 0){
        var end = new Date(end_stamp * 1000);   // В JS оно в миллисекундах
        $('select[name="add_experience_end[day]"]').val(end.getDate());
        $('select[name="add_experience_end[month]"]').val(end.getMonth() + 1);
        $('select[name="add_experience_end[year]"]').val(end.getFullYear());
    }else{
        $('select[name="add_experience_end[day]"]').val(0);
        $('select[name="add_experience_end[month]"]').val(0);
        $('select[name="add_experience_end[year]"]').val(0);
    }


    if (CKEDITOR.instances.add_experience_achievements != undefined) {
        CKEDITOR.instances.add_experience_achievements.setData( parent.find('.experience-achievements').html(), function() {
                this.checkDirty(); // true
        } );
    } else {
        $('#add_experience_achievements').html(parent.find('.experience-achievements').html());
    }

    $('#experienceModal').modal('show');

    return false;
});


$('#add_experience-save').click(function(e){
    e.preventDefault();

    if(resProcessing) return false;

    var x = $('input[name=x]').val();
    var resume_id = $('input[name="rid"]').val();
    var eOrganization = $('#add_experience_organization');
    var eCity         = $('#add_experience_city');
    var ePosition     = $('#add_experience_position');
    var eBegin        = $('select[name="add_experience_begin[year]"]');

    var isOk = true;
    if(eOrganization.val() == ''){
        eOrganization.parent('.form-group').addClass('has-error');
        isOk = false;
    }

    if(eCity.val() == ''){
        eCity.parent('.form-group').addClass('has-error');
        isOk = false;
    }
    if(ePosition.val() == ''){
        ePosition.parent('.form-group').addClass('has-error');
        isOk = false;
    }
    if(eBegin.val() == ''){
        eBegin.parents('.form-group').addClass('has-error');
        isOk = false;
    }
    if(!isOk) return false;

    var parent = $('#experience .modal-dialog');

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');

    if (CKEDITOR.instances.add_experience_achievements != undefined) {
        CKEDITOR.instances.add_experience_achievements.updateElement();
    }

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit", $('#experienceForm').serialize(), function( data ) {
        resProcessing = false;
        if(data.error != ''){
            alert(data.error);
            parent.css('opacity', '1');
            bgspan.remove();
        }else{
            if(data.act == 'reload') window.location.reload();

            parent.css('opacity', '1');
            bgspan.remove();
            $('#experienceModal').modal('hide');

            // Добавление строки с опытом работы
            if(data.result == 'added'){
                var itemRow = $('#experience-row-tpl').clone().attr('id', 'experience-row-'+data.experience_id);
                itemRow.find('.edit-experience').attr('data-id', data.experience_id);
                itemRow.find('.delete-experience').attr('data-id', data.experience_id);
            }else if(data.result == 'updated'){
                var itemRow = $('#experience-row-'+data.experience_id);
            }
            itemRow.find('.experience-organization').html(data.organization);
            itemRow.find('.experience-position').html(data.position);
            itemRow.find('.experience-achievements').html(data.achievements);
            itemRow.find('.experience-website-link').html('<a href="'+data.website+'">'+data.website+'</a>');
            itemRow.find('.experience-begin').html(data.begin_str);
            itemRow.find('.experience-end').html(data.end_str);
            itemRow.find('.experience-city').html(data.city_title);

            itemRow.find('.edit-experience').attr('data-website',    data.website).
                                             attr('data-city_id',    data.city).
                                             attr('data-for-now',    data.for_now).
                                             attr('data-end-stamp',  data.end_stamp).
                                             attr('data-end-date',   data.end_date).
                                             attr('data-begin-stamp',data.begin_stamp).
                                             attr('data-begin-date', data.begin_date);


            if(data.result == 'added'){
                itemRow.appendTo('#experienceContainer').slideDown('slow');
            }

            return;
        }
    }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });

});


$( document ).on( "click", ".delete-experience", function(e) {
    e.preventDefault();

    if(resProcessing) return false;

    var experience_id = $(this).attr('data-id');
    var parent = $('#experience-row-' + experience_id);

    var lLeft = parent.width() / 2 - 110;
    var lTop = parent.height() / 2 + 9;
    if ((lTop + 19) > parent.height()) lTop = 2;
    var bgspan = $('<span>', {
        id: "loading",
        class: "loading"
    })  .css('position', 'absolute')
        .css('left',lLeft + 'px')
        .css('top', lTop  + 'px');
    bgspan.html('<img src="images/spinner.gif" alt="loading"/>');
    parent.append(bgspan).css('position', 'relative');
    parent.css('opacity', '0.6');


    var act = 'delete_experience';
    var x = $('input[name="x"]').val();
    var resume_id = $('input[name="rid"]').val();

    resProcessing = true;

    $.post("index.php?e=personal&m=user&a=ajxResumeEdit",
        { rid: resume_id, experience_id: experience_id, act: act, x:x }, function( data ) {
            resProcessing = false;
            if(data.error != ''){
                alert(data.error);
                parent.css('opacity', '1');
                bgspan.remove();
            }else{
                if(data.act == 'reload') window.location.reload();
                parent.css('opacity', '1');
                bgspan.remove();

                // Удаление строки с местом работы
                parent.slideUp( "slow", function() { parent.remove(); });
                return;
            }
        }, "json")
        .fail(function( data ){
            resProcessing = false;
            parent.css('opacity', '1');
            bgspan.remove();
            alert('A error occurred. Please try again later.');
        });
});
// ==== /Опыт работы ====