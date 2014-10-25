<!-- BEGIN: MAIN -->
<div class="visible-xs-block breadcrumb">{BREADCRUMBS}</div>

<div class="row">
    <div class="col-xs-12 col-sm-3 col-sm-push-9">
        <!-- IF {PHP.usr.auth_write} -->
        <a href="{PHP|cot_url('personal', 'm=user&a=vacancyEdit')}" class="btn btn-default width100" ><span
                class="fa fa-file-text"></span> {PHP.L.personal_vacancy_add}</a>
        <!-- ENDIF -->
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
                    <label for="keywords" class="col-xs-12 col-sm-2" style="white-space: nowrap; line-height: 34px;">{PHP.L.Keywords}:</label>
                    <div class="col-xs-12 col-sm-8">{FILTER_QUERY}</div>
                    <div class="col-xs-12 col-sm-2">
                        <button class="btn btn btn-default marginleft10" type="submit"><span class="glyphicon glyphicon-search"></span>
                            {PHP.L.Search}</button>
                    </div>
                </div>

                <div id="ext-search" class="lhn row" style="margin-top: 5px">
                    <div class="col-xs-6">{PHP.L.Found}: {LIST_TOTALLINES}</div>
                    <div class="col-xs-6 text-right">
                        <a class="ext-search-toggle caret-closed strong" href="#">{PHP.L.personal_extended_search}</a>
                    </div>
                </div>

                <div id="ext-search-form" style="display: none">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="f_city">{PHP.L.select_city}</label> {FILTER_CITY}
                                <div class="checkbox" style="margin-top: 2px">{FILTER_LEAVING}</div>
                            </div>

                            <div class="form-group">
                                <label for="">{PHP.L.personal_staff}</label>
                                <div class="form-control" style="height: auto">{FILTER_STAFF}</div>

                            </div>

                            <div class="row">
                                <label for="" class="col-sm-4" style="line-height: 34px">{PHP.L.personal_salary}</label>
                                <div class="col-sm-8">{FILTER_SALARY}</div>
                            </div>

                            <div class="row" style="margin-top: 5px">
                                <label for="" class="col-sm-4" style="line-height: 34px">{PHP.L.personal_education}</label>
                                <div class="col-sm-8">{FILTER_EDUCATION}</div>
                            </div>

                            <div class="row" style="margin-top: 5px">
                                <label for="" class="col-sm-4" style="line-height: 34px">{PHP.L.personal_period}</label>
                                <div class="col-sm-8">{FILTER_PERIOD}</div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <label>{PHP.L.personal_category}</label>
                            {FILTER_CATEGORY}
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="row margintop10">
                        <div class="col-xs-12 col-sm-6" style="line-height: 34px">
                            <a class="ext-search-toggle caret-open strong" id="" href="#">{PHP.L.personal_extended_search_hide}</a>
                        </div>

                        <div class="col-xs-12 col-sm-6 text-right">
                            <button type="submit" class="btn btn-default marginleft10"><span class="glyphicon glyphicon-search"></span>
                                {PHP.L.Search}</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <!-- /Поиск -->
        <div class="clearfix"></div>

        <!-- BEGIN: EMPTY -->
        <h4 class="text-muted text-center">{PHP.L.None}</h4>
        <!-- END: EMPTY -->

        <!-- IF {LIST_PAGINATION} -->
        <div class="pagination text-right margintop20">
            {LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT}
        </div>
        <!-- ENDIF -->

        <!-- BEGIN: VACANCY_ROW -->
        <article class="list-row">
            <div class="row">
                <div class="col-xs-12 col-sm-9">
                    <header>
                        <h2 style="margin-top: 0">
                            <a href="{VACANCY_ROW_URL}" title="{VACANCY_ROW_TITLE}" rel="bookmark">{VACANCY_ROW_TITLE}</a>
                        </h2>
                    </header>
                    <p class="strong">
                        <!-- IF {VACANCY_ROW_SALARY} -->
                        {PHP.L.personal_from} {VACANCY_ROW_SALARY|number_format($this, 0, '.', ' ')} {PHP.L.personal_money_per_month}
                        <!-- ENDIF -->
                    </p>
                    <p>
                        <!-- IF {VACANCY_ROW_EMPL_PROFILE_TITLE} -->
                        <a href="{VACANCY_ROW_EMPL_PROFILE_URL}">{VACANCY_ROW_EMPL_PROFILE_TITLE}</a>
                        <!-- ELSE -->
                        <a href="{VACANCY_ROW_USER_DETAILSLINK}">{VACANCY_ROW_USER_DISPLAY_NAME}</a>
                        <!-- ENDIF -->
                    </p>
                    <!-- IF {VACANCY_ROW_CATEGORIES_COUNT} > 0 -->
                    {PHP.L.personal_category}:
                    <!-- BEGIN: CATEGORY_ROW -->
                    <!-- IF {VACANCY_ROW_CATEGORY_ROW_NUM} > 1 -->,<!-- ENDIF -->
                    <a href="{VACANCY_ROW_CATEGORY_ROW_ID|cot_url('personal', 'a=vacancy&f[cat]=$this')}">{VACANCY_ROW_CATEGORY_ROW_TITLE}</a>
                    <!-- END: CATEGORY_ROW -->
                    <!-- ENDIF -->
                </div>

                <footer class="col-xs-12 col-sm-3 text-right">
                    <p><span class="label label-info">{VACANCY_ROW_SORT_TEXT}</span></p>
                    <!-- IF {VACANCY_ROW_CITY} > 0 --><p class="margin0">{VACANCY_ROW_CITY_NAME}</p><!-- ENDIF -->
                    <p class="margin0">№ {VACANCY_ROW_ID}</p>
                </footer>
            </div>
        </article>
        <!-- END: VACANCY_ROW -->

        <!-- IF {LIST_PAGINATION} -->
        <div class="pagination text-right">
            {LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT}
        </div>
        <!-- ENDIF -->
    </div>
</div>
<script>
    $('.ext-search-toggle').click(function(e){
        e.preventDefault();
        $('#ext-search-form').slideToggle();
        $('#ext-search').slideToggle();
        return false;
    });
</script>
<!-- END: MAIN -->
