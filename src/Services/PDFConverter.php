<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFConverter
{
    /**
     * Saves a html page with tables as a pdf image
     *
     * @param $html
     * @param int $tableNumber
     * @param string $projectDirectory
     */
    public function convert($html, int $tableNumber, string $projectDirectory)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $projectDirectory . '/SavedDocuments';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  sprintf('%s/%s-%s.pdf',$publicDirectory, "Tabel Nominal", $tableNumber);

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
    }
}