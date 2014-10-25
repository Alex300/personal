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

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <form action="{FORM_ACTION}" role="form" method="post" name="vacancyForm" enctype="multipart/form-data" class="form-horizontal">
            {FORM_HIDDEN}
            <div class="form-group {PHP|p30_formGroupClass('title')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.Title}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_TITLE}
                    <span class="help-block">{PHP.L.personal_profile_title_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('city')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.select_city}: *</label>
                <div class="{PHP.p30.elementClass}">{FORM_CITY}</div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('district')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_district}:</label>
                <div class="{PHP.p30.elementClass}">{FORM_DISTRICT}</div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('category')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.Categories}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_CATEGORY}
                    <span class="help-block">{PHP.L.personal_vacancy_category_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('staff')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_staff}: *</label>
                <div class="{PHP.p30.elementClass}">
                    <div class="form-control" style="height: auto">{FORM_STAFF}</div>
                    <span class="help-block">{PHP.L.personal_staff_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('employment')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_employment}:</label>
                <div class="{PHP.p30.elementClass}">
                    <div class="form-control" style="height: auto">{FORM_EMPLOYMENT}</div>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('schedule')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_schedule}:</label>
                <div class="{PHP.p30.elementClass}">
                    <div class="form-control" style="height: auto">{FORM_SCHEDULE}</div>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('experience')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_experience}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_EXPERIENCE}
                    <span class="help-block">{PHP.L.personal_vacancy_experience_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('education')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_education_level}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_EDUCATION}
                    <span class="help-block">{PHP.L.personal_vacancy_education_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('salary')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_vacancy_salary}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_SALARY}
                    <span class="help-block">{PHP.L.personal_vacancy_salary_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('text')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_vacancy_text}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_TEXT}
                    <span class="help-block">{PHP.L.personal_vacancy_text_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('skills')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_vacancy_skills}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_SKILLS}
                    <span class="help-block">{PHP.L.personal_vacancy_skills_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('files')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.files_attach}:</label>
                <div class="{PHP.p30.elementClass}">
                    {VACANCY_ID|cot_files_filebox('personal_vacancy', $this)}
                    <span class="help-block">{PHP.L.personal_vacancy_attach_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('contact_face')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_contact_face}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_CONTACT_FACE}
                    <span class="help-block">{PHP.L.personal_name_hint}: <strong>{USER_DISPLAY_NAME}</strong></span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('phone')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_phone_s}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_PHONE}
                    <!-- IF {EMPLOYER_PHONE} -->
                    <span class="help-block">{PHP.L.personal_phone_hint}: <strong>{EMPLOYER_PHONE}</strong></span>
                    <!-- ENDIF -->
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('email')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.Email}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_EMAIL}
                    <span class="help-block">{PHP.L.personal_email_hint}: <strong>{EMPLOYER_EMAIL}</strong></span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('makeactive')}">
                <div class="{PHP.p30.elementClass} col-sm-offset-2"><div class="checkbox">{FORM_ACTIVATE}</div></div>
            </div>

            <div class="form-group">
                <div class="{PHP.p30.elementClass} col-sm-offset-2">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> {PHP.L.Save}</button>
                </div>
            </div>
        </form>
    </div>

</div>
<!-- END: MAIN -->
