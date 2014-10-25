<!-- BEGIN: MAIN -->

<!-- IF {PAGE_TITLE} -->
<h2 class="tags"><img src="{PHP.cfg.modules_dir}/{PHP.env.ext}/{PHP.env.ext}.png" style="vertical-align: middle;" /> {PAGE_TITLE}</h2>
<!-- ENDIF -->


<ul id="main_cpanel" class="body">
    <li>
        <a href="{PHP|cot_url('admin', 'm=personal&a=category')}">
            <img alt="{PHP.L.shop.products}" src="{PHP.cfg.modules_dir}/{PHP.env.ext}/tpl/images/category.png">
            <span>{PHP.L.Categories}</span>
        </a>
    </li>
    <li>
        <a href="{PHP|cot_url('admin', 'm=personal&a=staff')}">
            <img alt="{PHP.L.shop.shipment_methods}" src="{PHP.cfg.modules_dir}/{PHP.env.ext}/tpl/images/staff.png">
            <span>{PHP.L.personal_staff_levels}</span>
        </a>
    </li>
    <li>
        <a href="{PHP|cot_url('admin', 'm=personal&a=education')}">
            <img alt="{PHP.L.shop.payment_methods}" src="{PHP.cfg.modules_dir}/{PHP.env.ext}/tpl/images/education.png">
            <span>{PHP.L.personal_education_levels}</span>
        </a>
    </li>
    <li>
        <a href="{PHP|cot_url('admin', 'm=personal&a=language')}">
            <img alt="{PHP.L.shop.payment_methods}" src="{PHP.cfg.modules_dir}/{PHP.env.ext}/tpl/images/languages.png">
            <span>{PHP.L.Languages}</span>
        </a>
    </li>
</ul>

<div class="clear" style="height: 1px;"></div>

<div class="textcenter margintop10">
    <em>{PHP.L.personal_module_version}:</em> <strong>{PHP.cot_modules.personal.version}</strong>.
    <!--<a href="{PHP.L.shop.module_homepage_url}"
       target="_blank">{PHP.L.shop.check_updates}</a>--></div>

<!-- END: MAIN -->