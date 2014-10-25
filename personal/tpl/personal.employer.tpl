<!-- BEGIN: MAIN -->
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-sm-2 col-sm-push-10">

    </div>

    <div class="col-xs-12 col-sm-10 col-sm-pull-2">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}


        <div class="row margintop20">
            <div class="col-xs-12 col-sm-8">
                <header>
                    <h1 style="margin-top: 0">{PAGE_TITLE}</h1>
                </header>

                <!-- IF {EMPLOYER_SITE_URL} -->
                <div>
                    <a rel="nofollow" href="{EMPLOYER_SITE_URL}" target="_blank">{EMPLOYER_SITE}</a>
                </div>
                <!-- ENDIF -->

                <!-- IF {EMPLOYER_TYPE} == 1 -->
                <div class="italic">{PHP.L.personal_profile_type_1}</div>
                <!-- ENDIF -->

                <!-- IF {EMPLOYER_ADDRESS} -->
                <div class="row margintop10">
                    <div class="col-xs-5 col-sm-4">{PHP.L.personal_profile_adress}:</div>
                    <div class="col-xs-7 col-sm-8">{EMPLOYER_ADDRESS}</div>
                </div>
                <!-- ENDIF -->

                <!-- IF {EMPLOYER_PHONE} -->
                <div class="row margintop10">
                    <div class="col-xs-5 col-sm-4">{PHP.L.personal_phone_s}:</div>
                    <div class="col-xs-7 col-sm-8">{EMPLOYER_PHONE}</div>
                </div>
                <!-- ENDIF -->

                <!-- IF {EMPLOYER_EMAIL} -->
                <div class="row margintop10">
                    <div class="col-xs-5 col-sm-4">{PHP.L.Email}:</div>
                    <div class="col-xs-7 col-sm-8">{EMPLOYER_EMAIL}</div>
                </div>
                <!-- ENDIF -->
            </div>


            <div class="col-xs-12 col-sm-4 text-right">
                <!-- IF {EMPLOYER_ID} > 0 AND {EMPLOYER_ID|cot_files_count('personal_empl_profile', $this, '', 'images')} > 0 -->
                <img class="avatar img-responsive marginbottom10" alt="{EMPLOYER_TITLE}" style="display: inline-block"
                     src="{EMPLOYER_ID|cot_files_get('personal_empl_profile',$this,'')|cot_files_thumb($this,160,160,'auto')}" />
                <!-- ENDIF -->

                <!-- IF {EMPLOYER_CAN_EDIT} -->
                <div>
                <a class="btn btn-info" href="{EMPLOYER_EDIT_URL}"><span class="glyphicon glyphicon-edit"></span>
                    {PHP.L.Edit}</a>
                <!-- ENDIF -->
                </div>
            </div>
        </div>

        <!-- IF {EMPLOYER_TEXT} -->
        <h4 class="widget-title" style="margin-top: 20px">{PHP.L.personal_profile_text}</h4>
        <div>{EMPLOYER_TEXT}</div>
        <!-- ENDIF -->


        <h4 class="widget-title" style="margin: 30px 0 20px 0">{PHP.L.personal_profile_vacancies}</h4>
        <!-- IF {EMPLOYER_CAN_EDIT} -->
        <div class="text-right">
            <a href="{ADD_VACANCY_URL}" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>
                {PHP.L.personal_vacancy_add}</a>
        </div>
        {EMPLOYER_USER_ID|personal_vacancyList('personal.vacancylistEmployer',0,'','user_id=$this')}

    </div>
</div>
<!-- END: MAIN -->