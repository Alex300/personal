<!-- BEGIN: MAIN -->
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-md-2 col-md-push-10">{LIST_USER_ID|p30_userInfo($this)}</div>

    <div class="col-xs-12 col-md-10 col-md-pull-2">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        {USER_ID|p30_userTabs($this, 'vacancy')}

        <div>
            <span class="text-muted">{PHP.L.personal_vacancy_manage}</span> |
            <a href="{PHP|cot_url('personal', 'm=user&a=profileEdit')}">{PHP.L.personal_employer_info}</a>
        </div>

        <h1>{PAGE_TITLE}</h1>

        <!-- фильтры -->

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <!-- IF {LIST_SUBMITNEW_URL} -->
        <div class="text-right marginbottom10">
            <a href="{LIST_SUBMITNEW_URL}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>
                {PHP.L.personal_vacancy_add}</a>
        </div>
        <div class="clearfix"></div>
        <!-- ENDIF -->

        <!-- IF {LIST_PAGINATION} -->
        <div class="pagination text-right" style="margin-top: 5px">
            {LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT}
        </div>
        <!-- ENDIF -->

        <div id="vacancy-list">
            <!-- BEGIN: VACANCY_ROW -->
            <article class="list-row row">
                <header class="col-xs-12">
                    <h2>
                        № {VACANCY_ROW_ID}: <a href="{VACANCY_ROW_URL}" title="{VACANCY_ROW_TITLE}" rel="bookmark">{VACANCY_ROW_TITLE}</a>
                    </h2>
                </header>

                <div class="col-xs-12 col-sm-8">
                    {PHP.L.personal_state}:
                    <!-- IF {VACANCY_ROW_ACTIVE} -->
                        <span class="text-success strong">{PHP.L.personal_vacancy_state_on}</span>
                    {PHP.L.Until} {VACANCY_ROW_ACTIVE_TO_DATE}
                    <!-- ELSE -->
                        <span class="text-danger strong">{PHP.L.personal_vacancy_state_off}</span>
                    <!-- ENDIF -->
                    <br />{PHP.L.personal_vacancy_created} {VACANCY_ROW_CREATE_DATE}
                    <!-- IF {VACANCY_ROW_ACTIVATED} -->
                    / {PHP.L.Begin} {VACANCY_ROW_ACTIVATED_DATE}
                    <!-- ENDIF -->
                    <!-- IF {PHP.usr.isadmin} OR {VACANCY_ROW_USER_ID} == {PHP.usr.id} -->
                    <br />{PHP.L.Views}: {VACANCY_ROW_VIEWS}
                    <!-- ENDIF -->
                </div>
                <!-- IF {VACANCY_ROW_EDIT_URL} -->
                <footer class="col-xs-12 col-sm-4 text-right vacancyPanel">
                    <!-- IF {VACANCY_ROW_ACTIVE} -->
                        <a href="#" class="btn btn-danger btn-sm" title="{PHP.L.personal_vacancy_off}" data-toggle="tooltip"
                           data-id="{VACANCY_ROW_ID}" data-act="deactivate"><span class="glyphicon glyphicon-off"></span></a>
                    <!-- ELSE -->
                        <a href="#" class="btn btn-default btn-sm" title="{PHP.L.personal_vacancy_on}" data-toggle="tooltip"
                           data-id="{VACANCY_ROW_ID}" data-act="activate"><span class="glyphicon glyphicon-off"></span></a>
                    <!-- ENDIF -->

                    <a href="#" class="btn btn-default btn-sm" title="{PHP.L.personal_vacancy_up}" data-toggle="tooltip"
                       data-id="{VACANCY_ROW_ID}" data-act="up"><span class="glyphicon glyphicon-arrow-up"></span></a>

                    <a href="{VACANCY_ROW_EDIT_URL}" class="btn btn-default btn-sm"
                       title="{PHP.L.Edit}" data-toggle="tooltip"><span class="glyphicon glyphicon-edit"></span></a>

                    <a href="#" class="btn btn-danger btn-sm" title="{PHP.L.Delete}" data-toggle="tooltip"
                       data-id="{VACANCY_ROW_ID}" data-act="delete"><span class="glyphicon glyphicon-trash"></span></a>
                </footer>
                <!-- ENDIF -->
            </article>
            <!-- END: VACANCY_ROW -->

            <!-- BEGIN: EMPTY -->
            <h4 class="text-muted text-center">{PHP.L.None}</h4>
            <!-- END: EMPTY -->

        </div>

        <!-- IF {LIST_PAGINATION} -->
        <div class="pagination text-right">
            {LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT}
        </div>
        <!-- ENDIF -->
    </div>
</div>

<!-- IF {LIST_TOTALLINES} > 0 -->
<script>
    var processing = false;

    function sendPost(elem){
        'use strict';

        var parent = elem.parents('.list-row');

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
        $('#vacancy-list').css('opacity', '0.6');

        var vid = elem.attr('data-id');
        var act = elem.attr('data-act');

        processing = true;
        $.post( "{PHP|cot_url('personal','m=user&a=ajxVacancyEdit', '', 1)}", { vid: vid, act: act, x:'{PHP.sys.xk}' }, function( data ) {
            processing = false;
            if(data.error != ''){
                alert(data.error);
            }else{
                if(data.act == 'reload') window.location.reload();
                return;
            }
            $('#vacancy-list').css('opacity', '1');
            bgspan.remove();
        }, "json")
                .fail(function( data ){
                    processing = false;
                    $('#vacancy-list').css('opacity', '1');
                    bgspan.remove();
                    alert('A error occurred. Please try again later.');
                });
    }

    $('.vacancyPanel a').click(function(e){
        'use strict';

        if($(this).attr('href') == '#'){
            e.preventDefault();
            if(processing) return false;
            $.smartDialog({type: 'bootstrap'});

            var elem = $(this);
            var act = elem.attr('data-act');
            switch(act){

                case 'deactivate':
                    $.smartDialog('show',{
                        title:'{PHP.L.personal_vacancy_off}',
                        text: '{PHP.L.personal_vacancy_offConfirm}',
                        buttons:[
                                { text: '{PHP.L.personal_turn_off}', click: function() { sendPost(elem); $.smartDialog('close'); } },
                                { text: '{PHP.L.Cancel}', click: function() { $.smartDialog('close'); } }
                        ]
                    });
                    break;

                case 'activate':
                    $.smartDialog('show',{
                        title:'{PHP.L.personal_vacancy_on}',
                        text: '{PHP.L.personal_vacancy_onConfirm}',
                        buttons:[
                                { text: '{PHP.L.Yes}', click: function() { sendPost(elem); $.smartDialog('close'); } },
                                { text: '{PHP.L.Cancel}', click: function() { $.smartDialog('close'); } }
                        ]
                    });
                    break;

                case 'makeunhot':
                    $.smartDialog('show',{
                        title:'Снятие статуса «горячей» с вакансии',
                        text: 'Вы действительно желаете снять статус «горячей» с вакансии?',
                        buttons:[
                                { text: '{PHP.L.Yes}', click: function() { sendPost(elem); $.smartDialog('close'); } },
                                { text: '{PHP.L.Cancel}', click: function() { $.smartDialog('close'); } }
                        ]
                    });
                    break;

                case 'makehot':
                    $.smartDialog('show',{
                        title:'Присвоение статуса «горячей» для вакансии',
                        text: 'Вы действительно желаете сделать вакансию «горячей»?',
                        buttons:[
                                { text: '{PHP.L.Yes}', click: function() { sendPost(elem); $.smartDialog('close'); } },
                                { text: '{PHP.L.Cancel}', click: function() { $.smartDialog('close'); } }
                        ]
                    });
                    break;

                case 'up':
                    $.smartDialog('show',{
                        title:'{PHP.L.personal_vacancy_up}',
                        text: '{PHP.L.personal_vacancy_upConfirm}',
                        buttons:[
                                { text: '{PHP.L.Yes}', click: function() { sendPost(elem); $.smartDialog('close'); } },
                                { text: '{PHP.L.Cancel}', click: function() { $.smartDialog('close'); } }
                        ]
                    });
                    break;

                case 'delete':
                    $.smartDialog('show',{
                        title:'{PHP.L.Delete}',
                        text: '{PHP.L.personal_vacancy_deleteConfirm}',
                        'class': 'alert alert-danger',
                        buttons:[
                                { text: '{PHP.L.Yes}', click: function() { sendPost(elem); $.smartDialog('close'); } },
                                { text: '{PHP.L.Cancel}', click: function() { $.smartDialog('close'); } }
                        ]
                    });
                    break;

                default:
                    sendPost(elem);
            }
        }
    });
</script>
<!-- ENDIF -->
<!-- END: MAIN -->
