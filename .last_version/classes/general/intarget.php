<?
/** var CMain $APPLICATION */
IncludeModuleLangFile(__FILE__);

Class CUptolikeIntarget
{
    function ini()
    {
        global $APPLICATION;
        $dir = $APPLICATION->GetCurDir();
        $dirs = explode('/', $dir);
        if ($dirs[1] == 'bitrix') {
            CJSCore::Init(array("jquery"));
        }
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