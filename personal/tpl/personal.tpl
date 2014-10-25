<!-- BEGIN: MAIN -->
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-sm-3 col-sm-push-9">
        <!-- IF {PHP.usr.auth_write} -->
        <a href="{PHP|cot_url('personal', 'm=user&a=resumeEdit')}" class="btn btn-default width100" ><span
                class="fa fa-file-text"></span> {PHP.L.personal_resume_add}</a>
        <a href="{PHP|cot_url('personal', 'm=user&a=vacancyEdit')}" class="btn btn-default width100 margintop10"><span
                class="fa fa-clipboard"></span> {PHP.L.personal_vacancy_add}</a>
        <!-- ENDIF -->

        <div class="jumbotron padding10 margintop20 hidden-xs">
            <h4 style="margin-top: 0"><a href="{PHP|cot_url('personal', 'a=resume')}">{PHP.L.personal_resume_catalog}</a></h4>
            <p style="font-size: inherit">{PHP.L.personal_resume_catalog_desc}</p>
            <a href="{PHP|cot_url('personal', 'a=resume')}">{PHP.L.personal_find_resumes} »</a>
        </div>

        <div class="jumbotron padding10 margintop20 hidden-xs">
            <h4 style="margin-top: 0"><a href="{PHP|cot_url('personal', 'a=vacancy')}">{PHP.L.personal_vacancy_catalog}</a></h4>
            <p style="font-size: inherit">{PHP.L.personal_vacancy_catalog_desc}</p>
            <a href="{PHP|cot_url('personal', 'a=vacancy')}">{PHP.L.personal_find_vacancies} »</a>
        </div>
    </div>

    <div class="col-xs-12 col-sm-9 col-sm-pull-3">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        <h1>{PAGE_TITLE}</h1>


        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <!-- Поиск -->
        <div class="well well-sm">
            <form id="searchForm" class="" method="get" action="{PHP|cot_url('personal', 'a=vacancy')}">
                <input type="hidden" id="searchTypeA" name="a" value="vacancy" />
                <div class="row">
                    <div class="col-xs-12 col-sm-5">
                        <input id="kwtf" class="form-control" style="" name="f[kw]" type="text" placeholder="{PHP.L.personal_type_job_title}" />
                    </div>
                    <div class="col-xs-12 col-sm-5">
                        {FILTER_CITY}
                    </div>
                    <div class="col-xs-12 col-sm-2">
                        <button class="btn btn btn-default marginleft10" type="submit"><span class="glyphicon glyphicon-search"></span>
                            {PHP.L.Search}</button>
                    </div>
                </div>

                <div class="lhn" style="margin-top: 5px">
                    <div class="pull-left" style="padding-top: 2px;"><a href="#" id="chooseCat"
                            class="caret-closed strong">{PHP.L.personal_category_select}</a></div>

                    <div class="pull-left" style="margin-left: 106px">
                        <div class="form-inline">
                            <div class="radio">
                                <label class="">
                                    <input class="searchType" type="radio" name="p" value="vacancy" checked="checked" />
                                    {PHP.L.personal_vacancies}
                                </label>
                            </div>

                            <div class="radio" style="margin-left: 20px">
                                <label>
                                    <input class="searchType" type="radio" name="p"  value="resume" />
                                    {PHP.L.personal_resumes}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pull-right" style="padding-top: 2px;">
                        <a id="searchExtended" href="{PHP|cot_url('personal', 'a=vacancy')}" class="strong">{PHP.L.personal_extended_search}</a>
                    </div>
                    <div class="clearfix"></div>

                    <div id="vacancyCats" style="display: none; margin: 10px 0 20px 0">
                        {PHP|personal_select_tree('f[cat]',0,'personal_model_Category')}
                        <div class="clearfix"></div>
                    </div>

                </div>
            </form>
        </div>
        <!-- Поиск -->

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <h3>{PHP.L.personal_new_resumes}</h3>
                {PHP|personal_resumeList('personal.resumelist',5)}
                <a href="{PHP|cot_url('personal', 'a=resume')}">{PHP.L.personal_all_resumes}...</a>
            </div>

            <div class="col-xs-12 col-sm-6">
                <h3>{PHP.L.personal_new_vacancies}</h3>
                {PHP|personal_vacancyList('personal.vacancylist',5)}
                <a href="{PHP|cot_url('personal', 'a=vacancy')}">{PHP.L.personal_all_vacancies}...</a>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleBlock(block, elem){
        elem = elem || null;
        $(block).slideToggle(function(){
            if(elem){
                if($(block).css('display') == 'block' && $(elem).hasClass('caret-closed')){
                    $(elem).removeClass('caret-closed').addClass('caret-open');
                }else if($(block).css('display') == 'none' && $(elem).hasClass('caret-open')){
                    $(elem).removeClass('caret-open').addClass('caret-closed');
                }
            }
        });

        return false;
    }

    $('#chooseCat').click(function(){
        if($(this).hasClass('disabled') && $("#vacancyCats").css('display') != 'block'){
            return false;
        }
        toggleBlock("#vacancyCats", $(this));
        return false;
    });

    // Тип поиска "Вакансии / резюме"
    $('.searchType').change(function(){
        var val = $(this).val();
        $('#searchTypeA').val(val);
        if(val == 'resume'){
            $('#searchForm').attr('action', "{PHP|cot_url('personal', 'a=resume')}");
            $('#searchExtended').attr('href', "{PHP|cot_url('personal', 'a=resume')}");
        }else{
            $('#searchForm').attr('action', "{PHP|cot_url('personal', 'a=vacancy')}");
            $('#searchExtended').attr('href', "{PHP|cot_url('personal', 'a=vacancy')}");
        }
    });

    $('#searchForm').submit(function(){
        $('.searchType').attr('disabled', 'disabled');
    });
</script>
<!-- END: MAIN -->
