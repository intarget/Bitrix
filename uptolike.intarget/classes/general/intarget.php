<?
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\Page\Asset;

Class CUptolikeIntarget {
    function ini() {
        if (defined('ADMIN_SECTION')) return;

        global $APPLICATION;
        $js_code = COption::GetOptionString("uptolike.intarget", "intarget_code");
        $js_code = htmlspecialcharsBack($js_code);

        if (!empty($js_code)) {
            $APPLICATION->AddHeadString($js_code, true);

            //����������/�������� ������
            $js_code = '<script type="text/javascript">
                document.addEventListener("click", function(event){
                    var current = event.srcElement || event.currentTarget || event.target;
                    if (current.id.match("buy_link")) {
                        var productMatches = current.id.match(/([0-9]+)_buy_link/);
                        if (productMatches) {
                            document.cookie = "INTARGET_ADD=Y; path=/;";
                        }
                    }
                });
                document.addEventListener("click", function(event){
                    var current = event.srcElement || event.currentTarget || event.target;
                    if (current.href && (current.href.match("Action=delete") || current.href.match("action=delete"))) {
                        document.cookie = "INTARGET_DEL=Y; path=/;";
                    }
                });

                document.addEventListener("click", function(event){
                    var current = event.srcElement || event.currentTarget || event.target;
                    if (current.href && (current.href.match("Action=delete") || current.href.match("action=delete"))) {
                        document.cookie = "INTARGET_DEL=Y; path=/;";
                    }
                });
                </script>';
            Asset::getInstance()->addString($js_code);

            $js_code = "<script>
                function getCookie(name) {
                  var matches = document.cookie.match(new RegExp(
                    \"(?:^|; )\" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + \"=([^;]*)\"
                  ));
                  return matches ? decodeURIComponent(matches[1]) : 'N';
                }

                var intarget_add = getCookie('INTARGET_ADD');
                if(intarget_add && intarget_add == 'Y') {
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event('add-to-cart');
                                console.log('add-to-cart');
                            });
                        })(window, 'inTargetCallbacks');
                        document.cookie = 'INTARGET_ADD=N; path=/;';
                }

                var intarget_del = getCookie('INTARGET_DEL');
                if(intarget_del && intarget_del == 'Y') {
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event('del-from-cart');
                                console.log('del-from-cart');
                            });
                        })(window, 'inTargetCallbacks');
                        document.cookie = 'INTARGET_DEL=N; path=/;';
                }
                </script>";
            Asset::getInstance()->addString($js_code);

            //�������� ��������
            if (CModule::IncludeModule("catalog")) {
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
                $dir = $APPLICATION->GetCurDir();
                $dirs = explode('/', $dir);
                if (($dirs[1] == 'e-store' && !empty($dirs[4])) || ($dirs[1] == 'catalog' && !empty($dirs[3]))) {
                    $js_code = '<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event("item-view");
                                console.log("item-view");
                            });
                        })(window, "inTargetCallbacks");
                    </script>';
                    Asset::getInstance()->addString($js_code);
                }
            }

            //�������� ���������� ������
            if ($APPLICATION->get_cookie("INTARGET_REG_SUCCESS") == "Y") {
                $js_code = "<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event('user-reg');
                                console.log('user-reg');
                            });
                        })(window, 'inTargetCallbacks');
                    </script>";
                Asset::getInstance()->addString($js_code);
                $APPLICATION->set_cookie("INTARGET_REG_SUCCESS", "N");
            }

            if (CModule::IncludeModule("catalog")) {
                $dir = $APPLICATION->GetCurDir();
                $dirs = explode('/', $dir);
                if ($dirs[1] == 'personal' && $dirs[2] == 'order' && $dirs[3] == 'make' && empty($dirs[4]) && $_GET['ORDER_ID']) {
                    $js_code = '<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event("success-order");
                                console.log("success-order");
                            });
                        })(window, "inTargetCallbacks");
                    </script>';
                    Asset::getInstance()->addString($js_code);
                }
            }

            if ($APPLICATION->get_cookie("INTARGET_ORDER_SUCCESS") == "Y") {
                $js_code = "<script>
                        (function(w, c) {
                            w[c] = w[c] || [];
                            w[c].push(function(inTarget) {
                                inTarget.event('success-order');
                                console.log('success-order');
                            });
                        })(window, 'inTargetCallbacks');
                    </script>";
                Asset::getInstance()->addString($js_code);
                $APPLICATION->set_cookie("INTARGET_ORDER_SUCCESS", "N");
            }
        }
    }

    static function order($ID) {
        $arOrder = CSaleOrder::GetByID(intval($ID));
        $filter = Array("EMAIL" => $arOrder['USER_EMAIL']);
        $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter);
        $res = $rsUsers->Fetch();
        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id) return;
        global $APPLICATION;
        $APPLICATION->set_cookie("INTARGET_ORDER_SUCCESS", "Y", time() + 60);

        //����������� ������ ������������ ��� ���������� ������
        if ($res['LAST_LOGIN'] == $res['DATE_REGISTER']) {
            $APPLICATION->set_cookie("INTARGET_REG_SUCCESS", "Y", time() + 60);
        }
    }

    //���� ����������� ������������
    function OnAfterUserRegisterHandler(&$arFields) {
        $intarget_id = COption::GetOptionString("uptolike.intarget", "intarget_id");

        if (!$intarget_id) return;

        global $APPLICATION;
        $APPLICATION->set_cookie("INTARGET_REG_SUCCESS", "Y", time() + 60);
    }

    static public function userReg($email, $key) {
        $ch = curl_init();

        $jsondata = json_encode(array('email' => $email, 'key' => $key, 'url' => CUptolikeIntarget::GetCurrUrl(), 'cms' => 'bitrix'));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_URL, "https://intarget.ru/api/registration.json"); //intarget-dev.lembrd.com
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        if (empty($server_output)) {
            $info = curl_error($ch);
        }
        curl_close($ch);
        if (!empty($info)) {
            return $info;
        }
        return json_decode($server_output);
    }

    static public function GetCurrUrl() {
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

    static public function jsCode($id) {
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