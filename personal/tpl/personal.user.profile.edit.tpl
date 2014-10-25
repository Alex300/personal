<!-- BEGIN: MAIN -->
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-push-10">{LIST_USER_ID|p30_userInfo($this)}</div>

    <div class="col-xs-12 col-md-10 col-md-pull-2">
        <div class="breadcrumb hidden-xs">{BREADCRUMBS}</div>

        {USER_ID|p30_userTabs($this, 'vacancy')}

        <div>
            <a href="{PHP|cot_url('personal', 'm=user&a=vacancy')}">{PHP.L.personal_vacancy_manage}</a> |
            <span class="text-muted">{PHP.L.personal_employer_info}</span>
        </div>

        <h1>{PAGE_TITLE}</h1>

        {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

        <form action="{FORM_ACTION}" role="form" method="post" name="profileForm" enctype="multipart/form-data" class="form-horizontal">
            {FORM_HIDDEN}
            <div class="form-group {PHP|p30_formGroupClass('title')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.Title}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_TITLE}
                    <span class="help-block">{PHP.L.personal_profile_title_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('type')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_profile_type}:</label>
                <div class="{PHP.p30.elementClass}">{FORM_TYPE}</div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('text')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_profile_text}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_TEXT}
                    <span class="help-block">{PHP.L.personal_profile_text_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('logo')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_logo}:</label>
                <div class="{PHP.p30.elementClass}">
                    {PROFILE_ID|cot_files_filebox('personal_empl_profile', $this, '', 'image', 1)}
                    <span class="help-block">{PHP.L.personal_profile_logo_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('site')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_site}:</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_SITE}
                    <span class="help-block">{PHP.L.personal_profile_site_hint}</span>
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('address')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_profile_adress}:</label>
                <div class="{PHP.p30.elementClass}">{FORM_ADDRESS}</div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('phone')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.personal_phone_s}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_PHONE}
                    <!-- IF {USER_PHONE} -->
                    <span class="help-block">{PHP.L.personal_phone_hint}: <strong>{USER_PHONE}</strong></span>
                    <!-- ENDIF -->
                </div>
            </div>

            <div class="form-group {PHP|p30_formGroupClass('email')}">
                <label class="{PHP.p30.labelClass} control-label">{PHP.L.Email}: *</label>
                <div class="{PHP.p30.elementClass}">
                    {FORM_EMAIL}
                    <span class="help-block">{PHP.L.personal_email_hint}: <strong>{USER_EMAIL}</strong></span>
                </div>
            </div>

            <div class="form-group">
                <div class="{PHP.p30.elementClass} col-sm-offset-2">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> {PHP.L.Submit}</button>
                </div>
            </div>
        </form>
    </div>

</div>
<!-- END: MAIN -->
