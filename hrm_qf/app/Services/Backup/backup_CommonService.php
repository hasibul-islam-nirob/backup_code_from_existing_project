<?php

namespace App\Services;

class backup_CommonService
{
    /**
     * old project code
     */
    public function smsApi($sender = "8804445629073", $body = null, $receiver = "8801680954856")
    {

        //        $receiver = "8801613770999";

        $user          = "user";
        $pass          = "pass";
        $client_ref_id = $receiver . date();
        /* for Only English Text */
        //        $sid = "BAERAENG";

        /* for Only Bangla Text */
        $sid  = "BAERABANGLA";
        $body = $this->convertBanglatoUnicode($body);

        $url   = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
        $param = "user=$user&pass=$pass&sms[0][0]= $receiver &sms[0][1]=" . urlencode($body) . "&sms[0][2]=$client_ref_id&sid=$sid";
        $crl   = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);

        return $response;
    }

    public function convertBanglatoUnicode($BanglaText = null)
    {
        $unicodeBanglaTextForSms = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));
        return $unicodeBanglaTextForSms;
    }

    public function sendMailFormMailServer($to = null, $subject = null, $message = null)
    {
        //$to ='mozahid.hs@gmail.com';

        /*
         * This is BD Housing Mail server,
         */

        /*
        $postUrl = "http://mail5.bdhousing.com:8008/bdhousing/sendmail.php";
        $fields = array(
        'to' => urlencode($to),
        'subject' => urlencode($subject),
        'message' => urlencode($message),
        'php_master' => true);

        // in this example, POST request was made using PHP's CURL
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        // response of the POST request
        $response = curl_exec($ch);
        ob_end_clean();
        curl_close($ch);
         */

        // write out the response
        //return $response;

        return 1;
    }

    public function sendMailFromVendor($emails = null, $key = null, $hash = null, $name = null)
    {
        $this->viewBuilder()->layout(false);
        $this->render(false);
        //        $url = Router::url(array('controller' => 'Homes', 'action' => 'verifyRegistration'), true) . '/' . $key . '_' . $hash;
        //        $url2 = Router::url(array('controller' => 'Homes', 'action' => 'unSubscribe'), true) . '/' . $key . '_' . $hash;
        //        $wrappedurl = wordwrap($url, 1000);
        //        $wrappedur2 = wordwrap($url2, 1000);
        //        $path = Router::url('/', true);

        $wrappedurl = false;
        $wrappedur2 = false;
        $path       = false;

        $this->set('path', $path);
        $this->set('name', $name);
        $this->set('emails', $emails);
        $this->set('wrappedurl', $wrappedurl);
        $this->set('wrappedur2', $wrappedur2);

        $email = new Email('bdhousing');

        try {
            $email->to($emails)
                ->template('default')
                ->emailFormat('both')
                ->subject('Verify Registration of bdHousing Account')
                ->from('noreply@bdhousing.com')
                ->viewVars(compact('path', 'name', 'emails', 'wrappedurl', 'wrappedur2'))
                ->send();

            $pass_reset_mail_sent = 1;
            $this->set('pass_reset_mail_sent', $pass_reset_mail_sent);
        } catch (Exception $e) {
            $e->getMessage();
        }
        //  return true;
    }

    /**
     * old project code
     */

}
