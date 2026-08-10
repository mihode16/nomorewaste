<?php

class BarcodeGenerator
{
    /**
     * Génère une image SVG d'un code-barres EAN-13 avec les chiffres en dessous.
     * Compatible avec les environnements PHP sans extension GD.
     */
    public function generateEAN13(string $code, int $width = 2, int $height = 80): string
    {
        $code = preg_replace('/[^0-9]/', '', $code);
        if (strlen($code) === 12) {
            $code = $this->appendEAN13CheckDigit($code);
        }

        if (strlen($code) !== 13) {
            throw new \InvalidArgumentException('Code EAN-13 invalide');
        }

        $patterns = [
            'L' => [
                '0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101',
                '4' => '0100011', '5' => '0110001', '6' => '0101111', '7' => '0111011',
                '8' => '0110111', '9' => '0001011'
            ],
            'G' => [
                '0' => '0100111', '1' => '0110011', '2' => '0011011', '3' => '0100001',
                '4' => '0011101', '5' => '0111001', '6' => '0000101', '7' => '0010001',
                '8' => '0001001', '9' => '0010111'
            ],
            'R' => [
                '0' => '1110010', '1' => '1100110', '2' => '1101100', '3' => '1000010',
                '4' => '1011100', '5' => '1001110', '6' => '1010000', '7' => '1000100',
                '8' => '1001000', '9' => '1110100'
            ]
        ];

        $firstDigit = intval($code[0]);
        $encodingTable = [
            0 => 'LLLLLL', 1 => 'LLGLGG', 2 => 'LLGGLG', 3 => 'LLGGGL',
            4 => 'LGLLGG', 5 => 'LGGLLG', 6 => 'LGGGLL', 7 => 'LGLGLG',
            8 => 'LGLGGL', 9 => 'LGGLGL'
        ];
        $encoding = $encodingTable[$firstDigit];

        $barcode = '101';
        for ($i = 1; $i <= 6; $i++) {
            $digit = intval($code[$i]);
            $type = $encoding[$i - 1];
            $barcode .= $patterns[$type][$digit];
        }
        $barcode .= '01010';
        for ($i = 7; $i <= 12; $i++) {
            $digit = intval($code[$i]);
            $barcode .= $patterns['R'][$digit];
        }
        $barcode .= '101';

        $moduleWidth = max(1, $width);
        $quietZoneModules = 10;
        $totalModules = strlen($barcode);
        $svgWidth = ($quietZoneModules * 2 + $totalModules) * $moduleWidth;
        $svgHeight = $height + 34;

        $svg = [];
        $svg[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg[] = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $svgWidth . '" height="' . $svgHeight . '" viewBox="0 0 ' . $svgWidth . ' ' . $svgHeight . '">';
        $svg[] = '<rect x="0" y="0" width="' . $svgWidth . '" height="' . $svgHeight . '" fill="#ffffff"/>';

        $x = $quietZoneModules * $moduleWidth;
        for ($i = 0; $i < strlen($barcode); $i++) {
            if ($barcode[$i] === '1') {
                $svg[] = '<rect x="' . $x . '" y="0" width="' . $moduleWidth . '" height="' . $height . '" fill="#000000"/>';
            }
            $x += $moduleWidth;
        }

        $textY = $height + 20;
        $leftText = $code[0] . substr($code, 1, 6);
        $rightText = substr($code, 7, 6);

        $leftStartX = ($quietZoneModules + 3) * $moduleWidth;
        for ($i = 0; $i < strlen($leftText); $i++) {
            $charX = $leftStartX + ($i * 7 * $moduleWidth);
            $svg[] = '<text x="' . ($charX + ($moduleWidth * 3)) . '" y="' . $textY . '" font-family="Arial, sans-serif" font-size="14" text-anchor="middle" fill="#000000">' . htmlspecialchars($leftText[$i]) . '</text>';
        }

        $rightStartX = ($quietZoneModules + 50) * $moduleWidth;
        for ($i = 0; $i < strlen($rightText); $i++) {
            $charX = $rightStartX + ($i * 7 * $moduleWidth);
            $svg[] = '<text x="' . ($charX + ($moduleWidth * 3)) . '" y="' . $textY . '" font-family="Arial, sans-serif" font-size="14" text-anchor="middle" fill="#000000">' . htmlspecialchars($rightText[$i]) . '</text>';
        }

        $svg[] = '</svg>';

        return implode("\n", $svg);
    }

    private function appendEAN13CheckDigit(string $code): string
    {
        if (strlen($code) !== 12 || !ctype_digit($code)) {
            throw new \InvalidArgumentException('Code EAN-13 invalide');
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = intval($code[$i]);
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $code . $checkDigit;
    }

    private function validateEAN13(string $code): bool
    {
        if (strlen($code) !== 13 || !ctype_digit($code)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = intval($code[$i]);
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === intval($code[12]);
    }
}