<?
if(!$USER->IsAdmin())
	return;

$module_id = "uptolike.intarget";
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($MODULE_RIGHT >= "R"):

$message = false;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");

if ($message)
	echo $message->Show();

$aTabs = array(
	array(
		"DIV" => "edit0",
		"TAB" => GetMessage("INTARGET_TAB_SETTINGS"),
		"ICON" => "support_settings",
		"TITLE" => GetMessage("INTARGET_TAB_SETTINGS_TITLE")
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($message == ""):
if($REQUEST_METHOD == "POST")
{
	if(strlen($RestoreDefaults) > 0)
	{
		COption::RemoveOption("uptolike.intarget");
		COption::SetOptionString($module_id, "INTARGET_MAIL", $INTARGET_MAIL);
		COption::SetOptionString($module_id, "INTARGET_KEY", $INTARGET_KEY);
	}
	else
	{
		COption::SetOptionString($module_id, "INTARGET_LANGUAGE", $INTARGET_LANGUAGE);
		COption::SetOptionString($module_id, "INTARGET_MAIL", $INTARGET_MAIL);
		COption::SetOptionString($module_id, "INTARGET_KEY", $INTARGET_KEY);
		COption::SetOptionString($module_id, "WIDGET_CODE", $WIDGET_CODE);
	}

	if(strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0)
		LocalRedirect($_REQUEST["back_url_settings"]);
}
endif;

$INTARGET_LANGUAGE = COption::GetOptionString($module_id, "INTARGET_LANGUAGE");
$INTARGET_MAIL = COption::GetOptionString($module_id, "INTARGET_MAIL");
$INTARGET_KEY = COption::GetOptionString($module_id, "INTARGET_KEY");
$WIDGET_CODE = COption::GetOptionString($module_id, "WIDGET_CODE");

$tabControl->Begin();
?>
<form id="uptolike_form" method="post" action="<? echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<? echo LANGUAGE_ID?>">

<? $tabControl->BeginNextTab();?>
	Оцените принципиально новый подход к просмотру статистики. Общайтесь со своей аудиторией, продавайте лучше, зарабатываейте больше. И всё это бесплатно!
	<?
	$stat_url = '';
	if($INTARGET_MAIL and $INTARGET_KEY):
		$auth = CUptolikeIntarget::userReg($INTARGET_MAIL,$INTARGET_KEY);
	else: ?>
	<div class="adm-info-message-wrap adm-info-message-red">
		<div class="adm-info-message">
			<?php echo GetMessage("INTARGET_TAB_MESS_2");?>
			<div class="adm-info-message-icon"></div>
		</div>
	</div>
	<?php endif;?>
    <? var_dump($auth);
	if($auth['error']):?>
	<div class="adm-info-message-wrap adm-info-message-red">
		<div class="adm-info-message">
			<?php echo $auth['error'];?>
			<div class="adm-info-message-icon"></div>
		</div>
	</div>
<?php endif;
	if($auth['ok']):?>
		<div class="adm-info-message-wrap adm-info-message-green">
			<div class="adm-info-message">
				<?php echo $auth['ok'];?>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?php endif;?>
	<tr id="uptolike_email_field">
		<td><?=GetMessage("INTARGET_TAB_MAIL")?></td>
		<td>
			<input id="uptolike_email" type="text" name="INTARGET_MAIL" value="<?=htmlspecialcharsbx($INTARGET_MAIL)?>" required>
        </td>
	</tr>
	<tr id="uptolike_key_field">
		<td><?=GetMessage("INTARGET_TAB_KEY")?></td>
		<td>
			<input id="uptolike_key" type="text" name="INTARGET_KEY" value="<?=htmlspecialcharsbx($INTARGET_KEY)?>" required>
		</td>
	</tr>

<? $tabControl->Buttons();?>
	<input id="uptolike_form_update" type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
	<input id="uptolike_form_apply" type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<? if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
	<? endif?>
	<input type="submit" name="RestoreDefaults" title="<?=GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?=GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<? $tabControl->End();?>
</form>
<? endif;?>