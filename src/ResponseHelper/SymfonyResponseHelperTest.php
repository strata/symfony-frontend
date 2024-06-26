<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\ResponseHelper;

use FOS\HttpCache\ResponseTagger;
use PHPUnit\Framework\TestCase;
use Strata\Data\Http\Rest;
use Strata\Data\Query\Query;
use Strata\Data\Query\QueryManager;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseHelperTest extends TestCase
{
    public function testSecurityHeaders()
    {
        $helper = new SymfonyResponseHelper();
        $response = new Response();

        $helper->setFrameOptions()
            ->setContentTypeOptions()
            ->setReferrerPolicy()
            ->doNotCache();
        $response = $helper->apply($response);

        $this->assertSame('deny', $response->headers->get('X-Frame-Options'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('same-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame('no-cache, no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testResponseTaggerFromQueryManager()
    {
        // Create a bunch of mock responses
        $responses = array_fill(0, 4, new MockResponse('{"message": "OK"}'));

        $manager = new QueryManager();
        $rest = new Rest('https://example.com/');
        $rest->setHttpClient(new MockHttpClient($responses));
        $manager->addDataProvider('test1', $rest);

        $query = new Query();
        $query->setUri('test1')->cacheTags(['test1', 'test2']);
        $manager->add('query1', $query);
        $query = new Query();
        $query->setUri('test2')->cacheTags(['test3', 'test4']);
        $manager->add('query2', $query);
        $data = $manager->get('query1');

        $helper = new SymfonyResponseHelper();
        $response = new Response();
        $responseTagger = new ResponseTagger();

        $responseTagger = $helper->applyResponseTagsFromQuery($responseTagger, $manager);
        $this->assertNotSame('test1,test2,test3,test4', $response->headers->get('X-Cache-Tags'));

        $helper->setHeadersFromResponseTagger($responseTagger);
        $response = $helper->apply($response);
        $this->assertSame('test1,test2,test3,test4', $response->headers->get('X-Cache-Tags'));
    }
}
