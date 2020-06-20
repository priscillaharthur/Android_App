<?php
//include db connection file
    require_once 'connection.php';
//store response in array
    $response = array();

    if (isset($_GET['connection.php'])) {
      //switch apicalls btn sign up, login, forgot password and request pasword
      switch (isset($_GET['apicall'])) {
        case 'signup':

//case for sign up
      //check for details required
        if (isThisParametersAvailable(array('username', 'fullname', 'email', 'password'))) {
          $username = $_POST['username'];
          $fullname = $_POST['fullname'];
          $email = $_POST['email'];
          $password = md5($_POST['password']); //hash password

      //prepared statements for sign up
          $stmt = $conn->prepare("SELECT Id FROM users WHERE username =? OR email=?");
          $stmt->bind_param("ss", $username, $email);
          $stmt->execute();
          $stmt->store_result();

          //already registered user
          if ($stmt->num_rows > 0) {
            $response['error'] = true;
            $response['message'] = "User already exists";
            $stmt->close();
          }
          //new user
          else {
            $stmt= $conn->prepare("INSERT INTO users(Id, username, fullname,email, password) VALUES(?,?,?,?)");
            $stmt->bind_param("issss", $Id, $username, $fullname, $email, $password);

            //login new user
            if ($stmt->execute()) {
              $stmt= $conn->prepare("SELECT Id, username, fullname, email, password FROM users WHERE username = ?");
              $stmt ->bind_params("s", $username);
              $stmt->execute();
              $stmt->bind_params("issss", $Id, $username, $fullname, $email, $password);
              $stmt->fetch();

              // json_encode array for showing user data
            $user= array(
              'id'=>$Id,
              'username' => $username,
              'email' => $email,
              'fullname' => $fullname,
              'password' => $password
            );
            $stmt->close();
            $response ['error'] = true;
            $response ['message']= "User registered successfully";
            $response['user']= $user;
          }
        }
      }else {
        $response ['error'] = true;
        $response ['message']= "Required details are missing";
      }
          break;

//case for login

//default for switch
        default:
        $response ['error'] = true;
        $response ['message']= "Invalid Api call";
      }
    }

//display response in json code
    echo json_encode($response);

    function isTheseParametersAvailable($params){

      foreach($params as $param){
        if(!isset($_POST[$param])){
          return false;
        }
      }
      return true;
    }
 ?>
