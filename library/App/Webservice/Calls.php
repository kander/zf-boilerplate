<?php
namespace App\Webservice;

use Doctrine\ORM\EntityManager;

class Calls
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns a random quote using the RandomQuote Service
     * @param \App_Webservice_Types_Request_RandomQuoteRequest $request
     * @return  \App_Webservice_Types_Response_RandomQuoteResponse $response
     */
    public function randomQuote($request)
    {
        $dic = \Zend_Registry::get('pimple');
        /** @var \App\Service\RandomQuote $srv */
        $srv = $dic['randomQuote'];
        $quote = $srv->getQuote();
        $response = new \App_Webservice_Types_Response_RandomQuoteResponse();
        $response->quote->wording = $quote[0];
        $response->quote->author = $quote[1];
        return $response;
    }

    /**
     * Returns a quote from the Database using an ID given
     * @param \App_Webservice_Types_Request_QuoteRequest $request
     * @return  \App_Webservice_Types_Response_QuoteResponse $response
     */
    public function quote($request)
    {   
        $em = $this->entityManager;
        $quote = $em->getRepository('\App\Entity\Quote')->find($request->id);
        $response = new \App_Webservice_Types_Response_QuoteResponse();

        if (!is_null($quote)) {
            $response->quote->wording = $quote->getWording();
            $response->quote->author = $quote->getAuthor();
            return $response;
        } else {
            return new \SoapFault(
                (string) "404", "Quote with ID " .
                ($request->id) . " not found in DB."
            );
        }
    }
}
