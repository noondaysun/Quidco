<?php
declare(strict_types=1);

namespace Tests\Assignment;

use GuzzleHttp\Psr7\Response;
use Quidco\Recruitment\Assignment\Feighen;
use PHPUnit\Framework\TestCase;

class FeighenTest extends TestCase {
    /**
     * @var Feighen
     */
    protected $feighen;

    public function setUp(){
        parent::setUp();
        $this->feighen = new Feighen('AIzaSyBvzUU9Opj_zoYo7KyuwncTL-WDOcn28L4');
    }
    
    function testSearch() {
        $res = $this->feighen->search('magical unicorn farts');
        $this->assertInternalType(Response::class, $res);
    }
}