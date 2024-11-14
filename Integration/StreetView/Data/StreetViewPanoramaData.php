<?php declare(strict_types=1);

namespace Integration\StreetView\Data;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Response;

final readonly class StreetViewPanoramaData {
  public function __construct(
    public float           $lat,
    public float           $lng,
    public string          $panoId,
    public string          $copyright,
    public CarbonImmutable $date,
    public string          $status, // Should always be 'OK', as this class is only returned when a panorama is found.
    public bool            $from_id = true,
  ) {
  }

  public static function fromResponse(Response $response, bool $from_id = true): self|null {
    if ($response->status() !== 200) {
      return null;
    }
    $data = $response->json();
    if ($data['status'] !== 'OK') {
      return null;
    }
    return new StreetViewPanoramaData(
      lat: $data['location']['lat'],
      lng: $data['location']['lng'],
      panoId: $data['pano_id'],
      copyright: $data['copyright'],
      date: CarbonImmutable::parse($data['date'] . '-01'),
      status: $data['status'],
      from_id: $from_id,
    );
  }
}
