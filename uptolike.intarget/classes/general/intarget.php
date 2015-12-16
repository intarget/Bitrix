<?
IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Page\Asset;

Class CUptolikeIntarget
{
    function ini($arParams)
    {
        global $APPLICATION;
        $js_code = COption::GetOptionString("uptolike.intarget", "intarget_code");
        $js_code = htmlspecialcharsBack($js_code);

        if (!empty($js_code)) {
            $APPLICATION->AddHeadString($js_code, true);
            $js_code = '<script type="text/javascript">
            document.addEventListener("click", function(event){
                var current = event.srcElement || event.currentTarget || event.target;
                if (current.id.match("buy_link")) {
                    var productMatches = current.id.match(/([0-9]+)_buy_link/);
                    if (productMatches) {
                            inTarget.event("add-to-cart");
                     }
                }
            });

            document.addEventListener("click", function(event){
                var current = event.srcElement || event.currentTarget || event.target;
                if (current.href && current.href.match("action=delete")) {
                        inTarget.event("del-from-cart");
                }
            });
        </script>';

            Asset::getInstance()->addString($js_code);
            //�������� ��������
            if (CModule::IncludeModule("catalog")) {
                global $APPLICATION;
                $dir = $APPLICATION->GetCurDir();
                $dirs = explode('/', $dir);
                if (($dirs[1] == 'e-store' && empty($dirs[4])) || ($dirs[1] == 'catalog' && empty($dirs[3]))) {
                    $js_code = '<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event("cat-view");
                            });
                        })(window, "inTargetCallbacks");
                    </script>';
                    Asset::getInstance()->addString($js_code);
                }
            }
            //�������� ������
            if (CModule::IncludeModule("catalog")) {
                global $APPLICATION;
                $dir = $APPLICATION->GetCurDir();
                $dirs = explode('/', $dir);
                if (($dirs[1] == 'e-store' && !empty($dirs[4])) || ($dirs[1] == 'catalog' && !empty($dirs[3]))) {
                    $js_code = '<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event("item-view");
                            });
                        })(window, "inTargetCallbacks");
                    </script>';
                    Asset::getInstance()->addString($js_code);
                }
            }

            if ($APPLICATION->get_cookie("INTARGET_REG_SUCCESS") == "Y") {
                $js_code = "<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event('user-reg');
                            });
                        })(window, 'inTargetCallbacks');
                    </script>";
                Asset::getInstance()->addString($js_code);
                $APPLICATION->set_cookie("INTARGET_REG_SUCCESS", "N");
            }

            if ($APPLICATION->get_cookie("INTARGET_ORDER_SUCCESS") == "Y") {
                $js_code = "<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event('success-order');
                            });
                        })(window, 'inTargetCallbacks');
                    </script>";
                Asset::getInstance()->addString($js_code);
                $APPLICATION->set_cookie("INTARGET_ORDER_SUCCESS", "N");
            }
        }
    }

    static function order($ID)
    {
        $arOrder = CSaleOrder::GetByID(intval($ID));
        $filter = Array("EMAIL" => $arOrder['USER_EMAIL']);
        $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter);
        $res = $rsUsers->Fetch();
        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id)
            return;
        global $APPLICATION;
        $APPLICATION->set_cookie("INTARGET_ORDER_SUCCESS", "Y", time() + 60);

        //����������� ������ ������������ ��� ���������� ������
        if ($res['LAST_LOGIN'] == $res['DATE_REGISTER']) {
            $APPLICATION->set_cookie("INTARGET_REG_SUCCESS", "Y", time() + 60);
        }
    }

    //���� ����������� ������������
    function OnAfterUserRegisterHandler(&$arFields)
    {
        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id)
            return;

        global $APPLICATION;
        $APPLICATION->set_cookie("INTARGET_REG_SUCCESS", "Y", time() + 60);
    }

    static public function userReg($email, $key)
    {
        $ch = curl_init();

        $jsondata = json_encode(array(
                'email' => $email,
                'key' => $key,
                'url' => CUptolikeIntarget::GetCurrUrl(),
                'cms' => 'bitrix')
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_URL, "https://intarget.ru/api/registration.json"); //intarget-dev.lembrd.com
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);
        return json_decode($server_output);
    }

    static public function GetCurrUrl()
    {
        $result = '';
        $default_port = 80;

        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
            $result .= 'https://';
            $default_port = 443;
        } else {
            $result .= 'http://';
        }

        $result .= $_SERVER['SERVER_NAME'];

        if ($_SERVER['SERVER_PORT'] != $default_port) {
            $result .= ':' . $_SERVER['SERVER_PORT'];
        }
        return $result;
    }

    static public function jsCode($id)
    {
        $jscode = "<!-- INTARGET CODE -->
                <script type='text/javascript'>
                    (function(d, w, c) {
                      w[c] = {
                        projectId:" . $id . "
                      };
                      var n = d.getElementsByTagName('script')[0],
                      s = d.createElement('script'),
                      f = function () { n.parentNode.insertBefore(s, n); };
                      s.type = 'text/javascript';
                      s.async = true;
                      s.src = '//rt.intarget.ru/loader.js';
                      if (w.opera == '[object Opera]') {
                          d.addEventListener('DOMContentLoaded', f, false);
                      } else { f(); }
                    })(document, window, 'inTargetInit');
                </script>
                <!-- /INTARGET CODE -->";
        return $jscode;
    }
}