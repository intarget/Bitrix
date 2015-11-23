<?
/** var CMain $APPLICATION */
IncludeModuleLangFile(__FILE__);

Class CUptolikeIntarget
{
	const PARTNER_ID = "cms";
	
	function ini()
    {
		global $APPLICATION;
		$dir = $APPLICATION->GetCurDir();
		$dirs = explode('/', $dir);
		if($dirs[1] == 'bitrix')
		{
			CJSCore::Init(array("jquery"));
		}
    }
	
	function userReg($email,$key)
	{

		if ($email !== '' && $key !== '') {
			$ch = curl_init();
			$jsondata = json_encode(array(
				'email' => $email,
				'key' => $key,
				'url' => "http://bitrix1.utlplugins.ru/",
				'cms' => 'bitrix')
			);

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type:application/json', 'Accept: application/json'));
			curl_setopt($ch, CURLOPT_URL, "https://intarget.ru/api/registration.json"); //intarget-dev.lembrd.com
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$server_output = curl_exec($ch);

			$json_result = json_decode($server_output);
			curl_close($ch);

			if (isset($json_result->status)) {
				if (($json_result->status == 'OK')) {
					return array('ok' => $json_result->payload->projectId);
				} elseif ($json_result->status == 'error') {
					if ($json_result->code == '403') {
						$json_result->message = GetMessage('INTARGET_TAB_MESS_3');
					}
					if ($json_result->code == '500') {
						$json_result->message = GetMessage('INTARGET_TAB_MESS_4');
					}
					if ($json_result->code == '404') {
						$json_result->message = GetMessage('INTARGET_TAB_MESS_5');
					}
					if (!isset($json_result->code)) {
						$json_result->message = GetMessage('INTARGET_TAB_MESS_6');
					}
					return array('error' => $json_result->message);
				}
			}
			return array('error' => GetMessage('INTARGET_TAB_MESS_7'));
		}
	}
}