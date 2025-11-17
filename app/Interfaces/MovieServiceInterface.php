<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface MovieServiceInterface
{
    /**
     * Get theater information
     *
     * @return mixed
     */
    public function getTheater(Request $request);

    /**
     * Get recommended movies
     *
     * @return mixed
     */
    public function getRecommendMovie(Request $request);

    /**
     * Get boot data
     *
     * @return mixed
     */
    public function getBootData();
}
