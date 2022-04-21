<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use MongoDB\Client as Mongo;

class SearchPeopleController extends Controller
{

    public function index() {
        return view("search_people");
    }

    public function do_search_all_users(Request $request) {
        $utenti = DB::select("SELECT * FROM users");
        return response()->json($utenti);
    }

    public function do_search_people(Request $request) {

        $request->validate([
            "testo" => "required|string",
        ]);

        $utente = DB::select("SELECT * FROM users u where u.username like ? or concat(u.name, ' ', u.surname) like ?", ['%' . $request->testo . '%', '%' . $request->testo . '%']);
        return response()->json($utente);
    }

    public function follow_people(Request $request) {

        $request->validate([
            "param1" => "required|string",
        ]);

        $user = Auth::user();
        $followed_user = $request->param1;
        if($user->username!==$followed_user)
        {
            DB::insert("INSERT INTO hw2_followers(user_username, user_followed) VALUES(?, ?)", [$user->username, $followed_user]);
            return 1;
        }
        else {
            return 0;
        }
    }

    public function unfollow_people(Request $request) {

        $request->validate([
            "param1" => "required|string",
        ]);

        $user = Auth::user();
        $followed_user = $request->param1;
        if($user->username!==$followed_user) {
            DB::delete("DELETE FROM hw2_followers WHERE user_username=? AND user_followed=?", [$user->username, $followed_user]);
            return 1;
        }
        else {
            return 0;
        }
    }

    public function verifica_username(Request $request) {

        $request->validate([
            "param1" => "required|string",
        ]);

        $user = Auth::user();
        $followed_user = $request->param1;
        if($user->username!==$followed_user) {
            $res = DB::select("SELECT * FROM hw2_followers WHERE user_username=? AND user_followed=?", [$user->username, $followed_user]);
            echo count($res);
        }
        else {
            echo 1;
        }
    }

}
