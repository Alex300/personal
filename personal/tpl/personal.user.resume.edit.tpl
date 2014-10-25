<!-- BEGIN: MAIN -->
{PHP|p30_setFormElementClass('col-sm-9','col-sm-3')}
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-md-2 col-md-push-10">{LIST_USER_ID|p30_userInfo($this)}</div>

    <div class="col-xs-12 col-md-10 col-md-pull-2">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        {USER_ID|p30_userTabs($this, 'resume')}

        <h1>{PAGE_TITLE}</h1>

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <form action="{FORM_ACTION}" role="form" method="post" name="vacancyForm" enctype="multipart/form-data" class="form-horizontal">
            {FORM_HIDDEN}
            <div class="form-group {PHP|p30_formGroupClass('title')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_resume_title}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_TITLE}
                    <span class="help-block">{PHP.L.personal_resume_title_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('salary')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_resume_salary}:</label>
                <div class="{PHP.p30.elementClass}">{FORM_SALARY}</div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('city')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.select_city}: *</label>
                <div class="{PHP.p30.elementClass}">{FORM_CITY}</div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('district')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_district}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_DISTRICT}
                    <span class="help-block">{PHP.L.personal_resume_district_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('leaving')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_resume_leaving}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_LEAVING}
                    <span class="help-block">{PHP.L.personal_resume_leaving_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('category')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_category}:&nbsp;*</label>
                <div class="{PHP.p30.elementClass}">{FORM_CATEGORY}</div>
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


            <div class="form-group {PHP|p30_formGroupClass('education')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_education_level}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_EDUCATION}
                    <span class="help-block">{PHP.L.personal_resume_education_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('skills')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_resume_skills}:&nbsp;*</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_SKILLS}
                    <span class="help-block">{PHP.L.personal_resume_skills_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('files')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.Photo}:</label>
                <div class="{PHP.p30.elementClass}">
                    {RESUME_ID|cot_files_filebox('personal_resume', $this, '', 1)}
                    <span class="help-block">{PHP.L.personal_resume_photo_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('text')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_resume_text}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_TEXT}
                    <span class="help-block">{PHP.L.personal_resume_text_hint}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="{PHP.p30.elementClass} col-sm-offset-3">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> {PHP.L.Save}</button>
                </div>
            </div>
        </form>
    </div>

</div>
<!-- END: MAIN -->
