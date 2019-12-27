<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class RabbitMqController extends AbstractController
{
    /**
     * @Route(
     *     methods={"POST"},
     *     name="post-rabbit-mq",
     *     path={"/api/rabbit-mq"},
     * )
     */
    public function post(Request $request, ContainerInterface $container)
    {
        $directory = '/home/bib/Bureau/email_files';
        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $container->get('old_sound_rabbit_mq.file_producer')->publish(
                    json_encode(array('fileName' => $file,'filePath' => $directory . '/' . $file))
                );
            }
        }

        return new Response(json_encode(array("total" => 1111)));
    }
}