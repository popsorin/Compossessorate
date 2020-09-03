<?php


namespace App\Services;


use App\Entity\Document;
use ReflectionClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RequestParameterBag extends ParameterBag
{
    /**
     * Extracts the query parameters from the request and returns a parameter bag
     * that contains them.
     *
     * @param Request $request
     * @return RequestParameterBag
     */
    public function createFromRequest(Request $request): self
    {
        $queryStringArray = $request->query->all();

        foreach ($queryStringArray as $key => $queryString) {
                $this->set($key, $queryString);
        }
        return $this;
    }

    public function getArray(string $key, $default = null)
    {
        return \array_key_exists($key, $this->parameters) ? [$key => $this->parameters[$key]] : $default;
    }
}