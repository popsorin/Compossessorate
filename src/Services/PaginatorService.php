<?php


namespace App\Services;


class PaginatorService
{
    private int $documentsPerPage;
    private int $totalNumberOfDocuments;
    private int $currentPage;

    /**
     * PaginatorService constructor.
     * @param int $documentsPerPage
     * @param int $totalNumberOfDocuments
     * @param int $currentPage
     */
    public function __construct(int $documentsPerPage = 50, int $totalNumberOfDocuments = 0, $currentPage = 1)
    {
        $this->documentsPerPage = $documentsPerPage;
        $this->totalNumberOfDocuments = $totalNumberOfDocuments;
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     *  function calculates the offset for retrieving documents from the database
     */
    public function getOffset(): int
    {
        return $this->documentsPerPage * ($this->currentPage - 1);
    }

    /**
     *  function calculates the limit for retrieving documents from the database
     */
    public function getLimit(): int
    {
        return $this->documentsPerPage * $this->currentPage - 1;
    }
}