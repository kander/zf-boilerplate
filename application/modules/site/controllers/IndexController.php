<?php

class Site_IndexController extends Zend_Controller_Action
{

    /**
     * @var Doctrine\ORM\EntityManager
     * @InjectService entityManager
     */
    protected $entityManager = null;

    /**
     * @var \App\Service\RandomQuote
     * @InjectService randomQuote
     */
    protected $_randomQuote = null;

    public function searchAction()
    {
        if ($query = $this->getRequest()->getParam('query')) {
            $resultSet = array();

            try {
                $client = Zend_Registry::get('es');
                $index = $client->getIndex('quotes');
                $type = $index->getType('quote');
                $resultSet = $type->search($query);
            } catch (Exception $e) {
                $this->_redirect('/');
            }

            $data = array();

            foreach ($resultSet as $result) {
                $hit = $result->getHit();
                $quote = new \App\Entity\Quote();
                $quote->setAuthor($hit['_source']['author']);
                $quote->setWording($hit['_source']['wording']);
                $data[] = $quote;
            }

            $this->view->search = true;
            $this->view->data = $data;
            $this->_helper->viewRenderer('index');
        }
        else
            $this->_redirect('/');

    }

    public function addRandomAction()
    {
        $tempQuote = $this->_randomQuote->getQuote();
        $newQuote = new \App\Entity\Quote();
        $newQuote->setWording($tempQuote[0]);
        $newQuote->setAuthor($tempQuote[1]);

        try
        {
            $this->entityManager->persist($newQuote);
            $this->entityManager->flush();
            $this->indexQuote($newQuote);
        }
        catch (Exception $e)
        {
            $this->_redirect('/');
        }

        $this->_redirect('/');
    }

    public function addCustomAction()
    {
        if ($this->_request->isPost()) {
            $addQuoteForm = new \App\Form\AddQuote();

            if ($addQuoteForm->isValid($this->_request->getPost())) {

                $values = $addQuoteForm->getValues();
                $newQuote = new \App\Entity\Quote();
                $newQuote->setWording($values['quote']);
                $newQuote->setAuthor($values['name']);

                try {
                    $this->entityManager->persist($newQuote);
                    $this->entityManager->flush();
                    $this->indexQuote($newQuote);
                }
                catch (Exception $e) {
                    $this->_redirect('/');
                }
            } else {
                $addQuoteForm->buildBootstrapErrorDecorators();
            }

            $data = $this->entityManager->getRepository("\App\Entity\Quote")
                    ->findThemAll();

            $this->view->data = $data;
            $this->_helper->viewRenderer('index');
        } else {
            $this->_redirect('/');
        }
    }
        
    public function indexAction()
    {
        $addQuoteForm = new \App\Form\AddQuote();
        $this->view->assign('form', $addQuoteForm);
        $this->checkSearchindex();

        try {
            $entityRepository = $this->entityManager->getRepository('\App\Entity\Quote');
            $data = $entityRepository->findThemAll();
            $this->view->assign('data', $data);
        }
        catch (Exception $e) {
            $this->view->databaseError = true;
        }
    }

    private function checkSearchindex()
    {
        try {
            $client = Zend_Registry::get('es');
            $index = $client->getIndex('quotes');
            $type = $index->getType('quote');
            $resultSet = $type->search('*');
            //var_dump($resultSet);exit;
        } catch (Exception $e) {
            $this->view->searchindexError = true;
        }
    }

    private function indexQuote(\App\Entity\Quote $quote)
    {
       $client = Zend_Registry::get('es');
       $index = $client->getIndex('quotes');
       //$index->create(array(), true);
       $type = $index->getType('quote');

       $doc = new Elastica_Document(
           $quote->getId(), array('id' => $quote->getId(),
           'wording' => $quote->getWording(),
           'author' => $quote->getAuthor())
       );
       // var_dump($doc);exit;
       $type->addDocument($doc);
       $index->refresh();
    }

    public function headerAction()
    {
        $container = new Zend_Navigation(
            array(
                array(
                    'action'     => 'index',
                    'controller' => 'index',
                    'module'     => 'site',
                    'label'      => 'Home'
                ),
                array(
                    'uri'        => 'http://zf-boilerplate.com/documentation/',
                    'label'      => 'Documentation'
                )
            )
        );

        $this->view->navigation($container);
    }

    /**
     * @todo Somehow the original ZFBP project had a 'cache' key set in the Registry.
     * This was not done in the Bootstrap... and now it is missing. Where did the cache come from?
     */
    public function footerAction()
    {
        /*$cache = Zend_Registry::get('cache');

        if ($cache->contains('timestamp')) {
            $timestamp = $cache->fetch('timestamp');
            $this->view->cachedTimestamp = true;
        } else {
            $timestamp = time();
            $cache->save('timestamp', $timestamp);
        }
        */
        $this->view->assign('cachedTimestamp', false);
        $this->view->assign('timestamp', new Zend_Date());
    }
}