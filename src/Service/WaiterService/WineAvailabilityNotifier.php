<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/23/2019
 * Time: 10:01 PM
 */

namespace App\Service\WaiterService;

use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;


class WineAvailabilityNotifier implements ConsumerInterface
{
    private  $entityManager;
    private  $mailer;
    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function execute(AMQPMessage $msg)
    {
        $wineInfo = json_decode($msg->body);
        $wineId = $wineInfo['wine_id'];
        $wine =  $this->getWine($wineId);
        $orders = $this->getOrdersWithWineWhenDuringWineUnavailablePerid($wine);

        foreach ($orders as $order){
           // $this->notifyCustomerViaEmailOfWineAvailability($order->getCustomerContactEmail(),$wine);
        }
    }

    /*
     * We check for check for customer who have made request for wine between
     * last available date and new available date.
     */
    private function getOrdersWithWineWhenDuringWineUnavailablePerid($wine){

        $wineLastTwoAvailableDateUpdateLog = $this->entityManager->getRepository('App\Entity\WineLog')->getWineLastTwoUpdate($wine);
        $lastWineAvailableDate = $wineLastTwoAvailableDateUpdateLog[1];
        $winePenultimateAvailableDate = $wineLastTwoAvailableDateUpdateLog[0];

        return $this->entityManager->getRepository('App\Entity\Order')->getOrdersWithUnavailableResponseForWineByDate($wine, $lastWineAvailableDate, $winePenultimateAvailableDate);
    }

    private function getWine($wineId){
        return $this->entityManager->getRepository('Wine')->findOneBy(['id' => $wineId]);
    }


    private function notifyCustomerViaEmailOfWineAvailability($customerEmail, $wine)
    {
        $message = ['email' => $customerEmail,'availableWine' => $wine ];
        $this->sendMail($message);

    }

    private function sendMail($message){

        $mail = $this->getMailBody($message);
        $this->mailer->send($message);
    }

    private function getMailBody($content){
        $message = (new \Swift_Message('Wine Now Available'))
            ->setFrom('winery@winery.com')
            ->setTo($content['email'])
            ->setBody(
                $this->renderView(
                    'templates/Mail/mail.html.twig',
                    ['wine' => $content['wine']->getTitle()]
                ),
                'text/html'
            );

        return $message;
    }


}
