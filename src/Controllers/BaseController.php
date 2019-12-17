<?php

namespace Wishi\Controllers;

use Dotenv\Dotenv;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Wishi\Exceptions\RequestException;

class BaseController
{
    /**
     * The GuzzleClient instance to-be.
     */
    protected $client;

    const API_URL = 'http://where.yahooapis.com/v1/';

    const API_ATT = '?format=json&appid=';

    /**
     * Create instances of Guzzle and DotEnv
     * Load Environment.
     */
    public function __construct(GuzzleClient $client = null, Dotenv $dotenv = null)
    {
        $this->client = $client == null ? new GuzzleClient() : $client;

        $dotenv = $dotenv == null ? new Dotenv($_SERVER['DOCUMENT_ROOT']) : $dotenv;

        $dotenv->load();
    }

    /**
     * Make a collection of model instances.
     *
     * @param  An array containing information of each location.
     * @param  The name of the model class.
     * 
     * @return Illuminate\Support\Collection
     */
    protected function makeCollection($places, $model)
    {
        $class = 'Wishi\Model\\'.ucwords($model);

        return collect(array_map(function ($place) use ($class) {
            return new $class($place);
        }, $places));
    }

    /**
     * Make a request with Guzzle Client.
     *
     * @return Guzzle response
     */
    protected function getter($string)
    {
        try {
            return $this->client->request('GET',
                self::API_URL.$string.self::API_ATT.getenv('YAHOO_CLIENT_ID')
            );
        } catch (Exception $e) {
            throw RequestException::create($e->getCode());
        }
    }

    /**
     * Respond with Json response.
     *
     * @return json
     */
    protected function respondJSON($res)
    {
        return json_decode($res->getBody(), true);
    }

    /**
     * Make request and return data
     * 
     * @param  data
     * @param  $value
     * 
     * @return json      
     */
    public function data($data, $value)
    {
        $data = $this->respondJSON($data);
        $places = $data['places']['place'];

        return $this->makeCollection($places, $value);
    }
}
