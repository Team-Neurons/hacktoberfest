<?php

/**
 * Class DeviceCheck
 * check https://github.com/LeeWeiZhang/Utilities to learn how to call use function
 */
class DeviceCheck
{
    /** @serverVar array */
    protected $serverVar;

    public function __construct()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getServerVar():array
    {
        return $this->serverVar;
    }

    /**
     * @param array $serverVar
     * @return DeviceCheck
     */
    public function setServerVar($serverVar):self
    {
        $this->serverVar = $serverVar;

        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isMobile():bool
    {
        list($mobile_browser, $tablet_browser) = $this->matchMobileTablet();

        if ($tablet_browser > 0) {
            return true;
        } elseif ($mobile_browser > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string tablet | mobile | desktop
     * @throws \Exception
     */
    public function deviceType()
    {
        list($mobile_browser, $tablet_browser) = $this->matchMobileTablet();

        if ($tablet_browser > 0) {
            return "tablet";
        } elseif ($mobile_browser > 0) {
            return "mobile";
        } else {
            return "desktop";
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function matchMobileTablet()
    {
        if (!isset($this->serverVar['HTTP_USER_AGENT'])) {
            throw new \Exception("Server Variable HTTP_USER_AGENT Not Found");
        }
        if (!isset($this->serverVar['HTTP_ACCEPT'])) {
            throw new \Exception("Server Variable HTTP_ACCEPT Not Found");
        }
        $tablet_browser = 0;
        $mobile_browser = 0;

        $tabletPattern = '/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i';
        if (preg_match($tabletPattern, strtolower($this->serverVar['HTTP_USER_AGENT']))) {
            $tablet_browser++;
        }

        $mobilePattern = '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i';
        if (preg_match($mobilePattern, strtolower($this->serverVar['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }

        if (
            (strpos(strtolower($this->serverVar['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) ||
            ((isset($this->serverVar['HTTP_X_WAP_PROFILE']) or isset($this->serverVar['HTTP_PROFILE'])))
        ) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($this->serverVar['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-');

        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($this->serverVar['HTTP_USER_AGENT']), 'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            if (isset($this->serverVar['HTTP_X_OPERAMINI_PHONE_UA'])) {
                $phoneUA = $this->serverVar['HTTP_X_OPERAMINI_PHONE_UA'];
            } elseif (isset($this->serverVar['HTTP_DEVICE_STOCK_UA'])) {
                $phoneUA = $this->serverVar['HTTP_DEVICE_STOCK_UA'];
            } else {
                $phoneUA = "";
            }
            $stock_ua = strtolower($phoneUA);

            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
                $tablet_browser++;
            }
        }

        return [$mobile_browser, $tablet_browser];
    }
}
