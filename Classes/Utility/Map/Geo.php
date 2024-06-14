<?php
namespace JO\JoMuseo\Utility\Map;

class Geo
{
    public function __construct()
    {
        // $this->arrayUtil = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('JO\JoMuseo\Utility\Arrayfunc\Joarrayfunctions');
    }

    /**
     *    Rückgabe eines Arrays aus ShapePoints einer Route
     *
     *    @var string $api_url URL zum Webservice - aktuell Mapbox
     *    @var string $api_key API Key - aktuell Mapbox
     *    @var array $latlon_array_pois Array aus Geokordinaten der POIs in der Form:  [0] => 50.6964,11.59595 [1] => 50.6973,11.59785
     *    @return array -> Array aus Shapepoints der Route
    [0] => Array
    (
    [0] => 11.59596
    [1] => 50.696247
    )

    [1] => Array
    (
    [0] => 11.596611
    [1] => 50.696292
    )
     */
    public function getRouteShape($api_url = null, $api_key = null, $route_mode = 'pedestrian', $latlon_array_pois = [])
    {
        $shape_point_array = [];
        if (null != $api_url && null != $api_key && !empty($latlon_array_pois)) {
            $routeArray = $latlon_array_pois;
            $routeArray['options'] = [
                'fullShape' => true,
                'routeType' => $route_mode,
                'doReverseGeocode' => false,
                'unit' => 'k',
            ];
            $url = $api_url . '?key=' . $api_key . '&json=' . json_encode($routeArray, JSON_UNESCAPED_UNICODE);
            $route = json_decode(file_get_contents($url));

            if ($route) {
                // Zeiten und Distanzen
                if (!empty($route->route->legs)) {
                    $shape_point_array['routeinfo_all']['time'] = $route->route->formattedTime;
                    $shape_point_array['routeinfo_all']['distance'] = $route->route->distance;
                    foreach ($route->route->legs as $key => $value) {
                        $c = new \DateTime($value->formattedTime);
                        $shape_point_array['routeinfo_details'][$key]['time'] = $c->format('i:s');
                        $shape_point_array['routeinfo_details'][$key]['distance'] = number_format($value->distance, 1, ",", ".");
                    }
                }
                // Shapepoints der Route
                $shapePoints = $route->route->shape->shapePoints;
                for ($i = 0; $i < sizeof($shapePoints) - 1; $i = $i + 2) {
                    $shape_point_array['waypoints'][] = [$shapePoints[$i + 1], $shapePoints[$i]];
                }
            }
        }

        return $shape_point_array;
    }
}
