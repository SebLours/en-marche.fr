<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CoreController;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Controller\HelperController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class FilterConfiguratorListener
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $controllerClass = $controller[0];

        if ($controllerClass instanceof CRUDController
            || $controllerClass instanceof CoreController
            || $controllerClass instanceof HelperController
        ) {
            $this->em->getFilters()->disable('enabled');
        }
    }
}
