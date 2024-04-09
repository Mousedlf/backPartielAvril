<?php

namespace App\Service;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class QrCodeGenerator
{
    public function createQrCode($id)
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create($id)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(120)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $qrCodes = $writer->write(
            $qrCode
        )->getDataUri();


        return $qrCodes;
    }

}