<?php

declare(strict_types=1);

namespace Strata\SymfonyBundle\ResponseHelper;

use Strata\Data\Helper\UnionTypes;
use Strata\Frontend\ResponseHelper\HeaderValue;
use Strata\Frontend\ResponseHelper\ResponseHelperAbstract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Concrete implementation of response helper using Symfony response objects
 */
class SymfonyResponseHelper extends ResponseHelperAbstract
{
    /**
     * Apply headers to response object and return response
     * @param Response $response
     * @return Response
     */
    public function apply($response): Response
    {
        UnionTypes::assert('response', $response, Response::class);

        foreach ($this->getHeaders() as $name => $values) {
            /** @var HeaderValue $header */
            foreach ($values as $header) {
                $response->headers->set($name, $header->getValue(), $header->isReplace());
            }
        }
        return $response;
    }
}
