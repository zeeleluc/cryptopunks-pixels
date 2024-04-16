<?php

class Punk
{

    public function __construct(readonly ?int $id = null)
    {

    }

    public function rasterize()
    {
        $white = new ImagickPixel('#ffffff');

        $draw = new ImagickDraw();

        $draw->setStrokeColor($white);
        $draw->setFillColor($white);

        $draw->setStrokeWidth(2);
        $draw->setFontSize(35);

        foreach (range(1, 24) as $row) {
            $draw->line(1, $row * 100, 2400, $row * 100);
            $draw->line( $row * 100, 1,  $row * 100, 2400);
        }
        $imagick = new \Imagick('data/originals/' . $this->id . '.png');
        $imagick->scaleImage(2400, 2400);
        $imagick->setImageFormat("png");

        foreach (range(1,24) as $row) {
            $rowPos = ($row * 100) - 90;
            foreach (range(1,24) as $col) {
                $colPos = ($col * 100) - 40;
                $imagick->annotateimage($draw, $rowPos, $colPos, 0, $col . '-' . $row);
            }
        }

        $imagick->drawImage($draw);
        $imagick->writeImage('generated/rasterized/' . $this->id . '.png');
    }
}
