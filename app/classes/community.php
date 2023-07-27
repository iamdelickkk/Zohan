<?php
class Community extends User{

	public function AllCommunitiesCount(){
		$stmt = $this->pdo->prepare("SELECT * FROM communities");
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function communityCover($communityIdent){
		$communityy = $this->getCommunityData($communityIdent);
		if(!empty($communityy->communityCover)){
			echo 'style="background: url(/'.$communityy->communityCover.') center/cover!important"';
		}
	}

	public function search($query){
		$stmt = $this->pdo->prepare("SELECT * FROM communities WHERE communityName LIKE '%$query%' ORDER BY communityID DESC");
		$stmt->execute();
		$communities = $stmt->fetchAll(PDO::FETCH_OBJ);

		if($stmt->rowCount() == 0){
			echo '<center>Ничего не найдено</center>';
		}

		foreach($communities as $communityy){
			echo '<div class="profile_mini" '.(!empty($communityy->communityCover) ? 'style="background: url(/'.$communityy->communityCover.') center/cover!important"' : '').'>
                    <div class="profile_mini_content global--link" data-link="/community?v='.$communityy->communityIdent.'">
                        <div>
                            <div class="profile_mini_image">
                                <span>
                                    <img src="'.$communityy->communityImage.'" id="pfp_a" alt="Community Picture">
                                </span>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        '.$communityy->communityName.'
                                    </span>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>';
		}
	}

	public function MyCommunities($username){
		$stmt = $this->pdo->prepare("SELECT * FROM communities WHERE communityBy = :username ORDER BY communityID DESC");
		$stmt->bindValue(':username', $username);
		$stmt->execute();
		$communities = $stmt->fetchAll(PDO::FETCH_OBJ);

		if($stmt->rowCount() == 0){
			echo '<center>Пока что тут ничего нет...</center>';
		}

		foreach($communities as $communityy){
			echo '<div class="profile_mini" '.(!empty($communityy->communityCover) ? 'style="background: url(/'.$communityy->communityCover.') center/cover!important"' : '').'>
                    <div class="profile_mini_content global--link" data-link="/community?v='.$communityy->communityIdent.'">
                        <div>
                            <div class="profile_mini_image">
                                <span>
                                    <img src="'.$communityy->communityImage.'" id="pfp_a" alt="Community Picture">
                                </span>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        '.$communityy->communityName.'
                                    </span>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>';
		}
	}

	public function PublishStatus($username, $statusText, $communityIdent, $imgFiles){
		$communityy = $this->getCommunityData($communityIdent);
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $ident = '';
        for ($i = 0; $i < 64; $i++) {
            $ident .= $characters[random_int(0, $charactersLength - 1)];
        }

        if($imgFiles !== false){
            $totalImgFiles = count($imgFiles['name']);

            if($totalImgFiles < 11){
                $statusText = $statusText.'<div class="PostImgs">';
                for($i = 0; $i < $totalImgFiles; $i++){
                    $imageName = $imgFiles["name"][$i];
                    $tmpName = $imgFiles["tmp_name"][$i];

                    $imageExtension = explode('.', $imageName);

                    $name = $imageExtension[0];
                    $imageExtension = strtolower(end($imageExtension));

                    $newImageName = md5($name) . " - " . uniqid(); // Generate new image name
                    $newImageName .= '.' . $imageExtension;

                    move_uploaded_file($tmpName, $_SERVER['DOCUMENT_ROOT'].'/uploads/' . $newImageName);
                    $statusText = $statusText.'<div class="PostImgI"><img src="https://zohan.fun/uploads/'.$newImageName.'"></div>';
                }
                $statusText = $statusText.'</div>';
            }
        }

		if($communityy->communityBy != $username){
			if($communityy->communityPublicNews == 1){
				$stmt = $this->pdo->prepare("INSERT INTO statuses(statusBy, statusText, statusIdent, statusByCommunity) VALUES(:statusBy, :statusText, :statusIdent, :statusByCommunity)");
			}
		}else{
			$stmt = $this->pdo->prepare("INSERT INTO statuses(statusBy, statusText, statusIdent, statusByCommunity) VALUES(:statusBy, :statusText, :statusIdent, :statusByCommunity)");
		}
		$stmt->bindValue(':statusBy', $username);
		$stmt->bindValue(':statusText', $statusText);
		$stmt->bindValue(':statusIdent', $ident);
		$stmt->bindValue(':statusByCommunity', $communityIdent);
		$stmt->execute();
	}

	public function getCommunityData($communityIdent){
		$stmt = $this->pdo->prepare("SELECT * FROM communities WHERE communityIdent = :communityIdent");
		$stmt->bindValue(':communityIdent', $communityIdent);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function checkCommunity($communityIdent){
		$stmt = $this->pdo->prepare("SELECT * FROM communities WHERE communityIdent = :communityIdent");
		$stmt->bindValue(':communityIdent', $communityIdent);
		$stmt->execute();

		if($stmt->rowCount() > 0){
			return true;
		}else{
			return false;
		}
	}

	public function AddCommunity($name, $username){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $ident = '';
        for ($i = 0; $i < 64; $i++) {
            $ident .= $characters[random_int(0, $charactersLength - 1)];
        }

		$stmt = $this->pdo->prepare("INSERT INTO communities(communityName, communityBy, communityIdent, communityImage) VALUES(:name, :username, :ident, '/img/group.png')");
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':username', $username);
		$stmt->bindValue(':ident', $ident);
		$stmt->execute();

		header("Location: /config?community=".$ident);
	}

	public function AllCommunities($limit){
		$stmt = $this->pdo->prepare("SELECT * FROM communities ORDER BY communityID DESC LIMIT $limit");
		$stmt->execute();
		$communities = $stmt->fetchAll(PDO::FETCH_OBJ);

		if($stmt->rowCount() == 0){
			echo '<center>Пока что тут ничего нет...</center>';
		}

		foreach($communities as $communityy){
			echo '<div class="profile_mini" '.(!empty($communityy->communityCover) ? 'style="background: url(/'.$communityy->communityCover.') center/cover!important"' : '').'>
                    <div class="profile_mini_content global--link" data-link="/community?v='.$communityy->communityIdent.'">
                        <div>
                            <div class="profile_mini_image">
                                <span>
                                    <img src="'.$communityy->communityImage.'" id="pfp_a" alt="Community Picture">
                                </span>
                            </div>
                            <div>
                                <b>
                                    <span>
                                        '.$communityy->communityName.'
                                    </span>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>';
		}

		if($this->AllCommunitiesCount() > $limit){
			$newlimit = $limit + 16;
            echo '<center>
            <button class="button global--link" style="float:left;" data-link="/communities?limit='.$newlimit.'">Показать больше</button>
            </center>';
		}
	}

}
?>