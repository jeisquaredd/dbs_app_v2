<?php

class database{
    
    function opencon(){
        return new PDO( 
   'mysql:host=localhost; 
        dbname=dbs_app',   
        username: 'root', 
        password: '');
    }

    function signupUser($firstname, $lastname, $username, $password)
    {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            
            // Insert into Users table
            $stmt = $con->prepare("INSERT INTO Admin (admin_FN, admin_LN, admin_username, admin_password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $username, $password]);
            
            // Get the newly inserted user_id
            $userId = $con->lastInsertId();
                 
            $con->commit();
            return $userId; // return user_id for further use (like inserting address)
        
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

    function isUsernameExists($username) {
        $con = $this->opencon();

        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();

        return $count > 0; // Returns true if username exists, false otherwise
    }

    function loginUser($username, $password)
    {
        $con = $this->opencon();
        
        $stmt = $con->prepare("SELECT * FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo 'User found: ' . print_r($user, true);
        } else {
            echo 'No user found';
        }

        if ($user && password_verify($password,  $user['admin_password'])) {
            return $user; // Return user data on success
            
    }else{
        // Invalid login
        return false;
     
    }
}

    

}