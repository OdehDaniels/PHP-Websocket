<?php
class User{

  private $conn;
  private $table_name="user";

  public $id;
  public $username;
  public $email;
  public $password;
  public $profile;
  public $is_active;
  public $status;
  public $verification_token;
  public $created_at;
  public $updated_at;

  public function __construct($db){
      $this->conn = $db;
  }

  /**
   * Return User ID by user's email.
   */
  public function getUserIdByEmail(){

      $query = "SELECT
                    id
               FROM
                  " . $this->table_name . "
                  WHERE
                    email = ?
                      LIMIT
                        0,1";

    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $this->email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $num = $stmt->rowCount();
    if($num>0){
      $this->id = $row['id'];
      return true;
    } else {
      $this->showError($stmt);
        return false;
    }
  }

  /**
   * Return current user by session Id.
   */
  public function getUserBySessionId(){
    //$notAdmin = \Userprofile::SUPER_ADMIN;
    $query = "SELECT *
                FROM
                  " . $this->table_name . "
              WHERE 
                profile != '{$notAdmin}'
              AND 
                id = '{$_SESSION['userId']}'
              AND 
                email = '{$_SESSION['email']}'
                LIMIT 0,1";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt;
  }
  
  /**
   * Return all users.
   */
  public function getAllUser(){

    //$userprofile = \Userprofile::USER;
    $query = "SELECT *
                FROM
                  " . $this->table_name . "
                  WHERE 
                    profile = '{$userprofile}'
                ORDER BY
                  id DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  /**
   * Return User's Email exists
   */
  public function emailAlreadyExists(){

    $query = "SELECT *
            FROM " . $this->table_name . "
            WHERE email = ?
            LIMIT 0,1";

    $stmt = $this->conn->prepare( $query );
    $this->email=htmlspecialchars(strip_tags($this->email));
    $stmt->bindParam(1, $this->email);

    $stmt->execute();
    $num = $stmt->rowCount();
      if($num>0){

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      $this->username = $row['username'];
      $this->email = $row['email'];
      $this->password = $row['password'];
      $this->is_active = $row['is_active'];
      $this->profile = $row['profile'];
      $this->status = $row['status'];
      $this->id = $row['id'];

      return true;
      } else {
      $this->showError($stmt);
      return false;
    }
    
  } 

  /**
   * Return User by ID.
   */
  public function getUserById(){
    $query = "SELECT
                  *
              FROM
                " . $this->table_name . "
                WHERE
                  id = ?
                    LIMIT
                      0,1";

    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $this->id);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->username = $row['username'];
    $this->email = $row['email'];
    $this->status = $row['status'];
    $this->profile = $row['profile'];
    $this->id = $row['id'];
  }

  /**
   * Add new User email & password.
   */
  public function registerNewUser(){
    $this->created_at = date('Y-m-d H:i:s');

      $query = "INSERT INTO
          " . $this->table_name . "
      SET
        email=:email,
        password=:password,
        created_at=:created_at
      ";
    $stmt = $this->conn->prepare($query);

    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->password=htmlspecialchars(strip_tags($this->password));
    $this->created_at=htmlspecialchars(strip_tags($this->created_at));

    $stmt->bindParam(':email', $this->email);
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password_hash);
    $stmt->bindParam(':created_at', $this->created_at);

      if($stmt->execute()){
        return true;
      } else {
        $this->showError($stmt);
        return false;
      }

  }


  /**
   * Add | Update User by Id.
   */
  public function updateUserById(){
    $query = "UPDATE
        " . $this->table_name . "
    SET
      username=:username,
      email=:email,
      status=:status,
      profile=:profile
    WHERE
      id=:id
    ";
  $stmt = $this->conn->prepare($query);

  $this->username=htmlspecialchars(strip_tags($this->username));
  $this->email=htmlspecialchars(strip_tags($this->email));
  $this->status=htmlspecialchars(strip_tags($this->status));
  $this->profile=htmlspecialchars(strip_tags($this->profile));
  $this->id=htmlspecialchars(strip_tags($this->id));

  $stmt->bindParam(':username', $this->username);;
  $stmt->bindParam(':email', $this->email);
  $stmt->bindParam(':status', $this->status);
  $stmt->bindParam(':profile', $this->profile);
  $stmt->bindParam(':id', $this->id);

    if($stmt->execute()){
      return true;
    } else {
      $this->showError($stmt);
      return false;
    }
    return false;

  }

  /**
   * Delete User by ID.
   */
  public function deleteUser(){

    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->id);

    if($stmt->execute()){
        return true;
    } else {
      $this->showError($stmt);
      return false;
    }

  }

  /**
   * Change User is_active status.
   */
  public function changeStatus(){
    $query = "UPDATE
                " . $this->table_name . "
            SET
              is_active=:is_active
            WHERE 
              email = '{$_SESSION['email']}'
            AND 
              id = '{$_SESSION['user_id']}'";

    $stmt = $this->conn->prepare($query);

    if($stmt->execute()){
        return true;
      }
    return false;
  }

  /**
  * Return User count.
  *
  * @return mixed
  */
  public function getUserCount(){
    $activeStatus = \ActiveStatus::ACTIVE;
    $notAdmin = \Userprofile::SUPER_ADMIN;
    $query = "SELECT *
                FROM
                  " .$this->table_name. 
              " WHERE 
                  profile != '{$notAdmin}'
                AND
                  is_active ='{$activeStatus}'";

    $stmt = $this->conn->prepare($query);

    $stmt->execute();
    $count = $stmt->rowCount();

    return $count;
  }


  public function showError($stmt){
    ini_set("log_errors", 1);
    ini_set("error_log", "../../../app/logs/error.log");
    error_log( ":: Method Name::" . __METHOD__);
    error_log( ":: ERROR::" .$stmt->errorInfo()[2]);
  }

}

?>
