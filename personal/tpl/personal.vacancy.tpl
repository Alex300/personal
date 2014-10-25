<!-- BEGIN: MAIN -->
{PHP|p30_setFormElementClass('col-xs-7 col-sm-9','col-xs-5 col-sm-3')}
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-sm-2 col-sm-push-10">

    </div>

    <div class="col-xs-12 col-sm-10 col-sm-pull-2">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        <!-- IF {PHP.env.location} == 'personal.vacancy_preview' -->{USER_ID|p30_userTabs($this, 'vacancy')}<!-- ENDIF -->

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <header>
            <h1 style="margin-top: 0">{VACANCY_TITLE}</h1>
            <div class="jumbotron" style="padding: 15px">
                <div class="row">
                    <div class="col-xs-12 col-sm-3 col-sm-push-9">
                        <!-- IF {VACANCY_EMPL_PROFILE_ID} > 0 AND {VACANCY_EMPL_PROFILE_ID|cot_files_count('personal_empl_profile', $this, '', 'images')} > 0 -->
                        <img class="avatar img-responsive" alt="{VACANCY_EMPL_PROFILE_TITLE}"
                             src="{VACANCY_EMPL_PROFILE_ID|cot_files_get('personal_empl_profile',$this,'')|cot_files_thumb($this,160,160,'auto')}" />
                        <!-- ELSE -->
                        {VACANCY_USER_AVATAR}
                        <!-- ENDIF -->
                    </div>

                    <div class="col-xs-12 col-sm-9 col-sm-pull-3">
                        <h3 style="margin-top: 0">
                            <!-- IF {VACANCY_EMPL_PROFILE_ID} > 0 -->
                            <a href="{VACANCY_EMPL_PROFILE_URL}">{VACANCY_EMPL_PROFILE_TITLE}</a>
                            <!-- ELSE -->
                            <a href="{VACANCY_USER_DETAILSLINK}">{VACANCY_USER_DISPLAY_NAME}</a>
                            <!-- ENDIF -->
                        </h3>

                        <!-- IF {VACANCY_CAN_EDIT} -->
                            <!-- IF {VACANCY_ACTIVE} == 0 OR {VACANCY_HOT} == 1 -->
                            <div class="marginbottom10">
                                <!-- IF {VACANCY_ACTIVE} == 0 -->
                                <span class="text-danger">{PHP.L.personal_vacancy_state_off}</span>
                                <!-- ENDIF -->
                                <!-- IF {VACANCY_HOT} == 1 -->
                                <span class="text-danger">Горячее</span>
                                <!-- ENDIF -->
                            </div>
                            <!-- ENDIF -->
                        <!-- ENDIF -->

                        <div class="row" style="font-size: 14px">
                            <div class="col-xs-6">
                                <span class="text-muted">{PHP.L.personal_salary}:</span>
                                    <!-- IF {VACANCY_SALARY} > 0 -->
                                    <h4 style="white-space: nowrap">{PHP.L.personal_from} {VACANCY_SALARY|number_format($this, 0, '.', ' ')} {PHP.L.personal_money_per_month}</h4>
                                    <!-- ELSE -->
                                    <strong>{PHP.L.personal_negotiated}</strong>
                                    <!-- ENDIF -->
                            </div>

                            <!-- IF {VACANCY_CITY} > 0 -->
                            <div class="col-xs-6">
                                <span class="text-muted">{PHP.L.Location}:</span>
                                    <strong>{VACANCY_CITY_NAME}</strong><!-- IF {VACANCY_DISTRICT} -->, {VACANCY_DISTRICT}<!-- ENDIF -->
                            </div>
                            <!-- ENDIF -->
                        </div>

                        <hr />

                        <!-- IF {VACANCY_CONTACT_FACE} -->
                        <span class="fa fa-user"></span>
                        <span class="text-muted">{PHP.L.personal_contact_face}:</span> {VACANCY_CONTACT_FACE}
                        <!-- ENDIF -->

                        <!-- IF {VACANCY_PHONE} -->
                        &nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-earphone"></span> {VACANCY_PHONE}
                        <!-- ENDIF -->

                        <!-- IF {VACANCY_EMPL_PROFILE_EMAIL} -->
                        &nbsp;&nbsp;&nbsp;<span class="fa fa-envelope-o"></span> <a href="mailto:{RESUME_EMAIL}">{VACANCY_EMPL_PROFILE_EMAIL}</a>
                        <!-- ENDIF -->

                        <!-- IF {PHP.env.location} == 'personal.vacancy' -->
                        <div class="margintop10 text-right">
                            <a href="{VACANCY_USER_ID|cot_url('pm', 'm=send&to=$this')}" class="btn btn-default btn-sm"><span
                                    class="glyphicon glyphicon-envelope"></span> {PHP.L.personal_send_message}</a>
                        </div>
                        <!-- ENDIF -->
                    </div>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <!-- IF {VACANCY_CATEGORY_RAW} -->
                <div class="row margintop10">
                    <div class="col-xs-12">
                        <div id="treebox-{VACANCY_ID}" style="text-align: left;"></div>
                    </div>
                </div>
                <!-- ENDIF -->

            </div>

            <!-- IF {VACANCY_CAN_EDIT} -->
            <div class="col-xs-12 col-sm-4 text-right">
                <a class="btn btn-info" href="{VACANCY_EDIT_URL}"><span class="glyphicon glyphicon-edit"></span>
                    {PHP.L.Edit}</a>
            </div>
            <!-- ENDIF -->
        </div>

        <!-- IF {VACANCY_STAFF} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_staff}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_STAFF}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_EDUCATION_LEVEL} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_education_level}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_EDUCATION_LEVEL}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_EXPERIENCE} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_experience}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_EXPERIENCE}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_EMPLOYMENT} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_employment}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_EMPLOYMENT}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_SCHEDULE} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_schedule}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_SCHEDULE}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {PHP.env.location} == 'personal.vacancy_preview' -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_vacancy_created}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_CREATE_DATE}</div>
        </div>
        <div class="row">
            <div class="{PHP.p30.labelClass}">{PHP.L.Updated}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_UPDATE_DATE}</div>
        </div>
        <div class="row">
            <div class="{PHP.p30.labelClass}">{PHP.L.Views}:</div>
            <div class="{PHP.p30.elementClass}">{VACANCY_VIEWS}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_TEXT} -->
        <h4 class="widget-title" style="margin-top: 20px">{PHP.L.personal_vacancy_text}</h4>
        <div>{VACANCY_TEXT}</div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_SKILLS} -->
        <h4 class="widget-title" style="margin-top: 20px">{PHP.L.personal_vacancy_skills}</h4>
        <div>{VACANCY_SKILLS}</div>
        <!-- ENDIF -->

        <!-- IF {VACANCY_ID|cot_files_count('personal_vacancy', $this)} > 0 -->
        <h4 class="widget-title" style="margin-top: 20px">{PHP.L.files_attachments}</h4>
        <div>{VACANCY_ID|cot_files_display('personal_vacancy',$this,'', 'files.downloads')}</div>
        <!-- ENDIF -->
    </div>
</div>
<!-- END: MAIN -->