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
     * @return array
     */
    public function createFromRequest(Request $request)
    {
        $reflection = new ReflectionClass(Document::class);
        $properties = $reflection->getProperties();
        $queryStringArray = $request->query->all();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $queryStringValue = $request->query->get($propertyName);
            if(array_key_exists($propertyName, $queryStringArray)) {
                $this->parameters[$propertyName] = $queryStringValue;
            }
        }

        return $this->parameters;
    }
}