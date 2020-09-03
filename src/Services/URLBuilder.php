<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\Request;

class URLBuilder
{
    /**
     *  This method is used to get the URL
     *  I'm using this method to build the URL for the number of documents per page
     * links and for the order by fullname links.
     *  The reason for the 'exclude' parameter is that when I'm building the URL for
     * the let's say document's per page link, I want to exclude the
     * query parameter that tells me how many documents will be on the page because
     * that is already hard-coded in the twig template.
     *
     * @param RequestParameterBag $parameterBag
     * @param string $exclude
     * @return string
     */
    public function get(RequestParameterBag $parameterBag, string $exclude): string
    {
        $url = "";
        $parameters = $parameterBag->all();
        foreach ($parameters as $key => $queryString) {
            if($key === $exclude) {
                continue;
            }
            $url = sprintf("%s&%s=%s", $url, $key, $queryString);
        }

        return $url;
    }
}