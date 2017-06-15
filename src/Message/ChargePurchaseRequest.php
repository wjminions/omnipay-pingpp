<?php

namespace Omnipay\Pingpp\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Pingpp\Helper;

/**
 * Class ChargePurchaseRequest
 * @package Omnipay\Pingpp\Message
 */
class ChargePurchaseRequest extends AbstractChargeRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validateData();

        $data = array (
            //商户订单号
            'order_no'        => $this->getOrderNo(),
            //交易金额，单位分
            'amount'         => $this->getAmount(),
            //主题
            'subject' => $this->getSubject(),
            //内容
            'body' => $this->getBody(),
            //app_id
            'app' => $this->getApp(),
            //支付方式
            'channel' => $this->getChannel(),
            //callback地址
            'callback' => $this->getCallback(),
            //app_key
            'app_key' => $this->getAppKey(),
            //货币
            'currency' => $this->getCurrency(),
            //私钥地址
            'private_key_path' => $this->getPrivateKeyPath()
        );

        return $data;
    }


    private function validateData()
    {
        $this->validate(
            'order_no',
            'amount',
            'subject',
            'body',
            'app',
            'channel',
            'callback',
            'app_key',
            'currency',
            'private_key_path'
        );
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     *
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        require dirname(__FILE__) . '/../init.php';

        // 此处为 Content-Type 是 application/json 时获取 POST 参数的示例
        $channel = strtolower($data['channel']);
        $amount = $data['amount'];
        $orderNo = substr(md5(time()), 0, 12);

        /**
         * 设置请求签名密钥，密钥对需要你自己用 openssl 工具生成，如何生成可以参考帮助中心：https://help.pingxx.com/article/123161；
         * 生成密钥后，需要在代码中设置请求签名的私钥(rsa_private_key.pem)；
         * 然后登录 [Dashboard](https://dashboard.pingxx.com)->点击右上角公司名称->开发信息->商户公钥（用于商户身份验证）
         * 将你的公钥复制粘贴进去并且保存->先启用 Test 模式进行测试->测试通过后启用 Live 模式
         */

        \Pingpp\Pingpp::setApiKey($data['app_key']);                                         // 设置 API Key
        \Pingpp\Pingpp::setPrivateKeyPath($data['private_key_path']);   // 设置私钥

        // 设置私钥内容方式2
        // \Pingpp\Pingpp::setPrivateKey(file_get_contents(__DIR__ . '/your_rsa_private_key.pem'));

        /**
         * $extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array()。
         * 以下 channel 仅为部分示例，未列出的 channel 请查看文档 https://pingxx.com/document/api#api-c-new；
         * 或直接查看开发者中心：https://www.pingxx.com/docs/server；包含了所有渠道的 extra 参数的示例；
         */
        $extra = array();
        switch ($channel) {
            case 'alipay_wap':
                $extra = array(
                    // success_url 和 cancel_url 在本地测试不要写 localhost ，请写 127.0.0.1。URL 后面不要加自定义参数
                    'success_url' => $data['callback'],
                    'cancel_url' => $data['callback']
                );
                break;
            case 'bfb_wap':
                $extra = array(
                    'result_url' => $data['callback'],// 百度钱包同步回调地址
                    'bfb_login' => true// 是否需要登录百度钱包来进行支付
                );
                break;
            case 'upacp_wap':
                $extra = array(
                    'result_url' => $data['callback']// 银联同步回调地址
                );
                break;
            case 'wx_pub':
                $extra = array(
                    'open_id' => 'openidxxxxxxxxxxxx'// 用户在商户微信公众号下的唯一标识，获取方式可参考 pingpp-php/lib/WxpubOAuth.php
                );
                break;
            case 'wx_pub_qr':
                $extra = array(
                    'product_id' => $data['order_no']// 为二维码中包含的商品 ID，1-32 位字符串，商户可自定义
                );
                break;
            case 'yeepay_wap':
                $extra = array(
                    'product_category' => '1',// 商品类别码参考链接 ：https://www.pingxx.com/api#api-appendix-2
                    'identity_id'=> 'your identity_id',// 商户生成的用户账号唯一标识，最长 50 位字符串
                    'identity_type' => 1,// 用户标识类型参考链接：https://www.pingxx.com/api#yeepay_identity_type
                    'terminal_type' => 1,// 终端类型，对应取值 0:IMEI, 1:MAC, 2:UUID, 3:other
                    'terminal_id'=>'your terminal_id',// 终端 ID
                    'user_ua'=>'your user_ua',// 用户使用的移动终端的 UserAgent 信息
                    'result_url'=>$data['callback']// 前台通知地址
                );
                break;
            case 'jdpay_wap':
                $extra = array(
                    'success_url' => $data['callback'],// 支付成功页面跳转路径
                    'fail_url'=> $data['callback'],// 支付失败页面跳转路径
                    /**
                     *token 为用户交易令牌，用于识别用户信息，支付成功后会调用 success_url 返回给商户。
                     *商户可以记录这个 token 值，当用户再次支付的时候传入该 token，用户无需再次输入银行卡信息
                     */
                );
                break;
        }


        try {
            $ch = \Pingpp\Charge::create(
                array(
                    //请求参数字段规则，请参考 API 文档：https://www.pingxx.com/api#api-c-new
                    'subject'   => $data['subject'],
                    'body'      => $data['body'],
                    'amount'    => $data['amount'],//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                    'order_no'  => $data['order_no'],// 推荐使用 8-20 位，要求数字或字母，不允许其他字符
                    'currency'  => $data['currency'],
                    'extra'     => $extra,
                    'channel'   => $data['channel'],// 支付使用的第三方支付渠道取值，请参考：https://www.pingxx.com/api#api-c-new
                    'client_ip' => $_SERVER['REMOTE_ADDR'],// 发起支付请求客户端的 IP 地址，格式为 IPV4，如: 127.0.0.1
                    'app'       => array('id' => $data['app'])
                )
            );
            echo $ch;// 输出 Ping++ 返回的支付凭据 Charge
        } catch (\Pingpp\Error\Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                echo $e->getHttpBody();
            } else {
                echo $e->getMessage();
            }
        }
        exit;

        // 查询 charge 对象
        $charge_id = 'ch_L8qn10mLmr1GS8e5OODmHaL4';
        try {
            $charge = \Pingpp\Charge::retrieve($charge_id);
            echo $charge;
        } catch (\Pingpp\Error\Base $e) {
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                echo $e->getHttpBody();
            } else {
                echo $e->getMessage();
            }
        }
        exit;


        // 查询 charge 对象列表
        $search_params = [
            'app'   => array('id' => APP_ID)            // 此参数必填
        ];
        try {
            $charge_all = \Pingpp\Charge::all($search_params);
            echo $charge_all;                                                     // 输出 Ping++ 返回的 charge 对象列表
        } catch (\Pingpp\Error\Base $e) {
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                echo $e->getHttpBody();
            } else {
                echo $e->getMessage();
            }
        }





        return $this->response = new ChargePurchaseResponse($this, $data);
    }
}
