<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\ResponseHelper;

use Strata\Frontend\ResponseHelper\ResponseHelperAbstract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Concrete implementation of response helper using Symfony response objects
 */
class SymfonyResponseHelper extends ResponseHelperAbstract
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->setResponse($response);
    }

    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set a header to the response object
     * @param string $name
     * @param string $value
     * @param bool $replace If true, replace header, if false, append header
     * @return $this
     */
    public function setHeader(string $name, string $value, bool $replace = true): self
    {
        if ($replace) {
            $this->setResponse($this->response->withHeader($name, $value));
        } else {
            $this->setResponse($this->response->withAddedHeader($name, $value));
        }

        $this->response->headers->set($this->getTagsHeaderName(), $this->getTagsHeaderValue(), $replace);

        return $this;
    }

}
