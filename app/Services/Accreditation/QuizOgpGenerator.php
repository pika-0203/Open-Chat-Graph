<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use Shadow\File\Image\ImageStore;

class QuizOgpGenerator
{
    private string $existingImagePath = __DIR__ . '/../../../storage/font/text-ogp-quiz.png';
    private string $fontFile = __DIR__ . '/../../../storage/font/mgenplus-1p-medium.ttf';
    private string $destPath = PUBLIC_DIR . '/quiz-img';

    function __construct(
        private ImageStore $imageStore
    ) {
    }

    function generateTextOgp(
        string $text,
        string $fileName,
        int $fontSize = 45,
        int $characterSpacing = 9,
        int $top = 90,
        int $left = 120,
        int $right = 1090,
        int $bottom = 490,
    ): string {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = str_replace("\n", " ", $text);

        // PNG画像を読み込む
        $image = imagecreatefrompng($this->existingImagePath);

        // 描画範囲を赤で可視化
        /* $red = imagecolorallocate($image, 255, 0, 0);
        imagerectangle($image, $left, $top, $right, $bottom, $red); */

        $black = imagecolorallocate($image, 0, 0, 0);

        $this->imageTtfTextArea(
            $image,
            $fontSize,
            $left,
            $top,
            $right,
            $bottom,
            $black,
            $this->fontFile,
            $text,
            $characterSpacing,
            10,
        );

        if(!$fileName) {
            // 画像を出力（ブラウザに表示する場合）
            header('Content-Type: image/png');
            imagepng($image);
            return '';
        }

        return $this->imageStore->storeImageFromGdImage(
            $image,
            $this->destPath,
            $fileName,
        );
    }

    /**
     * Checks if the text fits within the specified area using the given font size.
     *
     * @param int $fontSize Font size
     * @param int $left Left boundary
     * @param int $top Top boundary
     * @param int $right Right boundary
     * @param int $bottom Bottom boundary
     * @param string $fontFile Path to font file
     * @param string $text Text to be drawn
     * @param int $characterSpacing Character spacing
     * @param int $lineSpacing Line spacing
     * @param int $lineHeight Line height (0 adjusts to text height)
     * @return bool True if the text fits, false otherwise
     */
    private function doesTextFit(
        int $fontSize,
        int $left,
        int $top,
        int $right,
        int $bottom,
        string $fontFile,
        string $text,
        int $characterSpacing = 0,
        int $lineSpacing = 0,
        int $lineHeight = 0
    ): bool {
        $lineSpacing = (int)floor($fontSize / 1.5);

        $len = mb_strlen($text);
        if ($len === 0) return true;

        $bBox = imagettfbbox($fontSize, 0, $fontFile, 'A');
        $defaultHeight = $bBox[1] - $bBox[7];

        $currentTop = $top;
        $count = 0;
        while ($count < $len) {
            $outputTexts = array();
            $textTop = PHP_INT_MAX;
            $textHeight = $defaultHeight;
            $x = $left;

            for (; $count < $len; $count++) {
                $c = mb_substr($text, $count, 1);
                if ($c === "\n") {
                    $count++;
                    break;
                }

                $bBox = imagettfbbox($fontSize, 0, $fontFile, $c);
                $w = $bBox[4] - $bBox[6];
                $wX = $x + $w;
                if ($wX > $right || $wX < $left) break;

                $textHeight = max($textHeight, $bBox[1] - $bBox[7]);
                if ($currentTop + $textHeight > $bottom) return false;

                $textTop = min($textTop, $bBox[7]);
                $outputTexts[] = [$c, $x - $bBox[0]];
                $x = $wX + $characterSpacing;
                if ($x > $right || $x < $left) {
                    $count++;
                    break;
                }
            }

            $currentTop += ($lineHeight === 0 ? $textHeight : $lineHeight) + $lineSpacing;
            if ($currentTop > $bottom || $currentTop < $top) return false;
        }
        return true;
    }

    /**
     * Draws text within a specified area using GD.
     *
     * @param \GdImage $image GdImage object
     * @param int $fontSize Font size
     * @param int $left Left boundary
     * @param int $top Top boundary
     * @param int $right Right boundary
     * @param int $bottom Bottom boundary
     * @param int $color Text color
     * @param string $fontFile Path to font file
     * @param string $text Text to be drawn
     * @param int $characterSpacing Character spacing
     * @param int $lineSpacing Line spacing
     * @param int $lineHeight Line height (0 adjusts to text height)
     */
    private function imageTtfTextArea(
        \GdImage $image,
        int $fontSize,
        int $left,
        int $top,
        int $right,
        int $bottom,
        int $color,
        string $fontFile,
        string $text,
        int $characterSpacing = 0,
        int $lineSpacing = 0,
        int $lineHeight = 0
    ): void {
        $len = mb_strlen($text);
        if ($len === 0) return;

        // Calculate the maximum font size that fits the text within the specified area
        while (!$this->doesTextFit($fontSize, $left, $top, $right, $bottom, $fontFile, $text, $characterSpacing, $lineSpacing, $lineHeight)) {
            $fontSize--;
            if ($fontSize <= 0) return; // Exit if font size becomes zero or negative
        }

        $lineSpacing = (int)floor($fontSize / 1.5);

        // 行の高さの既定値算出
        $bBox = imagettfbbox($fontSize, 0, $fontFile, 'A');
        $defaultHeight = $bBox[1] - $bBox[7];

        $currentTop = $top;
        $count = 0;
        while ($count < $len) {
            $outputTexts = array(); // 行データ [ 文字 , x座標 ]の配列
            $textTop = PHP_INT_MAX; // 文字列の上端座標
            $textHeight = $defaultHeight; // 文字列の高さ
            $x = $left; // 現在の文字位置

            // 1行分の文字列抽出
            for (; $count < $len; $count++) {
                $c = mb_substr($text, $count, 1);
                if ($c === "\n") {
                    $count++;
                    break;
                } // 改行→行終了

                $bBox = imagettfbbox($fontSize, 0, $fontFile, $c);
                $w = $bBox[4] - $bBox[6]; // 文字幅算出
                $wX = $x + $w;
                if ($wX > $right || $wX < $left) break; // 右端または左端を超えた

                $textHeight = max($textHeight, $bBox[1] - $bBox[7]);
                if ($currentTop + $textHeight > $bottom) return; // 下端を超えた

                $textTop = min($textTop, $bBox[7]);
                $outputTexts[] = [$c, $x - $bBox[0]]; // [ 文字 , x座標 ]
                $x = $wX + $characterSpacing; // 次の文字位置に移動
                if ($x > $right || $x < $left) {
                    $count++;
                    break;
                } // 右端または左端を超えた
            }

            $baseY = $currentTop - $textTop; // y座標(ベースライン)算出
            // 1行を描画
            foreach ($outputTexts as $v) {
                ImageTTFText(
                    $image,
                    $fontSize,
                    0,
                    $v[1],
                    $baseY,
                    $color,
                    $fontFile,
                    $v[0]
                );
            }
            // 次の行に移動
            $currentTop += ($lineHeight === 0 ? $textHeight : $lineHeight) + $lineSpacing;
            if ($currentTop > $bottom || $currentTop < $top) return; // 下端または上端を超えた
        }
    }
}
