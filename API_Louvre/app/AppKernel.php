<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            //new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            //new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
           // new JMS\AopBundle\JMSAopBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new ADesigns\CalendarBundle\ADesignsCalendarBundle(),
            //new JMS\Payment\CoreBundle\JMSPaymentCoreBundle(),
            //new JMS\Payment\PaypalBundle\JMSPaymentPaypalBundle(),
            new AppBundle\AppBundle(),
            new MDPlanningBundle\MDPlanningBundle(),
            new OC\CommandeBundle\OCCommandeBundle(),
            new OC\CoreBundle\OCCoreBundle(),
            //new Ruudk\Payment\StripeBundle\RuudkPaymentStripeBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
