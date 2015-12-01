<?
/** var CMain $APPLICATION */
IncludeModuleLangFile(__FILE__);

Class CUptolikeIntarget
{
    function ini()
    {
        global $APPLICATION;
        $js_code = COption::GetOptionString("uptolike.intarget", "intarget_code");
        $js_code = htmlspecialcharsBack($js_code);
        if ($APPLICATION->get_cookie("INTARGET_REG") == 1) {
            $js_code .= "<script>
                    (function(w, c) {
                        w[c] = w[c] || [];
                        w[c].push(function(inTarget) {
                            inTarget.event('user-reg');
                        });
                    })(window, 'inTargetCallbacks');
                    console.log('user-reg');
                    </script>";
            $APPLICATION->set_cookie("INTARGET_REG", 0);
        }

        if ($APPLICATION->get_cookie("INTARGET_ADD_ITEM") == 1) {
            $js_code .= "<script>
                    (function(w, c) {
                        w[c] = w[c] || [];
                        w[c].push(function(inTarget) {
                            inTarget.event('add-to-cart');
                        });
                    })(window, 'inTargetCallbacks');
                    console.log('add-to-cart');
                    </script>";
        }

        if ($APPLICATION->get_cookie("INTARGET_DEL_ITEM") == 1) {
            $js_code .= "<script>
                    (function(w, c) {
                        w[c] = w[c] || [];
                        w[c].push(function(inTarget) {
                            inTarget.event('del-from-cart');
                        });
                    })(window, 'inTargetCallbacks');
                    console.log('del-from-cart');
                    </script>";
        }

        $APPLICATION->AddHeadString($js_code, true);
        $APPLICATION->set_cookie("INTARGET_ADD_ITEM", 0);
        $APPLICATION->set_cookie("INTARGET_DEL_ITEM", 0);
    }

    //просмотр товара
    static function productView($arResult)
    {
        if ($arResult["ID"] != "")
            $arResult["PRODUCT_ID"] = $arResult["ID"];

        if (class_exists("DataManager"))
            return false;

        global $APPLICATION;

        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id)
            return true;

        $js_code = "<script>
                    (function(w, c) {
                        w[c] = w[c] || [];
                        w[c].push(function(inTarget) {
                            inTarget.event('item-view');
                        });
                    })(window, 'inTargetCallbacks');
                    console.log('item-view');
                    </script>";
        $APPLICATION->AddHeadString($js_code, true);
        return true;
    }

    static function updateCart($id, $arFields = false) {
        $js_code = "<script>
                    (function() {
                       inTarget.event('cart_update');
                    })(window, 'inTargetCallbacks');
                    console.log('cart_update');
                    </script>";
        echo $js_code;
        return;

    }

    static function addToCart($id, $arFields)
    {
//        global $APPLICATION;
//        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");
//
//        if (!$intarget_id)
//            return true;
//
//        $APPLICATION->set_cookie("INTARGET_ADD_ITEM", 1);
//        return true;
        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id)
            return true;

        $js_code = "<script>
                    (function() {
                       inTarget.event('add-to-cart');
                    })(window, 'inTargetCallbacks');
                    console.log('add-to-cart');
                    </script>";
        echo $js_code;
        return;
    }

    //удаление из корзины
    static function deleteFromCart($ID, &$arFields)
    {
        global $APPLICATION;
        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id)
            return true;

        $APPLICATION->set_cookie("INTARGET_DEL_ITEM", 1);
        return true;
//        global $APPLICATION;
//        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");
//
//        if (!$intarget_id)
//            return true;
//
//        $APPLICATION->set_cookie("INTARGET_DEL_ITEM", 1);
//        return true;
    }

    //цель регистрация пользователя
    function OnAfterUserRegister(&$arFields)
    {
        // если регистрация успешна то
        if ($arFields["USER_ID"] > 0) {
            global $APPLICATION;
            $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

            if (!$intarget_id)
                return true;

            $APPLICATION->set_cookie("INTARGET_REG", 1);
            return true;
        }
        return true;
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
        curl_setopt($ch, CURLOPT_URL, "http://intarget-dev.lembrd.com/api/registration.json"); //intarget-dev.lembrd.com
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
                      s.src = '//rt.intarget-dev.lembrd.com/loader.js';
                      if (w.opera == '[object Opera]') {
                          d.addEventListener('DOMContentLoaded', f, false);
                      } else { f(); }
                    })(document, window, 'inTargetInit');
                    console.log('intarget_script');
                </script>
                <!-- /INTARGET CODE -->";
        return $jscode;
    }
}