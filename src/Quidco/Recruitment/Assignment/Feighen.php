<?php
declare(strict_types=1);

namespace Quidco\Recruitment\Assignment;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client;
use Quidco\Recruitment\Interview as QuidcoInterview;
use GuzzleHttp\Psr7\Response;

/**
 *
 * @author Feighen Oosterbroek <feighen@gmail.com>
 *        
 */
class Feighen implements QuidcoInterview
{
    /**
     * Google Search API key
     *
     * @var string
     */
    protected $googleApiKey;
    
    /**
     * Class constructor
     *
     * @param string $key
     * @return void
     */
    public function __construct(string $key)
    {
        $this->setGoogleApiKey($key);
    }
    
    /**
     * 
     * @return string
     */
    public function getGoogleApiKey(): string
    {
        return $this->googleApiKey;
    }
    
    /**
     * 
     * @param string $key
     * @return void
     */
    public function setGoogleApiKey(string $key)
    {
        $this->googleApiKey = $key;
    }
    
    /**
     * Search google for a given term
     *
     * @param string $term
     *  What are we searching for
     * @param int $num
     *  Number of results per page - defaults to ten (range of 1 through 10)
     * @param int $start
     *  Start on which page - defaults to one (max value of 100)
     * @return Response
     * @throws BadResponseException
     */
    public function search(string $term, int $num = 10, int $start = 1): Response
    {
        //: Tests
        if (!in_array($num, range(1, 10))) {$num = 10;}
        if ($start > 100) {$start = 1;}
        
        $baseurl = 'https://www.googleapis.com/customsearch/v1?q=' . $term . '&key=' . $this->getGoogleApiKey();
        $baseurl .= '&searchType=image&fileType=gif&tbs=itp:animated&num=' . $num . '&start=' . $start;
        $baseurl .= '&cx=018386883167948996847:8oa_p_csklo';
        
        $client = new Client();
        $result = $client->request('GET', $baseurl, [
            'Accept'       => 'application/json',
        ]);
        
        if ($result->getStatusCode() >= 400) {
            throw new BadResponseException('Request resulted in an unacceptable response.', $result);
        }
        $data = [
            'status' => $result->getStatusCode(),
            'meta'   => [
                'pagination' => [
                    'current page' => $num,
                    'next page' => ($num <=10 ? $num+1 : 10),
                    'previous page' => ($num>1 ? $num-1 : 0),
                ]
            ],
        ];
        
        foreach (json_decode($result->getBody()->read(15000), true) as $key => $values) {
            if ($key === 'items') {
                $data['items'] = $values;
            }
        }
        
        return new Response(
            $result->getStatusCode(),
            [],
            json_encode($data)
        );
    }
}
