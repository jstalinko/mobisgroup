<?php

namespace App\Services;


class NetshortService
{
    use ServiceTrait;

    public function __construct()
    {
        $this->bootService();
    }

    public function getTheater($lang = 'in')
    {
        return $this->http('/netshort/theaters?lang='.$lang , 'GET');
    }
    public function getRecommend($lang = 'in',$page=1)
    {
        return $this->http('/netshort/recommend?page='.$page.'&lang='.$lang,'GET');
    }
    public function getCategory($lang = 'in',$page= 1)
    {
        return $this->http('/netshort/categories?page='.$page.'&lang='.$lang,'GET');
    }
    public function getTheaterDetail($lang='in',$bookId)
    {
        return $this->http('/netshort/detail/'.$bookId.'?lang='.$lang);
    }
    public function getChapterDetail($lang='in',$bookId)
    {
        return $this->http('/netshort/detail/chapter/'.$bookId.'?lang='.$lang);
    }
    public function getStream($lang='in',$bookId)
    {
        return $this->http('/netshort/stream/'.$bookId.'?lang='.$lang);
    }
    public function getSearch($lang='in',$query,$page=1)
    {
        return $this->http('/netshort/search?query='.$query.'&page='.$page.'&lang='.$lang);
    }
}
