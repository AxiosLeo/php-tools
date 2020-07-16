<?php

declare(strict_types=1);

namespace tpr\tools;

class Mail
{
    use InstanceTrait;

    private $mail_config = [
        'is_smtp'     => true,
        'is_html'     => true,
        'host'        => 'smtp.qq.com',
        'port'        => 465,
        'smtp_auth'   => true,
        'username'    => '',
        'password'    => '',
        'email'       => '',
        'from_name'   => '',
        'smtp_secure' => 'ssl',
        'char_set'    => 'UTF-8',
    ];

    private $client;

    public function config(array $config = []): self
    {
        $this->mail_config = array_merge($this->mail_config, $config);

        return $this;
    }

    public function client(bool $debug = false)
    {
        $mail = new \PHPMailer();
        if (isset($config['is_smtp']) && $config['is_smtp']) {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
        }
        if ($debug) {
            $mail->SMTPDebug = 1;
        }

        $mail->Host       = $this->mail_config['host']; //smtp服务器地址
        $mail->Port       = $this->mail_config['port']; //设置ssl连接smtp服务器的远程服务器端口号
        $mail->SMTPAuth   = $this->mail_config['smtp_auth'];
        $mail->SMTPSecure = $this->mail_config['smtp_secure']; //设置使用ssl加密方式登录鉴权
        $mail->Username   = $this->mail_config['username']; //smtp登录的账号
        $mail->Password   = $this->mail_config['password']; //smtp登录的密码

        //设置发件人邮箱地址
        $mail->From     = $this->mail_config['email'];
        $mail->FromName = $this->mail_config['from_name'];

        if ($this->mail_config['is_html']) {
            //邮件正文是否为html编码
            $mail->isHTML(true);
        }

        //设置发送的邮件的编码
        $mail->CharSet = $this->mail_config['char_set'];

        return $mail;
    }
}
