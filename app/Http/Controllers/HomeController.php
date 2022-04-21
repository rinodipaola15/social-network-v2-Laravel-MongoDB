<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Jenssegers\Mongodb\Eloquent\Model;
use App\UserMongoDB;





class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        return view('home', [
            "nome" => $user->username,
            "url_img" => $user->photo
        ]);
    }


    public function carica_post() {

        $user = Auth::user();
        $posts = DB::select("SELECT * FROM hw2_posts p join users u on p.creator=u.username where p.creator=? UNION (SELECT * from hw2_posts p join users u on p.creator=u.username where p.creator in (SELECT f.user_followed from hw2_followers f join users u on f.user_username=?)) order by date_and_time DESC", [$user->username, $user->username]);


        $num_posts = count($posts);
        for($i=0; $i<$num_posts; $i++) {
            $post = (int) $posts[$i]->id;
            $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
            $query = new \MongoDB\Driver\Query(['id' => $post]);
            $cursor = $manager->executeQuery('laravel.mdb_posts', $query);
            $count=0;
            foreach($cursor as $doc){
                $count = $count+1;
            }
            if($count===0) {
                $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
                $write = new \MongoDB\Driver\BulkWrite();
                $write->insert(['id' => $post]);
                try {
                    $res = $manager->executeBulkWrite('laravel.mdb_posts', $write);
                }
                catch(Exception $e) {
                    echo "Errore";
                }
                $utenti_like = DB::select("SELECT * FROM hw2_likes l join users u on l.username=u.username where l.post=?", [$post]);
                $num_likes = count($utenti_like);
                if($num_likes>0) {
                    for($j=0; $j<$num_likes; $j++) {
                        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
                        $write = new \MongoDB\Driver\BulkWrite();
                        $write->update(['id' => $post], ['$push' => ['likers' => ['username' => $utenti_like[$j]->username, 'name' => $utenti_like[$j]->name, 'surname' => $utenti_like[$j]->surname, 'photo' => $utenti_like[$j]->photo]]]);
                        try {
                            $res = $manager->executeBulkWrite('laravel.mdb_posts', $write);
                        }
                        catch(Exception $e) {
                            echo "Errore";
                        }
                    }
                }
            }
        }

        return response()->json($posts);
    }

    public function controllo_like(Request $request) {

        $request->validate([
            "id_post" => "required",
        ]);

        $user = Auth::user();

            $id_post = (int) $request->id_post;
            $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
            $query = new \MongoDB\Driver\Query(['id' => $id_post, 'likers' => ['$elemMatch' => ['username' => $user->username]]]);
            $cursor = $manager->executeQuery('laravel.mdb_posts', $query);
            $counter = 0;
            foreach($cursor as $doc) {
                $counter = $counter+1;
            }
            if($counter===0)
                return 0;
            else
                return 1;

    }

    public function conteggio_like(Request $request) {

        $request->validate([
            "id_post" => "required",
        ]);

            $id_post = (int) $request->id_post;
            $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
            $query = new \MongoDB\Driver\Query(['id' => $id_post]);
            $cursor = $manager->executeQuery('laravel.mdb_posts', $query);
            $result = $cursor->toArray();
            if(isset($result[0]->likers)) {
                $likers = $result[0]->likers;
                return count($likers);
            }
            else return 0;

    }

    public function aggiungi_like(Request $request) {

        $request->validate([
            "id_post" => "required",
        ]);

        $user = Auth::user();

            $liked_post = (int) $request->id_post;
            $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
            $write = new \MongoDB\Driver\BulkWrite();
            $photo = DB::select("SELECT photo from users WHERE username=?", [$user->username]);
            $photo = $photo[0]->photo;
            $write->update(['id' => $liked_post], ['$push' => ['likers' => ['username' => $user->username, 'name' => $user->name, 'surname' => $user->surname, 'photo' => $photo]]]);
            try {
                $res = $manager->executeBulkWrite('laravel.mdb_posts', $write);
            }
            catch(Exception $e) {
                echo "Errore";
            }

    }

    public function rimuovi_like(Request $request) {

        $request->validate([
            "id_post" => "required",
        ]);

        $user = Auth::user();

            $liked_post = (int) $request->id_post;
            $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
            $write = new \MongoDB\Driver\BulkWrite();
            $write->update(['id' => $liked_post], ['$pull' => ['likers' => ['username' => $user->username]]]);
            try {
                $res = $manager->executeBulkWrite('laravel.mdb_posts', $write);
            }
            catch(Exception $e) {
                echo "Errore";
            }

    }

    public function lista_like(Request $request) {

        $request->validate([
            "id_post" => "required",
        ]);

        $post = (int) $request->id_post;
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $query = new \MongoDB\Driver\Query(['id' => $post]);
        $cursor = $manager->executeQuery('laravel.mdb_posts', $query);
        return response()->json($cursor->toArray());

    }

}
