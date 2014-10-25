<!-- BEGIN: MAIN -->
<!-- IF {PAGE_TITLE} -->
<h2 class="tags"><img src="{PHP.cfg.modules_dir}/{PHP.env.ext}/{PHP.env.ext}.png" style="vertical-align: middle;" /> {PAGE_TITLE}</h2>
<!-- ENDIF -->

<div class="block">
    <form method="post" action="{LIST_FORM_URL}" class="ajax" id="editStaff" name="editStaff">
        <input type="hidden" name ="a" value="masssave" />

        <table class="cells">
            <tr>
                <td class="coltop"></td>
                <td class="coltop">{PHP.L.Title}</td>
                <td class="width10 coltop">{PHP.L.personal_resume_count}</td>
                <td class="width10 coltop">{PHP.L.personal_vacancy_count}</td>
                <td class="coltop"></td>
                <td class="coltop">ID</td>
            </tr>
            <!-- BEGIN: LIST_ROW -->
            <tr>
                <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
                <td class="{LIST_ROW_ODDEVEN}" style="{LIST_ROW_TD_STYLE}">{LIST_ROW_FORM_TITLE}</td>
                <td class="{LIST_ROW_ODDEVEN}"></td>
                <td class="{LIST_ROW_ODDEVEN}"></td>
                <td class="{LIST_ROW_ODDEVEN} centerall">
                    <a href="{LIST_ROW_DELETE_URL}" class="confirmLink"><img src="images/icons/default/delete.png" /></a>
                </td>
                <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_ID}</td>
            </tr>
            <!-- END: LIST_ROW -->

            <!-- BEGIN: EMPTY -->
            <tr>
                <td class="odd centerall" colspan="6">{PHP.L.None}</td>
            </tr>
            <!-- END: EMPTY -->

            <!-- IF {LIST_TOTALLINES} > 0 -->
            <tr>
                <td colspan="6" class="valid">
                    <input type="submit" value="{PHP.L.Update}" class="submit">
                </td>
            </tr>
            <!-- ENDIF -->
        </table>
    </form>
</div>

<div class="block">
    <h3>{PHP.L.Add}:</h3>
    <form enctype="multipart/form-data" class="ajax" method="post" action="{ADDFORM_URL}" id="addStaff" name="addStaff">
        <input type="hidden" value="addnew" name="act" />

        <table class="cells info">
            <tr>
                <td class="width20">{PHP.L.Title}:</td>
                <td class="width80">{ADDFORM_NAME} (обязательно)</td>
            </tr>
            <tr>
                <td colspan="2" class="valid">
                    <input type="submit" value="{PHP.L.Add}" class="submit">
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- END: MAIN -->