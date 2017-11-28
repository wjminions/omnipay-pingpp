<?php

namespace Omnipay\Pingpp\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Pingpp\Helper;

/**
 * Class ChargeQueryRequest
 *
 * @package Omnipay\Pingpp\Message
 */
class ChargeQueryRequest extends AbstractChargeRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate(
            'app_key',
            'order_no'
        );

        $data = array(
            //app_id
            'app'              => $this->getApp(),
            //支付方式
            'channel'          => $this->getChannel(),
            //callback地址
            'callback'         => $this->getCallback(),
            //app_key
            'app_key'          => $this->getAppKey(),
            //货币
            'currency'         => $this->getCurrency(),
            //私钥地址
            'private_key_path' => $this->getPrivateKeyPath(),
            //交易id
            'order_no'         => $this->getOrderNo()
        );

        return $data;
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {

        \Pingpp\Pingpp::setApiKey($data['app_key']);           // 设置 API Key
        \Pingpp\Pingpp::setPrivateKeyPath($data['private_key_path']);   // 设置私钥

        // 查询支付成功列表
        $ch = \Pingpp\Charge::all(array(
            'limit'    => 10,
            'app'      => array('id' => $data['app']),
            'channel'  => $data['channel'],
            'paid'     => true,
            'refunded' => false,
            'reversed' => false
        ));

        $data['is_paid'] = false;

        foreach ($ch->data as $charge) {
            if ($charge['order_no'] == $data['order_no'] && $charge['paid'] && ! $charge['refunded'] && ! $charge['reversed']) {
                $data['is_paid'] = true;

                $data['id']              = $charge->id;
                $data["object"]          = $charge->object;
                $data["created"]         = $charge->created;
                $data["livemode"]        = $charge->livemode;
                $data["paid"]            = $charge->paid;
                $data["refunded"]        = $charge->refunded;
                $data["reversed"]        = $charge->reversed;
                $data["app"]             = $charge->app;
                $data["channel"]         = $charge->channel;
                $data["client_ip"]       = $charge->client_ip;
                $data["amount"]          = $charge->amount;
                $data["amount_settle"]   = $charge->amount_settle;
                $data["currency"]        = $charge->currency;
                $data["subject"]         = $charge->subject;
                $data["body"]            = $charge->body;
                $data["time_paid"]       = $charge->time_paid;
                $data["time_expire"]     = $charge->time_expire;
                $data["time_settle"]     = $charge->time_settle;
                $data["amount_refunded"] = $charge->amount_refunded;
                $data["failure_code"]    = $charge->failure_code;
                $data["failure_msg"]     = $charge->failure_msg;
                $data["description"]     = $charge->description;

                break;
            }
        }

        return $data;// 输出 Ping++ 返回 Charge 对象
    }
}
