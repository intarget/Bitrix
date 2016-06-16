<?php

use Bitrix\Main\Localization\Loc;

if (!$USER->IsAdmin()) {
    return;
}

define('ADMIN_MODULE_NAME', 'uptolike.intarget');


if ($APPLICATION->GetGroupRight(ADMIN_MODULE_NAME) >= 'R') {

    Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
    Loc::loadMessages(__FILE__);

    $tabControl = new CAdminTabControl("tabControl", array(array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),));

    if ((!empty($save) || !empty($restore)) && $REQUEST_METHOD == "POST" && check_bitrix_sessid()) {
        if (!empty($restore)) {
            COption::RemoveOption(ADMIN_MODULE_NAME);
            CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("OPTIONS_RESTORED"), "TYPE" => "OK"));
        } else {
            $is_saved = false;

            $intarget_id = 'intarget_id';
            $intarget_code = "intarget_code";
            $intarget_mail = "intarget_mail";
            $intarget_key = "intarget_key";

            if (!empty($_REQUEST[$intarget_mail])) {
                COption::SetOptionString(ADMIN_MODULE_NAME, $intarget_mail, $_REQUEST[$intarget_mail], Loc::getMessage("INTARGET_MAIL"));
                $is_saved = true;
            } else {
                CAdminMessage::ShowMessage(Loc::getMessage("ERROR_MAIL_EMPTY"));
            }
            if (!empty($_REQUEST[$intarget_key])) {
                COption::SetOptionString(ADMIN_MODULE_NAME, $intarget_key, $_REQUEST[$intarget_key], Loc::getMessage("INTARGET_KEY"));
                $is_saved = true;
            }

            if ($is_saved) {
                $json_result = CUptolikeIntarget::userReg($_REQUEST['intarget_mail'], $_REQUEST['intarget_key']);
                if (isset($json_result->status)) {
                    if (($json_result->status == 'OK')) {
                        $val_intarget_id = $json_result->payload->projectId;
                        COption::SetOptionString(ADMIN_MODULE_NAME, 'intarget_id', $json_result->payload->projectId, '');
                        $val_intarget_code = CUptolikeIntarget::jsCode($val_intarget_id);
                        COption::SetOptionString(ADMIN_MODULE_NAME, 'intarget_code', htmlspecialchars($val_intarget_code), '');
                        CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage('INTARGET_ID_SUCCESS'), "TYPE" => "OK"));
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
                } else {
                    CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("INTARGET_TAB_MESS_7") . ' ' . $json_result, "TYPE" => "ERROR"));
                }
            }
        }
    }

    $tabControl->Begin();
    $val_intarget_id = COption::GetOptionString(ADMIN_MODULE_NAME, 'intarget_id');
    $val_intarget_code = COption::GetOptionString(ADMIN_MODULE_NAME, 'intarget_code');
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
        $intarget_mail = 'intarget_mail';
        $intarget_key = 'intarget_key';
        $intarget_id = 'intarget_id';
        $intarget_code = 'intarget_code';

        $val_intarget_mail = COption::GetOptionString(ADMIN_MODULE_NAME, 'intarget_mail');
        $val_intarget_key = COption::GetOptionString(ADMIN_MODULE_NAME, 'intarget_key');
        ?>

        <tr class="heading">
            <td colspan="2"><b><?= GetMessage('INTARGET_TAB_HEADER') ?></b></td>
        </tr>


        <tr>
            <td width="40%">
                <label for="<?= $intarget_mail ?>"><?= Loc::getMessage("INTARGET_MAIL") ?>:</label>
            <td width="60%">
                <input type="text" size="50" name="<?= $intarget_mail ?>"
                       value="<?= $val_intarget_mail; ?>" <? echo (!empty($val_intarget_id)) ? 'disabled' : ''; ?>>
                <? if (!empty($val_intarget_id)): ?>
                    <div
                        style="background-image: url('../images/<?= ADMIN_MODULE_NAME ?>/ok.png');width: 16px;height: 16px;margin: -4px -22px;display: inline-block;"></div>
                <?endif; ?>
            </td>
        </tr>

        <tr>
            <td width="40%">
                <label for="<?= $intarget_key ?>"><?= Loc::getMessage("INTARGET_KEY") ?>:</label>
            <td width="60%">
                <input type="text" size="50" name="<?= $intarget_key ?>"
                       value="<?= $val_intarget_key; ?>" <? echo (!empty($val_intarget_id)) ? 'disabled' : ''; ?>>
                <? if (!empty($val_intarget_id)): ?>
                    <div
                        style="background-image: url('../images/<?= ADMIN_MODULE_NAME ?>/ok.png');width: 16px;height: 16px;margin: -4px -22px;display: inline-block;"></div>
                <?endif; ?>
            </td>
        </tr>

        <tr>
            <input type="hidden" name="<?= $intarget_id ?>"
                   value="<?= $val_intarget_id; ?>">
            <input type="hidden" name="<?= $intarget_code ?>"
                   value="<?= htmlspecialcharsbx($val_intarget_code) ?>">
        </tr>

        <tr>
            <td colspan="2">
                <? if (!empty($val_intarget_id)): ?>
                    <?= GetMessage("INTARGET_TAB_TEXT3") ?> <a
                        href="https://intarget.ru"><?= GetMessage("INTARGET_TITLE") ?></a><br><br>
                <? else: ?>
                    <?= GetMessage("INTARGET_TAB_TEXT1") ?> <a
                        href="https://intarget.ru"><?= GetMessage("INTARGET_TITLE") ?></a><br><br>
                    <?= GetMessage("INTARGET_TAB_TEXT2") ?> <a
                        href="https://intarget.ru"><?= GetMessage("INTARGET_TITLE") ?></a><br><br>
                <?endif; ?>
                <?= GetMessage("INTARGET_TAB_TEXT4") ?> <a href='mailto:plugins@intarget.ru'>plugins@intarget.ru</a><br><br>
                <?= GetMessage("INTARGET_TAB_TEXT5") ?><br><br>
            </td>
        </tr>

        <? $tabControl->Buttons(); ?>
        <? if (empty($val_intarget_id)): ?>
            <input type="submit" name="save" value="<?= GetMessage("MAIN_SAVE") ?>"
                   title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save">
            <input type="submit" name="restore" title="<?= GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
                   OnClick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
                   value="<?= GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
            <?= bitrix_sessid_post(); ?>
        <?endif; ?>
        <? $tabControl->End(); ?>

    </form>
    <style>
        .adm-detail-content-table > tbody > .heading td {
            text-align: left !important;
        }

        .adm-detail-content-table > tbody > .heading td > b {
            font-weight: normal !important;
        }
    </style>
    <?php

}

?>
