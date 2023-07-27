<?php
class User{
	protected $pdo;

	public function __construct($pdo){
		$this->pdo = $pdo;
	}

	public function checkUsername($string){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
		$stmt->bindValue(":username", $string);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			return true;
		}else{
			return false;
		}
	}

	public function checkEmail($string){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
		$stmt->bindValue(":email", $string);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			return true;
		}else{
			return false;
		}
	}

	public function checkToken($token){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE token = :token");
		$stmt->bindValue(":token", $token);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			return true;
		}else{
			return false;
		}
	}

	public function getUserData($username){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
		$stmt->bindValue(":username", $username);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function profileCover($username){
		$user = $this->getUserData($username);
		if(!empty($user->coverImage)){
			echo 'style="background: url(/'.$user->coverImage.') center/cover!important"';
		}
	}


	public function getUserDataToken($token){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE token = :token");
		$stmt->bindValue(":token", $token);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function getUserDataEmail($email){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
		$stmt->bindValue(":email", $email);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function register($name, $username, $email, $password){
		$passwordHashed = password_hash($password, PASSWORD_DEFAULT);

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $token = '';
	    for ($i = 0; $i < 255; $i++) {
	        $token .= $characters[random_int(0, $charactersLength - 1)];
	    }

		$stmt = $this->pdo->prepare("INSERT INTO users(username, password, email, name, profileImage, token) VALUES(:username, :password, :email, :name, '/img/account.png', :token)");

		$stmt->bindValue(":username", $username);
		$stmt->bindValue(":password", $passwordHashed);
		$stmt->bindValue(":email", $email);
		$stmt->bindValue(":name", $name);
		$stmt->bindValue(":token", $token);
		$stmt->execute();
	}

	public function searchUsers($query){
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE name LIKE '%$query%' OR username LIKE '%$query%' ORDER BY userID DESC");

		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		if($stmt->rowCount() == 0){
			echo '<center>Ничего не найдено</center>';
		}
		foreach($users as $user){
			echo '<div class="profile_mini" '.(!empty($user->coverImage) ? 'style="background: url(/'.$user->coverImage.') center/cover!important"' : '').'>
                    <div class="profile_mini_content" data-profile="'.$user->username.'">
                        <div>
                            <div class="profile_mini_image">
                                <span>
                                    <img src="'.$user->profileImage.'" id="pfp_a" alt="Profile Picture">
                                </span>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        '.$user->name.'
                                    </span>
                                </b>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        @'.$user->username.'
                                    </span>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>';
		}
	}

	public function blocklist_all($username){
		$stmt = $this->pdo->prepare("SELECT * FROM blocklist LEFT JOIN users ON username = blockTo WHERE blockBy = :blockBy ORDER BY blockID DESC");
		$stmt->bindValue(":blockBy", $username);
		$stmt->execute();
		$blocks = $stmt->fetchAll(PDO::FETCH_OBJ);
		if($stmt->rowCount() == 0){
			echo '<center>Эм... Наверное те, которые ненавидят вас, не зарегистрированны в Zohan =D</center>';
		}
		foreach($blocks as $block){
			echo '<div class="profile_mini">
                    <div class="profile_mini_content" data-profile="'.$block->username.'">
                        <div>
                            <div class="profile_mini_image">
                                <span>
                                    <img src="img/blocked.png" id="pfp_a" alt="Profile Picture">
                                </span>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        '.$block->name.'
                                    </span>
                                </b>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        @'.$block->blockTo.'
                                    </span>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>';
		}
	}

	public function block($username, $by){
		if($username != $by){
			if($this->checkBlock($by, $username) === false){
				$stmt = $this->pdo->prepare("INSERT INTO blocklist(blockTo, blockBy) VALUES(:blockTo, :blockBy)");
			}else{
				$stmt = $this->pdo->prepare("DELETE FROM blocklist WHERE blockTo = :blockTo AND blockBy = :blockBy");
			}
			$stmt->bindValue(":blockTo", $username);
			$stmt->bindValue(":blockBy", $by);
			$stmt->execute();
		}
	}

	public function whoBlocked($blockTo, $blockBy){
		$stmt = $this->pdo->prepare("SELECT * FROM blocklist WHERE blockTo = :blockTo AND blockBy = :blockBy");
		$stmt->bindValue(":blockTo", $blockTo);
		$stmt->bindValue(":blockBy", $blockBy);
		$stmt->execute();
		if($stmt->rowCount() == 0){
			$anstmt = $this->pdo->prepare("SELECT * FROM blocklist WHERE blockTo = :blockTo AND blockBy = :blockBy");
			$anstmt->bindValue(":blockTo", $blockBy);
			$anstmt->bindValue(":blockBy", $blockTo);
			$anstmt->execute();
			return $blockTo;
		}else{
			return $blockBy;
		}
	}

	public function checkBlock($by, $to){
		$stmt = $this->pdo->prepare("SELECT * FROM blocklist WHERE blockBy = :by AND blockTo = :to");

		$stmt->bindValue(":by", $by);
		$stmt->bindValue(":to", $to);
		$stmt->execute();

		if($stmt->rowCount() > 0){
			return true;
		}else if($by == $to){
			return false;
		}else{
			return false;
		}
	}
}
?>