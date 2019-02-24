<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/24/2019
 * Time: 6:42 PM
 */

namespace App\Log;


abstract class Logger
{
    public function logAction($action,$order_id, $message){
        $logMessage = [
            'action' =>$action,
            'body' => [
                'id' => $order_id,
                'message'=>$message
            ]
        ];

        $this->persistLogRecordToDB($logMessage);
    }

    abstract function persistLogRecordToDB($message);
}