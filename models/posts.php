<?php
$dbconn = null;
if(getenv('DATABASE_URL')){
    $connectionConfig = parse_url(getenv('DATABASE_URL'));
    $host = $connectionConfig['host'];
    $user = $connectionConfig['user'];
    $password = $connectionConfig['pass'];
    $port = $connectionConfig['port'];
    $dbname = trim($connectionConfig['path'],'/');
    $dbconn = pg_connect(
        "host=".$host." ".
        "user=".$user." ".
        "password=".$password." ".
        "port=".$port." ".
        "dbname=".$dbname
    );
} else {
    $dbconn = pg_connect("host=localhost dbname=bitcoin");
}

class Post {
  public $id;
  public $name;
  public $email;
  public $body;

  public function __construct($id, $name, $email, $body){
    $this->id = $id;
    $this->name = $name;
    $this->email = $email;
    $this->body = $body;
  }
}

class Posts {
  static function all(){
    $posts = array();

    $results = pg_query("SELECT * FROM posts");

    $row_object = pg_fetch_object($results);
    while($row_object){
      $new_post = new Post(
        intval($row_object->id),
        $row_object->name,
        $row_object->email,
        $row_object->body
      );
      $posts[] = $new_post;
      $row_object = pg_fetch_object($results);
    }
    return $posts;
  }

  static function create($post){
    $query = "INSERT INTO posts (name, email, body) VALUES ($1, $2, $3)";
    $query_params = array($post->name, $post->email, $post->body);
    pg_query_params($query, $query_params);
    return self::all();
  }

  static function update($updated_post){
      $query = "UPDATE posts SET name = $1, email = $2, body = $3 WHERE id = $4";
      $query_params = array($updated_post->name, $updated_post->email, $updated_post->body, $updated_post->id);
      $result = pg_query_params($query, $query_params);

      return self::all();
    }
    static function delete($id){
      $query = "DELETE FROM posts WHERE id = $1";
      $query_params = array($id);
      $result = pg_query_params($query, $query_params);

      return self::all();
    }
}

?>
