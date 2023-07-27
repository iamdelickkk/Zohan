<?php
class Status extends User{
    public function convertText($string){
		$string = htmlspecialchars($string);
		$string = trim($string);
		$string = stripslashes($string);
		return $string;
	}
    
    public function RemoveStatus($statusIdent, $username){
        $stmt = $this->pdo->prepare("DELETE FROM statuses WHERE statusIdent = :statusIdent AND statusBy = :username");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->bindValue(':username', $username);  
        $stmt->execute();
    }

    public function RemoveComment($commentIdent, $username){
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE commentIdent = :commentIdent AND commentBy = :username");
        $stmt->bindValue(':commentIdent', $commentIdent);
        $stmt->bindValue(':username', $username);  
        $stmt->execute();
    }

    public function checkLike($statusIdent, $username){
        $stmt = $this->pdo->prepare("SELECT * FROM likes WHERE likeBy = :username AND likeTo = :statusIdent");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->bindValue(':username', $username);  
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function countLikes($statusIdent){
        $stmt = $this->pdo->prepare("SELECT * FROM likes WHERE likeTo = :statusIdent");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function LikeStatus($statusIdent, $username){
        if($this->checkLike($statusIdent, $username) === false){
            $stmt = $this->pdo->prepare("INSERT INTO likes(likeBy, likeTo) VALUES(:username, :statusIdent)");
        }else{
            $stmt = $this->pdo->prepare("DELETE FROM likes WHERE likeBy = :username AND likeTo = :statusIdent");
        }

        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->bindValue(':username', $username);  
        $stmt->execute();
    }

    public function search($query, $username){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN users ON username = statusBy LEFT JOIN communities ON communityIdent = statusByCommunity WHERE statusText LIKE '%$query%' ORDER BY statusID DESC");
        $stmt->execute();
        $statuses = $stmt->fetchAll(PDO::FETCH_OBJ);
        if($stmt->rowCount() == 0){
            echo '<center>Ничего не найдено</center>';
        }
        foreach($statuses as $status){
            echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'"><img src="'.(!empty($status->statusByCommunity) ? $status->communityImage : $status->profileImage).'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'">
                    '.(!empty($status->statusByCommunity) ? $status->communityName : $status->name).'
                </a>
                '.(!empty($status->statusByCommunity) ? '<span id="coOLOGgg">(@'.$status->username.')</span>' : '').'
            </div>
            <div class="post_text">
                '.(!empty(strip_tags($status->statusText, '<br><br />')) ? strip_tags($status->statusText, '<br><br />') : '<i>Данное вложение пока что отключено</i>').'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($this->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$this->countLikes($status->statusIdent).'</span>
                </div>
                <div class="post_action global--link" data-link="/post/'.$status->statusIdent.'">
                    <i class="ion-ios7-chatboxes"></i>
                    '.$this->CommentsCount($status->statusIdent).'
                </div>
                '.($status->statusBy == $username && empty($status->statusByCommunity) ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
        }
    }

	public function Publish($username, $status, $imgFiles){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $ident = '';
        for ($i = 0; $i < 64; $i++) {
            $ident .= $characters[random_int(0, $charactersLength - 1)];
        }
        if($imgFiles !== false){
            $totalImgFiles = count($imgFiles['name']);

            if($totalImgFiles < 11){
                $status = $status.'<div class="PostImgs">';
                for($i = 0; $i < $totalImgFiles; $i++){
                    $imageName = $imgFiles["name"][$i];
                    $tmpName = $imgFiles["tmp_name"][$i];

                    $imageExtension = explode('.', $imageName);

                    $name = $imageExtension[0];
                    $imageExtension = strtolower(end($imageExtension));

                    $newImageName = md5($name) . " - " . uniqid(); // Generate new image name
                    $newImageName .= '.' . $imageExtension;

                    move_uploaded_file($tmpName, $_SERVER['DOCUMENT_ROOT'].'/uploads/' . $newImageName);
                    $status = $status.'<div class="PostImgI"><img src="https://zohan.fun/uploads/'.$newImageName.'"></div>';
                }
                $status = $status.'</div>';
            }
        }
		$stmt = $this->pdo->prepare("INSERT INTO statuses(statusBy, statusText, statusIdent) VALUES(:username, :status, :ident)");
		$stmt->bindValue(':username', $username);
		$stmt->bindValue(':status', $status);
        $stmt->bindValue(':ident', $ident);
		$stmt->execute();
	}

    public function checkStatus($statusIdent){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses WHERE statusIdent = :statusIdent");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function GetStatusInfo($statusIdent){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN users ON username = statusBy LEFT JOIN communities ON communityIdent = statusByCommunity WHERE statusIdent = :statusIdent");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function StatusCount($username){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses WHERE statusBy = :username AND statusByCommunity = ''");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function CommentsCount($statusIdent){
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE commentTo = :statusIdent");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->execute();
        return $stmt->rowCount();
    }

	public function StatusesBy($username, $usernameView, $limit){
		$stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN users ON username = statusBy LEFT JOIN communities ON communityIdent = statusByCommunity WHERE statusBy = :username AND statusByCommunity = '' ORDER BY statusID DESC LIMIT $limit");
		$stmt->bindValue(':username', $username);
		$stmt->execute();
		$statuses = $stmt->fetchAll(PDO::FETCH_OBJ);
		if($this->StatusCount($username) == 0){
			echo '<center>Пока что тут ничего нет...</center>';
		}
		foreach($statuses as $status){
			echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'"><img src="'.(!empty($status->statusByCommunity) ? $status->communityImage : $status->profileImage).'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'">
                    '.(!empty($status->statusByCommunity) ? $status->communityName : $status->name).'
                </a>
                '.(!empty($status->statusByCommunity) ? '<span id="coOLOGgg">(@'.$status->username.')</span>' : '').'
            </div>
            <div class="post_text">
                '.(!empty(strip_tags($status->statusText, '<br><br />')) ? strip_tags($status->statusText, '<br><br />') : '<i>Данное вложение пока что отключено</i>').'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($this->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$this->countLikes($status->statusIdent).'</span>
                </div>
                <div class="post_action global--link" data-link="/post/'.$status->statusIdent.'">
                    <i class="ion-ios7-chatboxes"></i>
                    '.$this->CommentsCount($status->statusIdent).'
                </div>
                '.($status->statusBy == $usernameView && empty($status->statusByCommunity) ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
		}
        if($this->StatusCount($username) > $limit){
            $newlimit = $limit + 16;
            echo '<center>
            <button class="button global--link" data-link="/profile/'.$username.'?limit='.$newlimit.'">Показать больше</button>
            </center>';
        }
	}

    public function PublishComment($username, $comment, $statusIdent){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $commentIdent = '';
        for ($i = 0; $i < 64; $i++) {
            $commentIdent .= $characters[random_int(0, $charactersLength - 1)];
        }

        $stmt = $this->pdo->prepare("INSERT INTO comments(commentContent, commentBy, commentTo, commentIdent) VALUES(:commentContent, :commentBy, :commentTo, :commentIdent)");

        $stmt->bindValue(':commentContent', $comment);
        $stmt->bindValue(':commentBy', $username);
        $stmt->bindValue(':commentTo', $statusIdent);
        $stmt->bindValue(':commentIdent', $commentIdent);

        $stmt->execute();
    }

    public function StatusComments($statusIdent, $limit, $username){
        $stmt = $this->pdo->prepare("SELECT * FROM comments LEFT JOIN users ON username = commentBy WHERE commentTo = :statusIdent ORDER BY commentID DESC LIMIT $limit");
        $stmt->bindValue(':statusIdent', $statusIdent);
        $stmt->execute();

        if($stmt->rowCount() == 0){
            echo '<center>Пока что тут ничего нет...</center>';
        }

        $comments = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach ($comments as $comment) {
            echo '<div class="post" id="comment-'.$comment->commentIdent.'">
        <div>
            <a href="/profile/'.$comment->username.'"><img src="'.$comment->profileImage.'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="/profile/'.$comment->username.'">
                    '.$comment->name.'
                </a>
            </div>
            <div class="post_text">
                '.strip_tags($comment->commentContent, '<br><br />').'
            </div>
            <div class="post_actions">
                '.($comment->commentBy == $username ? '
                <div class="post_action remove_comment" data-comment="'.$comment->commentIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
        }
    }

    public function StatusesByCommunityCount($communityIdent){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses WHERE statusByCommunity = :communityIdent");
        $stmt->bindValue(':communityIdent', $communityIdent);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function StatusesByCommunity($username, $communityIdent, $limit){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN communities ON communityIdent = statusByCommunity LEFT JOIN users ON username = statusBy WHERE statusByCommunity = :communityIdent ORDER BY statusID DESC LIMIT $limit");
        $stmt->bindValue(':communityIdent', $communityIdent);
        $stmt->execute();
        $statuses = $stmt->fetchAll(PDO::FETCH_OBJ);
        if($stmt->rowCount() < 0){
            echo '<center>Пока что тут ничего нет...</center>';
        }
        foreach($statuses as $status){
            echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="/community?v='.$status->communityIdent.'"><img src="'.$status->communityImage.'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="/community?v='.$status->communityIdent.'">
                    '.$status->communityName.'
                </a>
                <span id="coOLOGgg">(@'.$status->username.')</span>
            </div>
            <div class="post_text">
                '.(!empty(strip_tags($status->statusText, '<br><br />')) ? strip_tags($status->statusText, '<br><br />') : '<i>Данное вложение пока что отключено</i>').'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($this->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$this->countLikes($status->statusIdent).'</span>
                </div>
                <div class="post_action global--link" data-link="/post/'.$status->statusIdent.'">
                    <i class="ion-ios7-chatboxes"></i>
                    '.$this->CommentsCount($status->statusIdent).'
                </div>
                '.($status->statusBy == $username || $status->communityBy == $username ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
        }
        if($this->StatusesByCommunityCount($communityIdent) > $limit){
           $newlimit = $limit + 16;
            echo '<center>
            <button class="button global--link" data-link="/community?v='.$communityIdent.'&limit='.$newlimit.'">Показать больше</button>
            </center>';
        }
    }

	public function StatusesFollowed($username){
		$stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN users ON username = statusBy LEFT JOIN communities ON communityIdent = statusByCommunity LEFT JOIN followers ON followTo = username WHERE followBy = :username ORDER BY statusID DESC LIMIT 32");
		$stmt->bindValue(':username', $username);
		$stmt->execute();
		$statuses = $stmt->fetchAll(PDO::FETCH_OBJ);
		if($stmt->rowCount() == 0){
			echo '<center>Пока что тут ничего нет...</center>';
		}
		foreach($statuses as $status){
			echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'"><img src="'.(!empty($status->statusByCommunity) ? $status->communityImage : $status->profileImage).'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'">
                    '.(!empty($status->statusByCommunity) ? $status->communityName : $status->name).'
                </a>
                '.(!empty($status->statusByCommunity) ? '<span id="coOLOGgg">(@'.$status->username.')</span>' : '').'
            </div>
            <div class="post_text">
                '.(!empty(strip_tags($status->statusText, '<br><br />')) ? strip_tags($status->statusText, '<br><br />') : '<i>Данное вложение пока что отключено</i>').'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($this->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$this->countLikes($status->statusIdent).'</span>
                </div>
                <div class="post_action global--link" data-link="/post/'.$status->statusIdent.'">
                    <i class="ion-ios7-chatboxes"></i>
                    '.$this->CommentsCount($status->statusIdent).'
                </div>
                '.($status->statusBy == $username && empty($status->statusByCommunity) ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
		}
	}

    public function StatusesFollowedCommunity($username){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN users ON username = statusBy LEFT JOIN communities ON communityIdent = statusByCommunity LEFT JOIN followers ON communityFollow = communityIdent WHERE followBy = :username ORDER BY statusID DESC LIMIT 32");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $statuses = $stmt->fetchAll(PDO::FETCH_OBJ);
        if($stmt->rowCount() == 0){
            echo '<center>Пока что тут ничего нет...</center>';
        }
        foreach($statuses as $status){
            echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'"><img src="'.(!empty($status->statusByCommunity) ? $status->communityImage : $status->profileImage).'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'">
                    '.(!empty($status->statusByCommunity) ? $status->communityName : $status->name).'
                </a>
                '.(!empty($status->statusByCommunity) ? '<span id="coOLOGgg">(@'.$status->username.')</span>' : '').'
            </div>
            <div class="post_text">
                '.(!empty(strip_tags($status->statusText, '<br><br />')) ? strip_tags($status->statusText, '<br><br />') : '<i>Данное вложение пока что отключено</i>').'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($this->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$this->countLikes($status->statusIdent).'</span>
                </div>
                <div class="post_action global--link" data-link="/post/'.$status->statusIdent.'">
                    <i class="ion-ios7-chatboxes"></i>
                    '.$this->CommentsCount($status->statusIdent).'
                </div>
                '.($status->statusBy == $username && empty($status->statusByCommunity) ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
        }
    }

    public function AllStatuses($username){
        $stmt = $this->pdo->prepare("SELECT * FROM statuses LEFT JOIN communities ON communityIdent = statusByCommunity LEFT JOIN users ON username = statusBy ORDER BY statusID DESC LIMIT 32");
        $stmt->execute();
        $statuses = $stmt->fetchAll(PDO::FETCH_OBJ);
        if($stmt->rowCount() < 0){
            echo '<center>Пока что тут ничего нет...</center>';
        }
        foreach($statuses as $status){
            if($this->checkBlock($status->username, $username) === false && $this->checkBlock($username, $status->username) === false){
            echo '<div class="post" id="post-'.$status->statusIdent.'">
        <div>
            <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'"><img src="'.(!empty($status->statusByCommunity) ? $status->communityImage : $status->profileImage).'" alt="Profile Picture"></a>
        </div>
        <div>
            <div>
                <a href="'.(!empty($status->statusByCommunity) ? '/community?v='.$status->communityIdent : '/profile/'.$status->username).'">
                    '.(!empty($status->statusByCommunity) ? $status->communityName : $status->name).'
                </a>
                '.(!empty($status->statusByCommunity) ? '<span id="coOLOGgg">(@'.$status->username.')</span>' : '').'
            </div>
            <div class="post_text">
               '.(!empty(strip_tags($status->statusText, '<br><br />')) ? strip_tags($status->statusText, '<br><br />') : '<i>Данное вложение пока что отключено</i>').'
            </div>
            <div class="post_actions">
                <div class="post_action like-post '.($this->checkLike($status->statusIdent, $username) === true ? 'post_action_active' : '').'" data-status="'.$status->statusIdent.'">
                    <i class="ion-thumbsup"></i>
                    <span class="count-likes">'.$this->countLikes($status->statusIdent).'</span>
                </div>
                <div class="post_action global--link" data-link="/post/'.$status->statusIdent.'">
                    <i class="ion-ios7-chatboxes"></i>
                    '.$this->CommentsCount($status->statusIdent).'
                </div>
                '.($status->statusBy == $username && empty($status->statusByCommunity) ? '
                <div class="post_action remove_status" data-status="'.$status->statusIdent.'">
                    <i class="ion-ios7-trash"></i>
                </div>' : '').'
            </div>
        </div>
    </div>';
        }
        }
    }
}
?>