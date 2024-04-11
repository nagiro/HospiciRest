<?php

require_once DATABASEDIR.'Tables/OptionsModel.php';

class ElasticEmail
{

    public function SendEmailToAdmin($idSite, $subject, $body) {
        
        $OO = new OptionsModel();
        $FromEmail = $OO->getOption("MAIL_FROM", $idSite);
        $FromName = $OO->getOption("MAIL_NAME", $idSite); 

        return $this->SendEmail($FromEmail, $FromEmail, $FromName, $subject, $body );
               
    }

    public function SendEmail($to, $FromEmail, $FromName, $subject, $body, $files = array() ) {
        $url = 'https://api.elasticemail.com/v2/email/send';
                        
        try{
                $post = array('from' => $FromEmail,
                'fromName' => $FromName,
                'apikey' => '882D1E9420DA8EFC9A20F712B96703AC6D9D06099C059D20325B91A467DB449A558C4DAD46C13DC2712D8132F35847D3',
                'subject' => $subject,
                'to' => $to,
                'bodyHtml' => $body,                
                'isTransactional' => false);
                
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ));
                
                $result=curl_exec ($ch);
                $RES = json_decode($result, true);                                
                curl_close ($ch);
                
                if( $RES['success'] === false ){
                    throw new Exception($RES['error']);
                }                
                
                return true;
                
        }
        catch(Exception $ex){
            return false;            
        }        
    }
}