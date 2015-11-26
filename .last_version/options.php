<?php

use Bitrix\Main\Localization\Loc;

if (!$USER->IsAdmin()) {
    return;
}

define('ADMIN_MODULE_NAME', 'uptulike.intarget');


if ($APPLICATION->GetGroupRight(ADMIN_MODULE_NAME) >= 'R') {

    Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
    Loc::loadMessages(__FILE__);

    $tabControl = new CAdminTabControl("tabControl", array(
        array(
            "DIV" => "edit1",
            "TAB" => GetMessage("MAIN_TAB_SET"),
            "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")
        ),
    ));

    if ((!empty($save) || !empty($restore)) && $REQUEST_METHOD == "POST" && check_bitrix_sessid()) {

        if (!empty($restore)) {

            COption::RemoveOption(ADMIN_MODULE_NAME);
            CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("OPTIONS_RESTORED"), "TYPE" => "OK"));

        } else {

            $is_saved = false;

//			$rsSites = CSite::GetList($by="sort", $order="desc");
//			while ($arSite = $rsSites->Fetch()) {
            $intarget_id = 'intarget_id';
            $intarget_code = "intarget_code";//_".$arSite['ID'];
            $intarget_mail = "intarget_mail";//_".$arSite['ID'];
            $intarget_key = "intarget_key";//_".$arSite['ID'];

            if (!empty($_REQUEST[$intarget_mail])) {
                COption::SetOptionString(
                    ADMIN_MODULE_NAME,
                    $intarget_mail,
                    $_REQUEST[$intarget_mail],
                    Loc::getMessage("INTARGET_MAIL")
                );
                $is_saved = true;
            } else {
                CAdminMessage::ShowMessage(Loc::getMessage("ERROR_MAIL_EMPTY"));
            }

            if (!empty($_REQUEST[$intarget_key])) {
                COption::SetOptionString(
                    ADMIN_MODULE_NAME,
                    $intarget_key,
                    $_REQUEST[$intarget_key],
                    Loc::getMessage("INTARGET_KEY")
                );
                $is_saved = true;
            }

//			}

            if ($is_saved) {
                $json_result = CUptolikeIntarget::userReg($_REQUEST[$intarget_mail], $_REQUEST[$intarget_key]);

                if (isset($json_result->status)) {
                    if (($json_result->status == 'OK')) {
                        $intarget_id = $json_result->payload->projectId;
                        COption::SetOptionString(
                            ADMIN_MODULE_NAME,
                            $intarget_id,
                            $_REQUEST[$intarget_id],
                            Loc::getMessage("INTARGET_ID")
                        );
                        $is_saved = true;
                        CAdminMessage::ShowMessage(array("MESSAGE" => $json_result->payload->projectId, "TYPE" => "OK"));
                    } elseif ($json_result->status == 'error') {
                        if ($json_result->code == '403') {
                            $json_result->message = Loc::getMessage('INTARGET_TAB_MESS_3');
                        }
                        if ($json_result->code == '500') {
                            $json_result->message = Loc::getMessage('INTARGET_TAB_MESS_4');
                        }
                        if ($json_result->code == '404') {
                            $json_result->message = Loc::getMessage('INTARGET_TAB_MESS_5');
                        }
                        if (!isset($json_result->code)) {
                            $json_result->message = Loc::getMessage('INTARGET_TAB_MESS_6');
                        }

                        CAdminMessage::ShowMessage(array("MESSAGE" => $json_result->message, "TYPE" => "ERROR"));
                    }
                } else CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("INTARGET_TAB_MESS_7"), "TYPE" => "ERROR"));
            }

        }

    }

    $tabControl->Begin();

    ?>

    <form method="post"
          action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>">

        <?php if (!function_exists('curl_exec')): ?>
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <span
                        class="required"><?= Loc::getMessage("CURL_DISABLED_MESSAGE") ?></span><br/>
                    <?= Loc::getMessage("HOSTING_SUPPORT") ?>
                </div>
            </div>
        <?php endif; ?>

        <? $tabControl->BeginNextTab(); ?>

        <?php

        $intarget_mail = COption::GetOptionString(ADMIN_MODULE_NAME, "INTARGET_MAIL", '');
        $intarget_key = COption::GetOptionString(ADMIN_MODULE_NAME, "INTARGET_KEY", '');
        $intarget_id = COption::GetOptionString(ADMIN_MODULE_NAME, "INTARGET_ID", '');
        $intarget_code = COption::GetOptionString(ADMIN_MODULE_NAME, "INTARGET_CODE", '');

        //		$rsSites = CSite::GetList($by="sort", $order="desc");
        //		while ($arSite = $rsSites->Fetch()):
        $intarget_mail = "intarget_mail";//_".$arSite['ID'];
        $intarget_key = "intarget_key";//_".$arSite['ID'];
        $intarget_id = "intarget_id";//_".$arSite['ID'];
        $intarget_code = "intarget_code";//_".$arSite['ID'];

        $val_intarget_mail = COption::GetOptionString(ADMIN_MODULE_NAME, $intarget_mail, '');
        $val_intarget_key = COption::GetOptionString(ADMIN_MODULE_NAME, $intarget_key, '');
        $val_intarget_id = COption::GetOptionString(ADMIN_MODULE_NAME, $intarget_id, '');
        $val_intarget_code = COption::GetOptionString(ADMIN_MODULE_NAME, $intarget_code, '');
        ?>

        <tr class="heading">
            <td colspan="2"><b><?= GetMessage('INTARGET_TAB_HEADER') ?></b></td>
        </tr>


        <tr>
            <td width="40%">
                <label for="<?= $intarget_mail ?>"><?= Loc::getMessage("INTARGET_MAIL") ?>:</label>
            <td width="60%">
                <input type="text" size="50" name="<?= $intarget_mail ?>"
                       value="<?= htmlspecialcharsbx($val_intarget_mail) ?>">
            </td>
        </tr>

        <tr>
            <td width="40%">
                <label for="<?= $intarget_key ?>"><?= Loc::getMessage("INTARGET_KEY") ?>:</label>
            <td width="60%">
                <input type="text" size="50" name="<?= $intarget_key ?>"
                       value="<?= htmlspecialcharsbx($val_intarget_key) ?>">
            </td>
        </tr>

        <tr>
            <input type="hidden" name="<?= $intarget_id ?>"
                   value="<?= htmlspecialcharsbx($val_intarget_id) ?>">
            <input type="hidden" name="<?= $intarget_code ?>"
                   value="<?= htmlspecialcharsbx($val_intarget_code) ?>">
        </tr>

        <?php //endwhile; ?>

        <? $tabControl->Buttons(); ?>

        <input type="submit" name="save" value="<?= GetMessage("MAIN_SAVE") ?>"
               title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save">
        <input type="submit" name="restore" title="<?= GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
               OnClick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
               value="<?= GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
        <?= bitrix_sessid_post(); ?>

        <? $tabControl->End(); ?>

    </form>

    <?php

}

?>
