<?php

/**
 * Class YotiConnectButton
 *
 * @author Simon Tong <simon.tong@yoti.com>
 */
class YotiConnectButton
{
    /**
     * @param null $redirect
     * @return string
     */
    public static function render($redirect = null)
    {
        global $wpdb;

        $testToken = null;
        if (YotiConnectHelper::mockRequests()) {
            $testToken = file_get_contents(__DIR__ . '/sdk/sample-data/connect-token.txt');
        }

        // no config? no button
        $config = YotiConnectHelper::getConfig();
        if (!$config && !$testToken) {
            return null;
        }

        // if connect url starts with 'https://staging' then we are in staging mode
        $isStaging = strpos(\Yoti\YotiClient::CONNECT_BASE_URL, 'https://staging') === 0;
        if ($isStaging)
        {
            // base url for connect
            $baseUrl = preg_replace('/^(.+)\/connect$/', '$1', \Yoti\YotiClient::CONNECT_BASE_URL);

            $script[] = sprintf('_ybg.config.qr = "%s/qr/";', $baseUrl);
            $script[] = sprintf('_ybg.config.service = "%s/connect/";', $baseUrl);
        }

        // add init()
        $script[] = '_ybg.init();';
        $linkButton = '<span
            data-yoti-application-id="' . $config['yoti_app_id'] . '"
            data-yoti-type="inline"
            data-yoti-scenario-id="' . $config['yoti_scenario_id'] . '"
            data-size="small">
            %s
        </span>
        <script>' . implode("\r\n", $script) . '</script>';

        if (!is_user_logged_in()) {
            $button = sprintf($linkButton, 'Log in with Yoti');
        }
        else {
            $currentUser = wp_get_current_user();
            $yotiId = get_user_meta($currentUser->ID, 'yoti_connect.identifier');
            if (!$yotiId) {
                return '';
//                $button = sprintf($linkButton, 'Link account with Yoti');
            }
            else {
                $url = site_url('wp-login.php') . '?yoti-connect=1&action=unlink&redirect=' . ($redirect ? '&redirect=' . rawurlencode($redirect) : '');
                $label = 'Unlink account from Yoti';
                $button = '<a class="yoti-connect-button" href="' . $url . '">' . $label . '</a>';
            }
        }

        $message = YotiConnectHelper::getFlash();
        $html = '<div class="yoti-connect"> ';
        if ($message) {
            $html .= '<div class="' . ($message['type'] == 'error' ? 'error' : 'message') . ' notice">' .
                '<p><strong>' . $message['message'] . '</strong></p>' .
                '</div>';
        }
        $html .= $button;
        $html .= '</div>';

        return $html;

//        if (!is_user_logged_in())
//        {
//            if (YotiConnectHelper::mockRequests())
//            {
//                $url = site_url('wp - login . php') . ' ? yoti - connect = 1 & action = link & token = ' . $testToken . ($redirect ? ' & redirect = ' . rawurlencode($redirect) : '');
//            }
//            else
//            {
//                $url = YotiConnectHelper::getLoginUrl();
//            }
//            $label = 'Sign on with Yoti';
//        }
//        else
//        {
//            $currentUser = wp_get_current_user();
//            $yotiId = get_user_meta($currentUser->ID, 'yoti_connect . identifier');
//            if (!$yotiId)
//            {
//                if (YotiConnectHelper::mockRequests())
//                {
//                    $url = site_url('wp - login . php') . ' ? yoti - connect = 1 & action = link & token = ' . $testToken . ($redirect ? ' & redirect = ' . rawurlencode($redirect) : '');
//                }
//                else
//                {
//                    $url = YotiConnectHelper::getLoginUrl();
//                }
//                $label = 'Link account to Yoti';
//            }
//            else
//            {
//                $url = site_url('wp - login . php') . ' ? yoti - connect = 1 & action = unlink & redirect = ' . ($redirect ? ' & redirect = ' . rawurlencode($redirect) : '');
//                $label = 'Unlink account from Yoti';
//            }
//        }
//
//        $message = YotiConnectHelper::getFlash();
//        $html = ' <div class="yoti-connect"> ';
//        if ($message) {
//            $html .= '<div class="' . ($message['type'] == 'error' ? 'error' : 'message') . ' notice"> ' .
//                '<p><strong> ' . $message['message'] . ' </strong></p> ' .
//                '</div> ';
//        }
//        $html .= '<a class="yoti-connect-button" href = "' . $url . '"> ' . htmlspecialchars($label) . '</a> ';
//        $html .= '</div> ';
//
//        return $html;
    }
}