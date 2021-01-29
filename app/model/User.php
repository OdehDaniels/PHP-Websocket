<?php
class User{

  private $conn;
  private $table_name="users";

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
   * Set User ID.
   * 
   * @param $id
   * 
   */
  public function setUserId($id){
    $this->id = $id;
  }

  /**
  * Get User ID.
  */
  public function getUserId(){
    return $this->id;
  }

  /**
   * Set Username.
   * 
   * @param $username
   * 
   */
  public function setUsername($username){
    $this->username = $username;
  }

  /**
  * Get Username.
  */
  public function getUsername(){
    return $this->username;
  }

  /**
   * Set Email.
   * 
   * @param $email
   * 
   */
  public function setEmail($email){
    $this->email = $email;
  }

  /**
  * Get Email.
  */
  public function getEmail(){
    return $this->email;
  }

  /**
   * Set Password.
   * 
   * @param $password
   * 
   */
  public function setPassword($password){
    $this->password = $password;
  }

  /**
  * Get Password.
  */
  public function getPassword(){
    return $this->password;
  }

  /**
   * Set Profile image.
   * 
   * @param $profile
   * 
   */
  public function setUserProfileImage($profile){
    $this->profile = $profile;
  }

  /**
  * Get Profile.
  */
  public function getUserProfileImage(){
    return $this->profile;
  }

  /**
   * Set Status.
   * 
   * @param $status
   * 
   */
  public function setStatus($status){
    $this->status = $status;
  }

  /**
  * Get Status.
  */
  public function getStatus(){
    return $this->status;
  }

  /**
   * Set User isActive Status.
   * 
   * @param $is_active
   * 
   */
  public function setUserIsActiveStstus($is_active){
    $this->is_active = $is_active;
  }

  /**
  * Get isActive Status.
  */
  public function getUserIsActiveStstus(){
    return $this->is_active;
  }

  /**
   * Set User Verification Token.
   * 
   * @param $verification_token
   * 
   */
  public function setUserVerificationToken($verification_token){
    $this->verification_token = $verification_token;
  }

  /**
  * Get User Verification Token.
  */
  public function getsetUserVerificationToken(){
    return $this->verification_token;
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

    if ($stmt->execute()) {
        $num = $stmt->rowCount();
        if ($num>0) {
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          return true;
        }
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
            $this->password = $row['password'];
            $this->is_active = $row['is_active'];
            $this->profile = $row['profile'];
            $this->status = $row['status'];
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
        username=:username,
        email=:email,
        password=:password,
        profile=:profile,
        status=:status,
        verification_token=:verification_token,
        created_at=:created_at
      ";
    $stmt = $this->conn->prepare($query);

    $this->username=htmlspecialchars(strip_tags($this->username));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->password=htmlspecialchars(strip_tags($this->password));
    $this->profile=htmlspecialchars(strip_tags($this->profile));
    $this->status=htmlspecialchars(strip_tags($this->status));
    $this->verification_token=htmlspecialchars(strip_tags($this->verification_token));
    $this->created_at=htmlspecialchars(strip_tags($this->created_at));

    $stmt->bindParam(':username', $this->username);
    $stmt->bindParam(':email', $this->email);
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password_hash);
    $stmt->bindParam(':profile', $this->profile);
    $stmt->bindParam(':status', $this->status);
    $stmt->bindParam(':verification_token', $this->verification_token);
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
   * Make Avatar.
   * 
   * @param $character
   * 
   */
  public function makeAvatar($character){
    $path = "images/". time() .".png";
    $image = imagecreate(200, 200);
    $red = rand(0, 255);
    $blue = rand(0, 255);
    $green = rand(0, 255);
    imagecolorallocate($image, $red, $blue, $green);
    $textColor = imagecolorallocate($image, 255,255,255);

    $font = dirname(__FILE__) .'/font/arial.ttf';

    imagettftext($image, 100, 0, 55, 150, $textColor, $font, $character);
    imagepng($image, $path);
    imagedestroy($image);

    return $path;

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
    ini_set("error_log", "app/logs/error.log");
    error_log( ":: Method Name::" . __METHOD__);
    error_log( ":: ERROR::" .$stmt->errorInfo()[2]);
  }

}

?>
