<!-- BEGIN: MAIN -->

<!-- BEGIN:VACANCY_ROW -->
<div class="row">
    <div class="col-xs-12">
        <article class="list-row" style="margin-bottom: 10px; padding-bottom: 10px">
            <header>
                <h3 style="font-size: 14px; margin: 0 0 5px 0">
                    <a href="{VACANCY_ROW_URL}" title="{RESUME_ROW_TITLE}. {PHP.L.Views}: {VACANCY_ROW_VIEWS}" class="strong"
                       rel="bookmark">{VACANCY_ROW_TITLE}</a>
                    <!-- IF {VACANCY_ROW_SALARY} -->
                    <strong style="white-space: nowrap">({PHP.L.personal_from} {VACANCY_ROW_SALARY|number_format($this, 0, '.', ' ')} {PHP.L.personal_money_per_month})</strong>
                    <!-- ENDIF -->
                </h3>
            </header>
            <div class="small">
                <!-- IF {VACANCY_ROW_CITY} > 0 -->
                    {VACANCY_ROW_CITY_NAME}<!-- IF {RESUME_ROW_DISTRICT} != '' -->, {VACANCY_ROW_DISTRICT}<!-- ENDIF --><!-- IF {RESUME_ROW_LEAVING} -->, {PHP.L.personal_resume_leaving}<!-- ENDIF -->
                <!-- ENDIF -->
                /
                <!-- IF {VACANCY_ROW_EMPL_PROFILE_TITLE} -->
                <a href="{VACANCY_ROW_EMPL_PROFILE_URL}">{VACANCY_ROW_EMPL_PROFILE_TITLE}</a>
                <!-- ELSE -->
                <a href="{VACANCY_ROW_USER_DETAILSLINK}">{VACANCY_ROW_USER_DISPLAY_NAME}</a>
                <!-- ENDIF -->
                / {VACANCY_ROW_SORT_TEXT}
            </div>
        </article>
    </div>
</div>
<!-- END:VACANCY_ROW -->

<!-- IF {VACANCY_LIST_TOTALLINES} == 0 -->
<h4 class="text-muted" style="margin-top: 20">{PHP.L.None}</h4>
<!-- ENDIF -->
<!-- END: MAIN -->