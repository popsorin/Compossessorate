<?php


namespace App\Services;


class PaginatorService
{
    private int $documentsPerPage;
    private int $totalNumberOfDocuments;
    private int $currentPage;
    private int $numberOfPages;

    /**
     * PaginatorService constructor.
     * @param int $documentsPerPage
     * @param int $totalNumberOfDocuments
     * @param int $currentPage
     */
    public function __construct(
        ?int $documentsPerPage = 50,
        ?int $totalNumberOfDocuments = 0,
        ?int $currentPage = 1
    ) {
        $this->documentsPerPage = $documentsPerPage ?? 50;
        $this->totalNumberOfDocuments = $totalNumberOfDocuments ?? 0;
        $this->currentPage = $currentPage ?? 1;
        $this->setNumberOfPages();
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getDocumentsPerPage(): int
    {
        return $this->documentsPerPage;
    }

    /**
     *  function calculates the offset for retrieving documents from the database
     */
    public function getOffset(): int
    {
        return $this->documentsPerPage * ($this->currentPage - 1);
    }

    /**
     * @return $this
     */
    private function setNumberOfPages(): self
    {
        $this->numberOfPages = ceil($this->totalNumberOfDocuments / $this->documentsPerPage);

        return $this;
    }

    /**
     * @return int
     */
    public function getPreviousPage(): int
    {
        return ($this->currentPage - 1 > 0) ? $this->currentPage - 1 : $this->currentPage;
    }

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        return ($this->currentPage + 1 <= $this->numberOfPages) ? $this->currentPage + 1 : $this->currentPage;
    }
}