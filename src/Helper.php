<?php
namespace Omnipay\Pingpp;

/**
 * Class Helper
 * @package Omnipay\Pingpp
 */
class Helper
{
    public static function getExtra($data)
    {
        $extra = array();

        switch ($data['channel']) {
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

        return $extra;
    }


    /* *
         * 验证 webhooks 签名方法：
         * raw_data：Ping++ 请求 body 的原始数据即 event ，不能格式化；
         * signature：Ping++ 请求 header 中的 x-pingplusplus-signature 对应的 value 值；
         * pub_key_path：读取你保存的 Ping++ 公钥的路径；
         * pub_key_contents：Ping++ 公钥，获取路径：登录 [Dashboard](https://dashboard.pingxx.com)->点击管理平台右上角公司名称->开发信息-> Ping++ 公钥
         */
    public static function verify_signature($raw_data, $signature, $pub_key_path)
    {
        $pub_key_contents = file_get_contents($pub_key_path);
        // php 5.4.8 以上，第四个参数可用常量 OPENSSL_ALGO_SHA256
        return openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, 'sha256');
    }
}
