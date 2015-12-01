<?
IncludeModuleLangFile(__FILE__);
Class uptolike_intarget extends CModule
{
	const MODULE_ID = 'uptolike.intarget';
	var $MODULE_ID = 'uptolike.intarget';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("uptolike.intarget_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("uptolike.intarget_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("uptolike.intarget_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("uptolike.intarget_PARTNER_URI");
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	public function DoInstall() {
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, "CUptolikeIntarget", "ini");
		RegisterModuleDependences("sale", "OnBeforeViewedAdd", $this->MODULE_ID, "CUptolikeIntarget", "productView");

		RegisterModuleDependences("sale", "OnBeforeBasketAdd", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");
		RegisterModuleDependences("sale", "OnBasketAdd", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");
		RegisterModuleDependences("sale", "OnBasketUpdate", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");
		RegisterModuleDependences("sale", "OnSaleBasketSaved", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");

		RegisterModuleDependences("sale", "OnBasketDelete", $this->MODULE_ID, "CUptolikeIntarget", "deleteFromCart");
		RegisterModuleDependences("main", "OnAfterUserRegister", $this->MODULE_ID, "CUptolikeIntarget", "OnAfterUserRegister");
//		RegisterModuleDependences("sale", "OnBasketOrder", $this->MODULE_ID, "CUptolikeIntarget", "updateCart", "100");
//		RegisterModuleDependences("sale", "OnBasketUpdate", $this->MODULE_ID, "CUptolikeIntarget", "updateCart", "100");
//		RegisterModuleDependences("sale", "OnSaleBasketSaved", $this->MODULE_ID, "CUptolikeIntarget", "updateCart", "100");


		$this->InstallFiles();
		$this->InstallDB();
	}

	public function DoUninstall() {
		UnRegisterModuleDependences('main', 'OnPageStart', self::MODULE_ID, 'CUptolikeIntarget', 'ini');
		UnRegisterModuleDependences("main", "OnBeforeViewedAdd", $this->MODULE_ID, "CUptolikeIntarget", "productView");

		UnRegisterModuleDependences("sale", "OnBeforeBasketAdd", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");
		UnRegisterModuleDependences("sale", "OnBasketAdd", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");
		UnRegisterModuleDependences("sale", "OnBasketUpdate", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");
		UnRegisterModuleDependences("sale", "OnSaleBasketSaved", $this->MODULE_ID, "CUptolikeIntarget", "addToCart");

		UnRegisterModuleDependences("sale", "OnBasketDelete", $this->MODULE_ID, "CUptolikeIntarget", "deleteFromCart");
		UnRegisterModuleDependences("main", "OnAfterUserRegister", $this->MODULE_ID, "CUptolikeIntarget", "OnAfterUserRegister");
//		UnRegisterModuleDependences("sale", "OnBasketUpdate", $this->MODULE_ID, "CUptolikeIntarget", "updateCart");
//		UnRegisterModuleDependences("sale", "OnSaleBasketSaved", $this->MODULE_ID, "CUptolikeIntarget", "newEventUpdateCart");
//		UnRegisterModuleDependences("sale", "OnSaleBasketItemSetField", $this->MODULE_ID, "CUptolikeIntarget", "newEventSetQtyCart");

		COption::RemoveOption(self::MODULE_ID, "intarget_id");
		COption::RemoveOption(self::MODULE_ID, "intarget_mail");
		COption::RemoveOption(self::MODULE_ID, "intarget_key");
		COption::RemoveOption(self::MODULE_ID, "intarget_code");

		$this->UnInstallFiles();
		UnRegisterModule($this->MODULE_ID);
	}

	function InstallFiles() {

		return true;
	}
}

?>

