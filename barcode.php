<?php
class Barcode {

    public function generate($code, $type = 'C128', $widthFactor = 2, $totalHeight = 50) {
        $image = imagecreate(strlen($code) * $widthFactor, $totalHeight);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $white);

        $x = 0;
        foreach (str_split($code) as $char) {
            $ascii = ord($char);
            for ($i = 0; $i < 7; $i++) {
                $value = $ascii & (1 << (6 - $i)) ? $black : $white;
                for ($j = 0; $j < $widthFactor; $j++) {
                    imageline($image, $x, 0, $x, $totalHeight, $value);
                    $x++;
                }
            }
        }

        ob_start();
        imagepng($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);

        return $image_data;
    }

    public function render($code, $type = 'C128', $widthFactor = 2, $totalHeight = 50) {
        header('Content-type: image/png');
        echo $this->generate($code, $type, $widthFactor, $totalHeight);
    }
}
?>
