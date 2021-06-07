<?php

namespace App\Models;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post {

    public $title;

    public $slug;

    public $except;

    public $date;

    public $body;

    public function __construct($title, $slug, $except, $date, $body) {
        $this->title = $title;
        $this->slug = $slug;
        $this->except = $except ;
        $this->date = $date;
        $this->body = $body;
    }

    public static function all(){
        return cache()->rememberForever('posts.all', function (){
            return collect(File::files(resource_path("posts/")))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->title,
                    $document->slug,
                    $document->except,
                    $document->date,
                    $document->body(),
                ))->sortByDesc('date');   
        });
    }

    public static function find($slug){
       return static::all()->firstWhere('slug', $slug);
    }

    public static function findOrFail($slug){
       $post = static::find($slug);
       if(!$post) {
           throw new ModelNotFoundException();
       }
       return $post;
    }
}

?>