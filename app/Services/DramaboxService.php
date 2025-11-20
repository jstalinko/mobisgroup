<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Interfaces\MovieServiceInterface;
use GuzzleHttp\Exception\RequestException;

class DramaboxService
{
    use ServiceTrait;

    public function __construct()
    {
        $this->bootService();
    }

    public function getTheater($lang = 'in')
    {
        return $this->http('/dramabox/theaters?lang='.$lang , 'GET');
    }
    public function getRecommend($lang = 'in',$page=1)
    {
        return $this->http('/dramabox/recommend?page='.$page.'&lang='.$lang,'GET');
    }
    public function getCategory($lang = 'in',$page= 1)
    {
        return $this->http('/dramabox/categories?page='.$page.'&lang='.$lang,'GET');
    }
    public function getTheaterDetail($lang='in',$bookId)
    {
        return $this->http('/dramabox/detail/'.$bookId.'?lang='.$lang);
    }
    public function getChapterDetail($lang='in',$bookId)
    {
        return $this->http('/dramabox/detail/chapter/'.$bookId.'?lang='.$lang);
    }
    public function getStream($lang='in',$bookId)
    {
        return $this->http('/dramabox/stream/'.$bookId.'?lang='.$lang);
    }
    public function getSearch($lang='in',$query,$page=1)
    {
        return $this->http('/dramabox/search?query='.$query.'&page='.$page.'&lang='.$lang);
    }
}
