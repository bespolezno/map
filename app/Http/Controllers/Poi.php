<?php
/*
for reference only
*/
class Poi
{
    private $latitude;

    private $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude = deg2rad($latitude);
        $this->longitude = deg2rad($longitude);
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function distanceTo(Poi $other)
    {
        $earthRadius = 6371000;

        $diffLatitude = $other->getLatitude() - $this->latitude;
        $diffLongitude = $other->getLongitude() - $this->longitude;

        $a = sin($diffLatitude / 2) * sin($diffLatitude / 2) +
            cos($other->getLatitude()) * cos($this->latitude) *
            sin($diffLongitude / 2) * sin($diffLongitude / 2);
        $c = 2 * asin(sqrt($a));

        return $c * $earthRadius;
    }
}

class PoiFactory
{
	private $start;

	private $end;

	private $startPoint;

	private $width;

	private $height;

	public function __construct($start = ['latitude' => '24.59895', 'longitude' => '54.2522'], $end = ['latitude' => '24.35429', 'longitude' => 54.66419], $width = 1280, $height = 800)
	{
		$this->start = $start;
		$this->end = $end;
		$this->width = $width;
		$this->height = $height;
		$this->mapStartPoint();
	}

	private function calc($source, $target)
	{
		$_a = new Poi($source['latitude'], $target['longitude']);
		$a = new Poi($target['latitude'], $target['longitude']);

		$y = $_a->distanceTo($a);

		$_b = new Poi($source['latitude'], $source['longitude']);
		$b = new Poi($source['latitude'], $target['longitude']);

		$x = $_b->distanceTo($b);

		return [
			'x' => $x,
			'y' => $y
		];
	}

	private function mapStartPoint()
	{
		$calc = $this->calc($this->start, $this->end);

		$this->startPoint = [
			'x' => $calc['x'] / $this->width,
			'y' => $calc['y'] / $this->height
		];
	}

	public function calculate($target)
	{
		$calc = $this->calc($this->start, $target);

		return [
			'x' => floor($calc['x'] / $this->startPoint['x']),
			'y' => floor($calc['y'] / $this->startPoint['y'])
		];
	}
}