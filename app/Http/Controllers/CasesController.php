<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cases;
use Illuminate\Support\Facades\Http;


class CasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cases::get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->findCountry($request)) {
            return response()->json(['message' => 'Conflict'], 409);
        }

        $newCases = new Cases;

        $this->setCasesToModelFromRequest($newCases, $request)->save();

        return $newCases;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Insert or update data in the storage
     *
     * @return \Illuminate\Http\Response
     */
    public function fill()
    {
        $response = Http::get('https://api.covid19api.com/summary');

        $countries = $response->json()['Countries'];

        $countryObjProps =
            array('CountryCode', 'NewConfirmed', 'TotalConfirmed', 'NewDeaths', 'TotalDeaths', 'NewRecovered', 'TotalRecovered');

        foreach ($countries as $country) {

            $cases = $this->findCountry($country, $country[$countryObjProps[0]]);

            if ($cases) {

                $this->setCasesToModelFromObj($cases, $country, $countryObjProps)->save();

                continue;
            }

            $newCases = new Cases;

            $this->setCasesToModelFromObj($newCases, $country, $countryObjProps)->save();
        }

        return Cases::get();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $cases = $this->findCountry($request);

        if (!$cases) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $this->setCasesToModelFromRequest($cases, $request)->save();

        return $cases;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function findCountry($request, $code = null)
    {
        return Cases::where('country_code', $code ? $code : $request->input('country_code'))->first();
    }

    private function setCasesToModelFromRequest($model, $request)
    {
        $model->country_code = $request->input('country_code');
        $model->new_confirmed = $request->input('new_confirmed');
        $model->total_confirmed = $request->input('total_confirmed');
        $model->new_deaths = $request->input('new_deaths');
        $model->total_deaths = $request->input('total_deaths');
        $model->new_recovered = $request->input('new_recovered');
        $model->total_recovered = $request->input('total_recovered');

        return $model;
    }

    private function setCasesToModelFromObj($model, $obj, $prop)
    {
        $model->country_code = $obj[$prop[0]];
        $model->new_confirmed = $obj[$prop[1]];
        $model->total_confirmed = $obj[$prop[2]];
        $model->new_deaths = $obj[$prop[3]];
        $model->total_deaths = $obj[$prop[4]];
        $model->new_recovered = $obj[$prop[5]];
        $model->total_recovered = $obj[$prop[6]];

        return $model;
    }

}
