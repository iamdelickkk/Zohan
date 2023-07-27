<?php
class Follow extends User{
	public function followButton($profileUsername, $username){
		if($profileUsername == $username){
			echo '<button class="button global--link" data-link="/personalize"><i class="ion-edit"></i>Редактировать</button>';
		}else{
			if($this->checkFollow($profileUsername, $username) === true){
				echo '<button class="button" id="follow" data-follow="'.$profileUsername.'"><i class="ion-minus"></i>Отписаться</button>';
			}else{
				echo '<button class="button" id="follow" data-follow="'.$profileUsername.'"><i class="ion-person-add"></i>Подписаться</button>';
			}
		}
	}

	public function checkCommunityFollow($communityIdent, $username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers WHERE communityFollow = :followTo AND followBy = :followBy");
		$stmt->bindValue(':followTo', $communityIdent);
		$stmt->bindValue(':followBy', $username);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			return true;
		}else{
			return false;
		}
	}

	public function CommunityFollowButton($communityIdent, $username, $communityBy){
		if($communityBy == $username){
			echo '<button class="button global--link" data-link="/config?community='.$communityIdent.'"><i class="ion-edit"></i>Редактировать</button>';
		}else{
			if($this->checkCommunityFollow($communityIdent, $username) === true){
				echo '<button class="button" id="follow_community" data-follow="'.$communityIdent.'"><i class="ion-minus"></i>Отписаться</button>';
			}else{
				echo '<button class="button" id="follow_community" data-follow="'.$communityIdent.'"><i class="ion-plus"></i>Подписаться</button>';
			}
		}
	}

	public function checkFriends($followTo, $username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers WHERE (followTo = :followTo AND followBy = :followBy) OR (followTo = :followBy AND followBy = :followTo)");
		$stmt->bindValue(':followTo', $followTo);
		$stmt->bindValue(':followBy', $username);
		$stmt->execute();
		if($stmt->rowCount() == 2){
			return true;
		}else{
			return false;
		}
	}

	public function checkFollow($followTo, $username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers WHERE followTo = :followTo AND followBy = :followBy");
		$stmt->bindValue(':followTo', $followTo);
		$stmt->bindValue(':followBy', $username);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			return true;
		}else{
			return false;
		}
	}

	public function FollowCommunity($communityIdent, $username){
		if($this->checkCommunityFollow($communityIdent, $username) === true){
			$stmt = $this->pdo->prepare("DELETE FROM followers WHERE communityFollow = :followTo AND followBy = :followBy");
			$stmt->bindValue(':followTo', $communityIdent);
			$stmt->bindValue(':followBy', $username);
			$stmt->execute();
		}else{
			$stmt = $this->pdo->prepare("INSERT INTO followers(communityFollow, followBy) VALUES(:followTo, :followBy)");
			$stmt->bindValue(':followTo', $communityIdent);
			$stmt->bindValue(':followBy', $username);
			$stmt->execute();
		}
	}

	public function FollowUser($followTo, $username){
		if($this->checkUsername($followTo) === true && $this->checkUsername($username) === true){
			if($this->checkFollow($followTo, $username) === true){
				$stmt = $this->pdo->prepare("DELETE FROM followers WHERE followTo = :followTo AND followBy = :followBy");
				$stmt->bindValue(':followTo', $followTo);
				$stmt->bindValue(':followBy', $username);
				$stmt->execute();
			}else{
				$stmt = $this->pdo->prepare("INSERT INTO followers(followTo, followBy) VALUES(:followTo, :followBy)");
				$stmt->bindValue(':followTo', $followTo);
				$stmt->bindValue(':followBy', $username);
				$stmt->execute();
			}
		}
	}

	public function CommunityFollowersCount($communityIdent){
		$stmt = $this->pdo->prepare("SELECT * FROM followers WHERE communityFollow = :followTo");
		$stmt->bindValue(':followTo', $communityIdent);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function FollowersCount($username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers WHERE followTo = :followTo AND communityFollow = ''");
		$stmt->bindValue(':followTo', $username);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function FollowingCount($username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers WHERE followBy = :followBy AND communityFollow = ''");
		$stmt->bindValue(':followBy', $username);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function FollowersCommunity($communityIdent){
		$stmt = $this->pdo->prepare("SELECT * FROM followers LEFT JOIN users ON username = followBy WHERE communityFollow = :followTo");
		$stmt->bindValue(':followTo', $communityIdent);
		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
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

	public function Followers($username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers LEFT JOIN users ON username = followBy WHERE followTo = :followTo");
		$stmt->bindValue(':followTo', $username);
		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		if($stmt->rowCount() < 1){
			echo '<center>Пока что тут ничего нет...</center>';
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

	public function Following($username){
		$stmt = $this->pdo->prepare("SELECT * FROM followers LEFT JOIN users ON username = followTo WHERE followBy = :followBy AND communityFollow = ''");
		$stmt->bindValue(':followBy', $username);
		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		if($stmt->rowCount() < 1){
			echo '<center>Пока что тут ничего нет...</center>';
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
}
?>