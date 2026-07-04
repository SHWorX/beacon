<?php
/*
 * Project:     beacon
 * File:        QrCodeGenerator.php
 * Date:        2026-07-03
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Generators;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeGenerator
{
    /**
     * Generates a QR code
     *
     * @param string $content
     *
     * @return string
     * @throws ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function generate(string $content): string
    {
        $result = new Builder(
            writer: new SvgWriter(),
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 150,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return $result->build()->getString();
    }
}
