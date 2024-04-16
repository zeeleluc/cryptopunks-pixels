<?php

class Punk
{

    private ?array $properties = null;

    private ?array $accessories = null;

    private ?string $gender = null;

    private ?string $type = null;

    private ?string $skin = null;

    /**
     * @throws Exception
     */
    public function __construct(readonly ?int $id = null)
    {
        if ($this->id) {
            $this->properties = $this->getProperties();

            $this->accessories = $this->properties['accessories'];
            $this->gender = $this->properties['gender'];
            $this->type = $this->properties['type'];
            if (in_array($this->type, ['Zombie', 'Alien', 'Ape'])) {
                $this->skin = $this->type;
            } else {
                $this->skin = $this->properties['skin'];
            }
        }
    }

    /**
     * This is what I used for analyzing the CryptoPunks properties.
     * This method simply grabs an original CryptoPunk and prints an overlay that shows x-y coordinates per pixel.
     * I did this for each attribute and created separate CSV files for each attribute.
     *
     * @return void
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    public function rasterize(): void
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

    /**
     * @throws Exception
     */
    public function getPixels(): array
    {
        if (!$this->id) {
            throw new Exception('Need an ID');
        }

        return [
            'Type' => $this->getTypePixels(),
            'Skin' => $this->getSkinPixels(),
            'Eyes, Nose & Mouth' => $this->getEyesNoseMouthPixels(),
            'Accessories' => $this->getAccessoriesPixels(),
        ];
    }

    public function getAccessoriesPixels(): array
    {
        if (!$this->id) {
            throw new Exception('Need an ID');
        }

        $pixels = [];
        foreach ($this->accessories as $accessory) {
            $basePath = 'data/properties/accessory';
            $csvFile = $basePath . '/' . $this->gender . '/' . $accessory . '.csv';

            $pixels[$accessory] = determine_pixels(csv_to_array($csvFile));
        }

        return $pixels;
    }

    public function getEyesNoseMouthPixels(): array
    {
        if (!$this->id) {
            throw new Exception('Need an ID');
        }

        $basePath = 'data/properties/eyes-nose-mouth/Human';
        $csvFile = $basePath . '/' . $this->gender . '/' . $this->skin . '.csv';

        return determine_pixels(csv_to_array($csvFile));
    }

    public function getSkinPixels(): array
    {
        if (!$this->id) {
            throw new Exception('Need an ID');
        }

        $basePath = 'data/properties/skin/Human';
        $csvFile = $basePath . '/' . $this->gender . '/' . $this->skin . '.csv';

        return determine_pixels(csv_to_array($csvFile));
    }

    public function getTypePixels(): array
    {
        if (!$this->id) {
            throw new Exception('Need an ID');
        }

        $basePath = 'data/properties/type/Human';
        $csvFile = $basePath . '/' . $this->gender . '/' . $this->skin . '.csv';

        return determine_pixels(csv_to_array($csvFile));
    }

    public function getProperties(): array
    {
        if (!$this->id) {
            throw new Exception('Need an ID');
        }

        if ($this->properties) {
            return $this->properties;
        }

        $csvFile = file(ROOT . '/data/properties.csv');
        $data = [];
        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
        }

        $properties = [
            'type' => trim($data[$this->id][1]),
            'gender' => trim($data[$this->id][2]),
            'skin' => trim($data[$this->id][3]),
            'attributes_count' => trim($data[$this->id][4]) . ' Attributes',
            'accessories' => [],
        ];

        $propertiesString = $data[$this->id][5];
        if ($propertiesString) {
            foreach (explode('/', $propertiesString) as $property) {
                $properties['accessories'][] = trim($property);
            }
        }

        return $properties;
    }
}
