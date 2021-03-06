<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initConfig()
    {
        Zend_Registry::set('config', $this->getOptions());
    }
    
    public function _initModuleLayout()
    {
        $front = Zend_Controller_Front::getInstance();

        $front->registerPlugin(
            new Boilerplate_Controller_Plugin_ModuleLayout()
        );
        
        $front->setParam('prefixDefaultModule', true);
        $eh = new Zend_Controller_Plugin_ErrorHandler();
        $front = Zend_Controller_Front::getInstance()->registerPlugin($eh);
    }

    public function _initDependencyInjectionContainer()
    {
        $diContainer = new Pimple();

        $this->bootstrap('doctrine');
        /** @var Bisna\Doctrine\Container $doctrineContainer */
        $doctrineContainer = $this->getResource('doctrine');
        $entityManager = $doctrineContainer->getEntityManager();


        $diContainer['entityManager'] = $entityManager;
        Zend_Registry::set('em', $entityManager);

        $diContainer['randomizer'] = $diContainer->share(function($container) {
            return new App\Service\Randomizer();
        });

        $diContainer['randomQuote'] = function($container) {
            return new App\Service\RandomQuote($container['randomizer']);
        };

        \Zend_Registry::set('pimple', $diContainer);
    }

    public function _initLocale()
    {
        $config = $this->getOptions();
        
        try{
            $locale = new Zend_Locale(Zend_Locale::BROWSER);
        } catch (Zend_Locale_Exception $e) {
            $locale = new Zend_Locale($config['resources']['locale']['default']);
        }

        Zend_Registry::set('Zend_Locale', $locale);

        $translator = new Zend_Translate(
            array(
                'adapter' => 'Csv',
                'content' => APPLICATION_PATH . '/../data/lang/',
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
                'delimiter' => ',',
                'disableNotices' => true,
            )
        );

        if (!$translator->isAvailable($locale->getLanguage()))
            $translator->setLocale($config['resources']['locale']['default']);

        Zend_Registry::set('Zend_Translate', $translator);
        Zend_Form::setDefaultTranslator($translator);
    }

    public function _initElasticSearch()
    {
        $es = new Elastica_Client();
        Zend_Registry::set('es', $es);
    }

}