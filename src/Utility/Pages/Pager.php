<?php

namespace App\Utility\Pages;

class Pager
{
    public function getTotalNumberOfPages(int $totalNumberOfRecords, int $numberOfRecordsPerPage): int
    {

        return ceil($totalNumberOfRecords/$numberOfRecordsPerPage);
    }

    /*Создать для каждой страницы объект Page, заполнить его свойства (номер страницы, диапазон записей) */

    public function getPages(int $totalNumberOfPages, int $numberOfRecordsPerPage, int $totalNumberOfRecords): array
    {
        $pages = [];

        for ($i=1; $i<=$totalNumberOfPages; $i++) {
            
            $pageNumber = $i;

            $recordRange = $this->getRangeOfRecords($i, $totalNumberOfPages, $numberOfRecordsPerPage, $totalNumberOfRecords);

            $pages[] = new Page($pageNumber, $recordRange);
        }

        return $pages;
    }


    private function getRangeOfRecords(int $pageNumber, int $totalNumberOfPages, int $numberOfRecordsPerPage, int $totalNumberOfRecords): array
    {
        if ($pageNumber < $totalNumberOfPages) {
            /*
            50 * 2 = 100
             */
            $secondNumber = $numberOfRecordsPerPage * $pageNumber;
            /*
            (100 - 50)+1 = 51
             */
            $firstNumber = ($secondNumber - $numberOfRecordsPerPage) + 1;

        } elseif ($pageNumber === $totalNumberOfPages) {
            $pastRange = $this->getRangeOfRecords($pageNumber-1, $totalNumberOfPages, $numberOfRecordsPerPage, $totalNumberOfRecords);
            $firstNumber = $pastRange[1]+1;
            $secondNumber = $totalNumberOfRecords;
        }

        return [
            0 => $firstNumber,
            1 => $secondNumber
        ];
    }
}