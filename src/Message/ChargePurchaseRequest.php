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
        require dirname(__FILE__) . '/../../../../pingplusplus/pingpp-php/init.php';

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
        $extra = Helper::getExtra($data);

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

            $charge = $ch->__toJSON();
        } catch (\Pingpp\Error\Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                $charge = $e->getHttpBody();
            } else {
                $charge = $e->getMessage();
            }
        }

        return $this->response = new ChargePurchaseResponse($this, (array) json_decode($charge));
    }
}
