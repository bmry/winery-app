<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/20/2019
 * Time: 11:54 AM
 */

namespace App\Command;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Wine;

class RSSFeedLoaderCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:load_rss_feed')
            ->setDescription('This helps extract the RSS Feed content into the databae');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Process started');

        $this->loadXMLIntoDatabase();

        $output->writeln('Process finished');
    }

    private function getXMLFromURL($url){
        $xmlReader = new \XMLReader();
        $xmlReader->open($url);
        return $xmlReader;
    }

    private function createWineObjectFromXML($xml){
        $itemNode = $xml;
        $wine = new Wine();
        $wine->setTitle($itemNode->title);
        $wine->setLink($itemNode->link);
        $wine->setDescription($itemNode->description);
        $wine->setAuthor($itemNode->author);
        $wine->setGuid($itemNode->guid);

        $wine->setPublishDate(new \DateTime($itemNode->pubDate));
        $this->entityManager->persist($wine);
    }

    private function loadXMLIntoDatabase(){
        $path = 'https://www.winespectator.com/rss/rss?t=dwp';
        $xmlReader =  $this->getXMLFromURL($path);

        while ($xmlReader->read() && $xmlReader->name !== 'item');
        while($xmlReader->read()){
            if((\XMLReader::ELEMENT !== $xmlReader->nodeType) && ($xmlReader->name !== 'item')){
                continue;
            }
            $itemNode = simplexml_load_string($xmlReader->readOuterXML());
            if(!$this->isRecordExist($itemNode)){
                continue;
            }

            $this->createWineObjectFromXML($itemNode);
        }
        $this->entityManager->flush();
    }

    private function isRecordExist($record){
        $exist = false;
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['title' => $record->title]);
        if(!$wine){
            $exist = true;
        }

        return $exist;
    }
}