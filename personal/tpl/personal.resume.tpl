<!-- BEGIN: MAIN -->
{PHP|p30_setFormElementClass('col-xs-7 col-sm-9','col-xs-5 col-sm-3')}
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-sm-2 col-sm-push-10">
        <!-- IF {PHP.env.location} == 'personal.resume_preview' -->{USER_ID|p30_userInfo($this)}<!-- ENDIF -->
    </div>

    <div class="col-xs-12 col-sm-10 col-sm-pull-2">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        <!-- IF {PHP.env.location} == 'personal.resume_preview' -->{USER_ID|p30_userTabs($this, 'resume')}<!-- ENDIF -->

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <header>
            <div class="jumbotron" style="padding: 15px">
                <div class="row">
                    <div class="col-xs-12 col-sm-3 col-sm-push-9">
                        <!-- IF {RESUME_ID|cot_files_count('personal_resume', $this, '', 'images')} > 0 -->
                        <img class="avatar img-responsive" alt="{RESUME_TITLE}"
                             src="{RESUME_ID|cot_files_get('personal_resume',$this,'')|cot_files_thumb($this,160,160,'crop')}" />
                        <!-- ELSE -->
                            {USER_AVATAR}
                        <!-- ENDIF -->
                    </div>

                    <div class="col-xs-12 col-sm-9 col-sm-pull-3">
                        <h2 style="margin-top: 0">{USER_DISPLAY_NAME}</h2>

                        <!-- IF {RESUME_CAN_EDIT} -->
                            <!-- IF {RESUME_ACTIVE} == 0 OR {RESUME_HOT} == 1 OR {RESUME_DENY_UNREGISTER} == 1 -->
                            <div class="marginbottom10">
                                <!-- IF {RESUME_ACTIVE} == 0 -->
                                <span class="text-danger">{PHP.L.personal_resume_state_off}</span>
                                <!-- ENDIF -->
                                <!-- IF {RESUME_HOT} == 1 -->
                                <span class="text-danger">Горячее</span>
                                <!-- ENDIF -->
                                <!-- IF {RESUME_DENY_UNREGISTER} == 1 -->
                                <span class="text-danger">Скрыто от незарегистрированных пользователей</span>
                                <!-- ENDIF -->
                            </div>
                            <!-- ENDIF -->
                        <!-- ENDIF -->

                        <!-- IF {USER_AGE} > 0 -->
                        <strong>{USER_BIRTHDATE_STAMP|p30_friendlyAge($this)}</strong>
                        ({USER_BIRTHDATE_STAMP|cot_date('date_text', $this)})
                        <!-- ENDIF -->

                        <!-- IF {USER_GENDER_RAW} == 'F' OR {USER_GENDER_RAW} == 'M' -->
                        &nbsp;&nbsp;&nbsp;
                        {PHP.L.Gender}: <img src="/themes/{PHP.theme}/img/gender_{USER_GENDER_RAW}.gif" style="margin-top: -3px;" />
                        <strong>{USER_GENDER}</strong>
                        <!-- ENDIF -->

                        <!-- IF {RESUME_CITY} > 0 -->
                            &nbsp;&nbsp;&nbsp;<strong>{RESUME_CITY_NAME}</strong><!-- IF {RESUME_DISTRICT} != '' -->, {RESUME_DISTRICT}<!-- ENDIF --><!-- IF {RESUME_LEAVING} -->, {PHP.L.personal_resume_leaving}<!-- ENDIF -->
                        <!-- ENDIF -->
                        <hr />

                        <!-- IF {RESUME_PHONE} -->
                        <span class="glyphicon glyphicon-earphone"></span> {RESUME_PHONE}
                        <!-- ENDIF -->

                        <!-- IF {RESUME_EMAIL} -->
                            &nbsp;&nbsp;&nbsp;<span class="fa fa-envelope-o"></span> <a href="mailto:{RESUME_EMAIL}">{RESUME_EMAIL}</a>
                        <!-- ENDIF -->
                        <!-- IF {USER_ICQ} -->
                        &nbsp;&nbsp;&nbsp;
                        <a href="/go.php?www.icq.com/{USER_ICQ}#pager"><img src="/go.php?www.icq.com/whitepages/online?icq={USER_ICQ}&amp;img=5"
                                                                            class="icon" alt="" /></a>
                        <a href="/go.php?www.icq.com/{USER_ICQ}#pager">{USER_ICQ}</a>
                        <!-- ENDIF -->
                        <!-- IF {USER_SKYPE} -->&nbsp;&nbsp;&nbsp;<span class="fa fa-skype"></span> {USER_SKYPE}<!-- ENDIF -->
                        <!-- IF {USER_WEBSITE} -->
                        &nbsp;&nbsp;&nbsp;<span class="fa fa-laptop"></span>
                        <noindex>
                            <a href="/go.php?{USER_WEBSITE}" rel="nofollow" target="_blank">{USER_WEBSITE}</a>
                        </noindex>
                        <!-- ENDIF -->

                        <!-- IF {RESUME_LEAVING} -->
                        <div class="margintop10">
                        {PHP.L.personal_resume_leaving}: <strong>{RESUME_LEAVING_NAME}</strong>
                        </div>
                        <!-- ENDIF -->

                        <!-- IF {RESUME_OTHER_CONTACTS} -->
                        <div id="resume_other_contacts">{RESUME_OTHER_CONTACTS}</div>
                        <!-- ENDIF -->

                        <!-- IF {PHP.env.location} == 'personal.resume' -->
                        <div class="margintop10 text-right">
                            <a href="{USER_ID|cot_url('pm', 'm=send&to=$this')}" class="btn btn-default btn-sm"><span
                                    class="glyphicon glyphicon-envelope"></span> {PHP.L.personal_send_message}</a>
                        </div>
                        <!-- ENDIF -->
                    </div>
                </div>
            </div>

            <h1>{PAGE_TITLE}</h1>
        </header>

        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <!-- IF {RESUME_SALARY} > 0 -->
                <h3>{PHP.L.personal_from} {RESUME_SALARY|number_format($this, 0, '.', ' ')} {PHP.L.personal_money_per_month}</h3>
                <!-- ELSE -->
                <h4>{PHP.L.personal_salary} {PHP.L.personal_negotiated}</h4>
                <!-- ENDIF -->

                <!-- IF {RESUME_CATEGORY_RAW} -->
                <div class="row margintop10">
                    <div class="col-xs-12">
                        <div id="treebox-{RESUME_ID}" style="text-align: left;"></div>
                    </div>
                </div>
                <!-- ENDIF -->

            </div>
            <!-- IF {RESUME_CAN_EDIT} -->
            <div class="col-xs-12 col-sm-4 text-right">
                <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                <a class="btn btn-info" href="{RESUME_EDIT_URL}"><span class="glyphicon glyphicon-edit"></span>
                    {PHP.L.personal_resume_edit_main}</a>
                <!-- ELSE -->
                <a class="btn btn-info" href="{RESUME_PREVIEW_URL}"><span class="glyphicon glyphicon-edit"></span>
                    {PHP.L.Edit}</a>
                <!-- ENDIF -->
            </div>
            <!-- ENDIF -->
        </div>

        <!-- IF {RESUME_STAFF} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_staff}:</div>
            <div class="{PHP.p30.elementClass}">{RESUME_STAFF}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {RESUME_EMPLOYMENT} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_employment}:</div>
            <div class="{PHP.p30.elementClass}">{RESUME_EMPLOYMENT}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {RESUME_SCHEDULE} -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_schedule}:</div>
            <div class="{PHP.p30.elementClass}">{RESUME_SCHEDULE}</div>
        </div>
        <!-- ENDIF -->

        <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_resume_created}:</div>
            <div class="{PHP.p30.elementClass}">{RESUME_CREATE_DATE}</div>
        </div>
        <div class="row">
            <div class="{PHP.p30.labelClass}">{PHP.L.Updated}:</div>
            <div class="{PHP.p30.elementClass}">{RESUME_UPDATE_DATE}</div>
        </div>
        <div class="row">
            <div class="{PHP.p30.labelClass}">{PHP.L.Views}:</div>
            <div class="{PHP.p30.elementClass}">{RESUME_VIEWS}</div>
        </div>
        <!-- ENDIF -->

        <h4 class="widget-title" style="margin-top: 20px">{PHP.L.personal_education}</h4>
        <div class="row">
            <div class="{PHP.p30.labelClass}">{PHP.L.personal_education_level}:</div>
            <div class="{PHP.p30.elementClass}">
                <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                <button id="add-edu" class="btn btn-default btn-xs addToolTip" title="{PHP.L.personal_education_add}"
                        style="margin-right: 20px"><span class="fa fa-plus"></span></button>
                <!-- ENDIF -->
                {RESUME_EDUCATION_LEVEL}
            </div>
        </div>
        <div id="eduContainer">
            <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
            <div id="edu-row-tpl" class="row education-row margintop10" style="display: none">
                <div class="col-xs-4 col-sm-3 edu-year"></div>
                <div class="col-xs-7 col-sm-5">
                    <strong class="edu-title"></strong><br />
                    <span class="desc edu-level-title"></span><br />
                    <span class="desc edu-faculty"></span>,
                    <span class="desc edu-specialty"></span>
                </div>
                <div class="col-xs-1 col-sm-1">
                    <a href="#" class="edit-edu" title="{PHP.L.Edit}" data-toggle="tooltip" data-id="" data-level_id=""><span
                            class="glyphicon glyphicon-edit text"></span></a>

                    <a href="#" class="delete-edu" title="{PHP.L.Delete}" data-toggle="tooltip" data-id=""><span
                            class="glyphicon glyphicon-remove text-danger"></span></a>
                </div>
            </div>
            <!-- ENDIF -->
            <!-- BEGIN: RESUME_EDU_ROW -->
            <div id="edu-row-{RESUME_EDU_ROW_ID}" class="row education-row margintop10">
                <div class="col-xs-4 col-sm-3 edu-year">{RESUME_EDU_ROW_YEAR}</div>
                <div class="col-xs-7 col-sm-5">
                    <strong class="edu-title">{RESUME_EDU_ROW_TITLE}</strong><br />
                    <span class="desc edu-level-title">{RESUME_EDU_ROW_LVL_TITLE}</span><br />
                    <span class="desc edu-faculty">{RESUME_EDU_ROW_FACULTY}</span>,
                    <span class="desc edu-specialty">{RESUME_EDU_ROW_SPECIALTY}</span>
                </div>
                <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                <div class="col-xs-1 col-sm-1">
                    <a href="#" class="edit-edu" title="{PHP.L.Edit}" data-toggle="tooltip" data-id="{RESUME_EDU_ROW_ID}"
                            data-level_id="{RESUME_EDU_ROW_LVL_ID}"><span
                            class="glyphicon glyphicon-edit text"></span></a>

                    <a href="#" class="delete-edu" title="{PHP.L.Delete}" data-toggle="tooltip" data-id="{RESUME_EDU_ROW_ID}"><span
                            class="glyphicon glyphicon-remove text-danger"></span></a>
                </div>
                <!-- ENDIF -->
            </div>
            <!-- END: RESUME_EDU_ROW -->
        </div>

        <div class="row margintop10">
            <div class="{PHP.p30.labelClass}"><h5>{PHP.L.personal_langs}:</h5></div>
            <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
            <div class="{PHP.p30.elementClass}">
                <button class="btn btn-default btn-xs addToolTip" title="{PHP.L.personal_langs_add}" data-toggle="modal"
                        data-target="#languageModal"><span class="fa fa-plus"></span></button>
            </div>
            <!-- ENDIF -->
        </div>
        <div id="langLevelContainer">
            <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
            <div id="langLevel-tpl" class="row langLevel-row" style="display: none">
                <div class="col-xs-4 col-sm-3 strong langLevel-lang" data-id="">
                    <span class="langLevel-lang-title"></span>:
                </div>
                <div class="col-xs-7 col-sm-4 langLevel-level"></div>
                <div class="col-xs-1 col-sm-1">
                    <a href="#" class="delete-lang" title="{PHP.L.Delete}" data-toggle="tooltip" data-id=""><span
                                class="glyphicon glyphicon-remove text-danger"></span></a>
                </div>
            </div>
            <!-- ENDIF -->
            <!-- BEGIN: RESUME_LANG_ROW -->
            <div id="langLevel-row-{RESUME_LANG_ROW_ID}" class="row langLevel-row">
                <div class="col-xs-4 col-sm-3 strong langLevel-lang" data-id="{RESUME_LANG_ROW_LANG_ID}">
                    <span class="langLevel-lang-title">{RESUME_LANG_ROW_TITLE}</span>:
                </div>
                <div class="col-xs-7 col-sm-4 langLevel-level">{RESUME_LANG_ROW_LVL_TITLE}</div>
                <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                <div class="col-xs-1 col-sm-1">
                    <a href="#" class="delete-lang" title="{PHP.L.Delete}" data-toggle="tooltip" data-id="{RESUME_LANG_ROW_ID}"><span
                                class="glyphicon glyphicon-remove text-danger"></span></a>
                </div>
                <!-- ENDIF -->
            </div>
            <!-- END: RESUME_LANG_ROW -->
        </div>


        <h4 class="widget-title" style="margin-top: 20px">
            {PHP.L.personal_experience} {RESUME_EXPERIENCE}
            <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
            <button id="add-experience" class="btn btn-default btn-xs addToolTip" title="{PHP.L.Add}"
                    style="margin-right: 20px"><span class="fa fa-plus"></span></button>
            <!-- ENDIF -->
        </h4>
        <div id="experienceContainer" style="margin-bottom: 20px">
            <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
            <div id="experience-row-tpl" class="row experience-row margintop10" style="display: none">
                <div class="col-xs-4 col-sm-3">
                    {PHP.L.personal_begin} <span class="experience-begin"></span> - <span class="experience-end"></span>
                </div>
                <div class="col-xs-7 col-sm-5">
                    <h4 class="experience-organization" style="margin: 0"></h4>
                    <div>
                        <span class="desc experience-city"></span>
                        <span class="desc experience-website-link"></span>
                    </div>
                    <div class="strong margintop10 experience-position"></div>
                    <div class="edu-specialty margintop10 experience-achievements" style="overflow-x: hidden"></div>
                </div>

                <div class="col-xs-1 col-sm-1">
                    <a href="#" class="edit-experience" title="{PHP.L.Edit}" data-toggle="tooltip" data-id=""
                       data-city_id="" data-website="" data-begin-date="" data-begin-stamp="" data-end-date=""
                       data-end-stamp="" data-for-now=""><span class="glyphicon glyphicon-edit text"></span></a>

                    <a href="#" class="delete-experience" title="{PHP.L.Delete}" data-toggle="tooltip" data-id=""><span
                            class="glyphicon glyphicon-remove text-danger"></span></a>
                </div>

            </div>
            <!-- ENDIF -->
            <!-- BEGIN: RESUME_EXP_ROW -->
            <div id="experience-row-{RESUME_EXP_ROW_ID}" class="row experience-row margintop10">
                <div class="col-xs-4 col-sm-3">
                    {PHP.L.personal_begin} <span class="experience-begin">{RESUME_EXP_ROW_BEGIN}</span> -
                    <span class="experience-end"><!-- IF {RESUME_EXP_ROW_FOR_NOW} == 0 -->{PHP.L.personal_to}<!-- ENDIF -->
                        {RESUME_EXP_ROW_END}</span>
                </div>
                <div class="col-xs-7 col-sm-8">
                    <h4 class="experience-organization" style="margin: 0">{RESUME_EXP_ROW_ORGANIZATION}</h4>
                    <div>
                        <span class="desc experience-city">{RESUME_EXP_ROW_CITY_TITLE}</span>
                        <span class="desc experience-website-link">
                        <!-- IF {RESUME_EXP_ROW_WEBSITE} -->
                        <a target="_blank" rel="nofollow" href="/go.php?{RESUME_EXP_ROW_WEBSITE}">{RESUME_EXP_ROW_WEBSITE}</a>
                        <!-- ENDIF -->
                        </span>
                    </div>
                    <div class="strong margintop10 experience-position">{RESUME_EXP_ROW_POSITION}</div>
                    <div class="edu-specialty margintop10 experience-achievements" style="overflow-x: hidden">{RESUME_EXP_ROW_ACHIEVEMENTS}</div>
                </div>
                <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                <div class="col-xs-1 col-sm-1">
                    <a href="#" class="edit-experience" title="{PHP.L.Edit}" data-toggle="tooltip" data-id="{RESUME_EXP_ROW_ID}"
                       data-city_id="{RESUME_EXP_ROW_CITY_ID}" data-website="{RESUME_EXP_ROW_WEBSITE}" data-begin-date="{RESUME_EXP_ROW_BEGIN_DATE}"
                       data-begin-stamp="{RESUME_EXP_ROW_BEGIN_STAMP}" data-end-date="{RESUME_EXP_ROW_END_DATE}"
                       data-end-stamp="{RESUME_EXP_ROW_END_STAMP}" data-for-now="{RESUME_EXP_ROW_FOR_NOW}"><span
                            class="glyphicon glyphicon-edit text"></span></a>

                    <a href="#" class="delete-experience" title="{PHP.L.Delete}" data-toggle="tooltip" data-id="{RESUME_EXP_ROW_ID}"><span
                            class="glyphicon glyphicon-remove text-danger"></span></a>
                </div>
                <!-- ENDIF -->
            </div>
            <!-- END: RESUME_EXP_ROW -->
        </div>


        <hr class="margintop20" />
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <h4>{PHP.L.personal_resume_skills}:</h4>
                {RESUME_SKILLS}
            </div>
            <div class="col-xs-12 col-sm-6">
                <h4>
                    {PHP.L.personal_recommendations}:
                    <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                    <button id="add-recommend" class="btn btn-default btn-xs addToolTip" title="{PHP.L.Add}"
                            style="margin-right: 20px"><span class="fa fa-plus"></span></button>
                    <!-- ENDIF -->
                </h4>
                <div id="recommendContainer" style="margin-bottom: 20px">
                    <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                    <div id="recommend-row-tpl" class="row recommend-row margintop10" style="display: none">
                        <div class="col-xs-11">
                            <div class="strong recommend-name"></div>
                            <div>
                                <span class="recommend-organization"></span>
                                (<span class="recommend-position"></span>)
                            </div>
                            <div>
                                <span class="fa fa-phone"></span> <span class="recommend-phone"></span>
                            </div>
                        </div>
                        <div class="col-xs-1">
                            <a href="#" class="edit-recommend" title="{PHP.L.Edit}" data-toggle="tooltip"
                               data-id=""><span class="glyphicon glyphicon-edit text"></span></a>

                            <a href="#" class="delete-recommend" title="{PHP.L.Delete}" data-toggle="tooltip"
                               data-id=""><span class="glyphicon glyphicon-remove text-danger"></span></a>
                        </div>
                    </div>
                    <!-- ENDIF -->
                    <!-- BEGIN: RESUME_RECOMMEND_ROW -->
                    <div id="recommend-row-{RESUME_RECOMMEND_ROW_ID}" class="row recommend-row margintop10">
                        <div class="col-xs-11">
                            <div class="strong recommend-name">{RESUME_RECOMMEND_ROW_NAME}</div>
                            <div>
                                <span class="recommend-organization">{RESUME_RECOMMEND_ROW_ORGANIZATION}</span>
                                (<span class="recommend-position">{RESUME_RECOMMEND_ROW_POSITION}</span>)
                            </div>
                            <div>
                                <span class="fa fa-phone"></span> <span class="recommend-phone">{RESUME_RECOMMEND_ROW_PHONE}</span>
                            </div>
                        </div>
                        <!-- IF {PHP.env.location} == 'personal.resume_preview' -->
                        <div class="col-xs-1">
                            <a href="#" class="edit-recommend" title="{PHP.L.Edit}" data-toggle="tooltip"
                               data-id="{RESUME_RECOMMEND_ROW_ID}"><span class="glyphicon glyphicon-edit text"></span></a>

                            <a href="#" class="delete-recommend" title="{PHP.L.Delete}" data-toggle="tooltip"
                               data-id="{RESUME_RECOMMEND_ROW_ID}"><span class="glyphicon glyphicon-remove text-danger"></span></a>
                        </div>
                        <!-- ENDIF -->
                    </div>
                    <!-- END: RESUME_RECOMMEND_ROW -->
                </div>

                <h4>{PHP.L.personal_resume_text}</h4>
                {RESUME_TEXT}
            </div>
        </div>
    </div>
</div>

<!-- IF {PHP.env.location} == 'personal.resume_preview' -->
<!-- Language Modal -->
<div class="modal fade" id="languageModal" tabindex="-1" role="dialog" aria-labelledby="languageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="languageModalLabel">{PHP.L.personal_langs_add}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6">{ADD_LANG_LANG}</div>
                    <div class="col-xs-6">{ADD_LANG_LEVEL}</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add-lang-save"><span class="glyphicon glyphicon-ok"></span> {PHP.L.Save}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> {PHP.L.Cancel}</button>
            </div>
        </div>
    </div>
</div>

<!-- Education Modal -->
<div class="modal fade" id="educationModal" tabindex="-1" role="dialog" aria-labelledby="educationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="educationForm" method="post" action="{PHP|cot_url('personal', 'm=user&a=ajxResumeEdit')}">
                <input type="hidden" id="add_edu_act" name="act" value="save_edu" />
                <input type="hidden" name="rid" value="{RESUME_ID}" />
                <input type="hidden" id="add_edu_id" name="eid" value="0" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="educationModalLabel">{PHP.L.personal_education_add}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label" for="add_edu_level">{PHP.L.personal_level}:</label> {ADD_EDU_LEVEL}
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="add_edu_title">{PHP.L.personal_institution_title}: *</label> {ADD_EDU_TITLE}
                    </div>
                    <div class="form-group">
                        <label for="add_edu_faculty">{PHP.L.personal_faculty}:</label> {ADD_EDU_FACULTY}
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="add_edu_specialty">{PHP.L.personal_specialty}:</label>
                        {ADD_EDU_SPECIALTY}
                        <span class="help-block">{PHP.L.personal_resume_specialty_hint}</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="add_edu_year">{PHP.L.personal_education_year}:</label>
                        {ADD_EDU_YEAR}
                        <span class="help-block">{PHP.L.personal_resume_education_year_hint}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-edu-save"><span class="glyphicon glyphicon-ok"></span>
                        {PHP.L.Save}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>
                        {PHP.L.Cancel}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Recommendations Modal -->
<div class="modal fade" id="recommendModal" tabindex="-1" role="dialog" aria-labelledby="recommendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="recommendForm" method="post" action="{PHP|cot_url('personal', 'm=user&a=ajxResumeEdit')}">
                <input type="hidden" name="rid" value="{RESUME_ID}" />
                <input type="hidden" id="add-recommend_act" name="act" value="save_recommend" />
                <input type="hidden" id="add-recommend_id" name="recommend_id" value="0" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="recommendModalLabel">{PHP.L.personal_recommendations}:</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label" for="add_recommend_name">{PHP.L.Name}: *</label> {ADD_RECOMMEND_NAME}
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="add_recommend_position">{PHP.L.personal_position}: *</label>
                        {ADD_RECOMMEND_POSITION}
                    </div>
                    <div class="form-group">
                        <label for="add_recommend_organization">{PHP.L.personal_organization}: *</label>
                        {ADD_RECOMMEND_ORGANIZATION}
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="add_recommend_phone">{PHP.L.personal_phone_s}: *</label>
                        {ADD_RECOMMEND_PHONE}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add_recommend-save"><span class="glyphicon glyphicon-ok"></span>
                        {PHP.L.Save}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>
                        {PHP.L.Cancel}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ENDIF -->

<!-- Experiences Modal -->
<div class="modal fade" id="experienceModal" role="dialog" aria-labelledby="experienceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-lg">
            <form role="form" id="experienceForm" method="post" action="{PHP|cot_url('personal', 'm=user&a=ajxResumeEdit')}"
                    class="form-horizontal">
                <input type="hidden" name="rid" value="{RESUME_ID}" />
                <input type="hidden" id="add-experience_act" name="act" value="save_experience" />
                <input type="hidden" id="add-experience_id" name="experience_id" value="0" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="experienceModalLabel">{PHP.L.personal_experience}:</h4>
                </div>
                <div class="modal-body"  style="overflow:hidden;">
                    <span class="help-block">{PHP.L.personal_resume_experience_hint}</span>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="add_experience_organization">{PHP.L.personal_organization}: *</label>
                        <div class="col-sm-9">{ADD_EXPERIENCE_ORGANIZATION}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="add_experience_city">{PHP.L.personal_region}: *</label>
                        <div class="col-sm-9">{ADD_EXPERIENCE_CITY}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" class="col-sm-2 control-label" for="add_experience_website">{PHP.L.Website}:</label>
                        <div class="col-sm-9">{ADD_EXPERIENCE_WEBSITE}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="add_experience_position">{PHP.L.personal_position}: *</label>
                        <div class="col-sm-9">{ADD_EXPERIENCE_POSITION}</div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0">
                        <label class="col-sm-3 control-label" for="add_experience_begin">{PHP.L.personal_resume_begin}:&nbsp;*</label>
                        <div class="col-sm-3">{ADD_EXPERIENCE_BEGIN}</div>
                        <label class="col-sm-3 control-label" for="add_experience_end">{PHP.L.personal_resume_end}:</label>
                        <div class="col-sm-3">{ADD_EXPERIENCE_END}</div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">{ADD_EXPERIENCE_FOR_NOW}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="add_experience_achievements">{PHP.L.personal_resume_achievements}:</label>
                            {ADD_EXPERIENCE_ACHIEVEMENTS}
                            <span class="help-block">{PHP.L.personal_resume_achievements_hint}</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add_experience-save"><span class="glyphicon glyphicon-ok"></span>
                        {PHP.L.Save}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>
                        {PHP.L.Cancel}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->