<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;


class CreatePostController extends Controller
{

    public function index() {
        return view("create_post");
    }

    public function do_search_content(Request $request) {

        $request->validate([
            "testo" => "required|string",
            "servizio" => "required"
        ]);

        if($request->servizio=='Spotify')
        {

            $client_id = env("SPOTIFY_CLIENT_ID");
            $client_secret = env("SPOTIFY_CLIENT_SECRET");


            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://accounts.spotify.com/api/token");
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $headers = array("Authorization: Basic ".base64_encode($client_id.":".$client_secret));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);


            $artist=$request->testo;
            $token = json_decode($result)->access_token;
            $data = http_build_query(array("q" => $artist, "type" => "artist"));
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/search?".$data);
            $headers = array("Authorization: Bearer ".$token);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            return $result;

        }


        if($request->servizio=='Giphy')
        {

            $api_key = env("GIPHY_API_KEY");
            $file_value = urlencode($request->testo);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.giphy.com/v1/gifs/search?api_key=".$api_key."&q=".$file_value."&limit=25&offset=0&rating=G&lang=en");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            return $result;

        }


        if($request->servizio=='OpenMovieDatabase')
        {

            $api_key = env("OPEN_MOVIE_DATABASE_API_KEY");
            $file_value = urlencode($request->testo);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://www.omdbapi.com/?apikey=".$api_key."&s=".$file_value."");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            return $result;

        }


        if($request->servizio=='Jikan')
        {

            $file_value = urlencode($request->testo);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.jikan.moe/v3/search/anime?q=".$file_value."");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            return $result;

        }
    }

    public function server_post(Request $request) {

        $request->validate([
            "param1" => "required|string",
            "param2" => "required|string"
        ]);

        $user = Auth::user();
        $url_img = $request->param1;
        $titolo = $request->param2;
        $data_e_ora = date('Y-m-d H:i:s', time());


        $query = DB::insert("INSERT into hw2_posts(creator, title, url_img, date_and_time) values(?, ?, ?, ?)", [$user->username, $titolo, $url_img, $data_e_ora]);
        $id = DB::select("SELECT max(id) as id FROM hw2_posts where creator=?", [$user->username]);
        $id = $id[0]->id;


        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $write = new \MongoDB\Driver\BulkWrite();
        $write->insert(['id' => $id]);
        try {
            $res = $manager->executeBulkWrite('laravel.mdb_posts', $write);
        }
        catch(Exception $e) {
            echo "Errore";
        }

    }
}
