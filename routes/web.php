<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 use Illuminate\Http\Request;
Route::get('/', function () {
    return view('welcome');
});
    Route::get('/redirect', function () {
        $query = http_build_query([
            'client_id' => 9, // Replace with Client ID
            'redirect_uri' => 'http://127.0.0.1:8001/callback',
            'response_type' => 'code',
            'scope' => ''
        ]);

        return redirect('http://127.0.0.1:8000/oauth/authorize?'.$query);
    });

    Route::get('/callback', function (Request $request) {
        $response = (new GuzzleHttp\Client)->post('http://127.0.0.1:8000/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 9, // Replace with Client ID
                'client_secret' => '5ar09zDGCUme7ZWxaYfmhFvGF7adK50fsXsNm5GT', // Replace with client secret
                'redirect_uri' => 'http://127.0.0.1:8001/callback',
                'code' => $request->code,
            ]
        ]);

        session()->put('token', json_decode((string) $response->getBody(), true));

        return redirect('/details');
    });

    Route::get('/details', function () {
        $response = (new GuzzleHttp\Client)->post('http://127.0.0.1:8000/api/details', [
            'headers' => [
                 'Content-Type'=>'application/x-www-form-urlencoded',           
            	'Accept'=> 'application/json',
                'Authorization' => 'Bearer '.session()->get('token.access_token')
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    });