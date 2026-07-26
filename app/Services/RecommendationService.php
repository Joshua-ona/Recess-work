<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecommendationService
{

    private string $api;


    public function __construct()
    {
        $this->api = config('services.recommendation.url');
    }



    public function discussions($userId)
    {

        $response = Http::timeout(5)
            ->get(
                $this->api.'/recommendations/'.$userId
            );


        if($response->successful())
        {
            return $response->json();
        }


        return [
            "status"=>"error",
            "recommendations"=>[]
        ];

    }



public function groups($userId)
{
    $response = Http::timeout(5)
        ->get(
            $this->api.'/recommend-groups/'.$userId
        );


    if($response->successful())
    {
        $data = $response->json();

        return $data['recommendations'] ?? [];
    }


    return [];
}



}