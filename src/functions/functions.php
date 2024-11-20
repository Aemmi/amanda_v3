<?php 

function cleanUrl($str)
{

    $link = trim($str);

    $link = strtolower(str_replace(" ", "-", $link));

    $link = str_replace("?", "", $link);

    $link = str_replace(".", "", $link);

    $link = str_replace("(", "", $link);
    $link = str_replace(")", "", $link);
    $link = str_replace("<", "", $link);
    $link = str_replace(">", "", $link);
    $link = str_replace("&", "", $link);
    $link = str_replace("'", "", $link);
    $link = str_replace(",", "", $link);
    $link = str_replace("/", "-", $link);
    $link = str_replace("%", "", $link);
    $link = str_replace("'s", "s", $link);
    $link = str_replace(":", "", $link);
    $link = str_replace(";", "", $link);

    return $link;
}

function input($value)
{
	if($_SERVER['REQUEST_METHOD'] === "POST"){
    	$value = trim($_POST[$value]);
	}else{
		$value = trim($_GET[$value]);
	}
    $value = addslashes($value);
    return $value;
}

function messageController()
{
    global $router;
    $mailer = new PHPMailer(true);
    
    if(isset($_POST['sendMsgBtn']))
    {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $subject = sanitize($_POST['subject']);
        $msg = sanitize($_POST['message']);
        
        $res = gCaptcha($_POST['g-recaptcha-response']);
  
        if($res['success'] == false){
            alert("You have to take the reCAPTCHA test!");
            back();
        }else{
            
            try {
                //Server settings
                //$mailer->SMTPDebug = 2;                                 // Enable verbose debug output
                $mailer->isSMTP();
                $mailer->Host = $_ENV['MAIL_HOST']; 
                $mailer->SMTPAuth = true;
                $mailer->Username = $_ENV['MAIL_USERNAME']; 
                $mailer->Password = $_ENV['MAIL_PASSWORD'];
                $mailer->SMTPSecure = 'tls';          
                $mailer->Port = $_ENV['MAIL_PORT'];
            
                //Recipients
                $mailer->setFrom($email, $name);
                $mailer->addAddress('info@yourwebsite.com', 'Site'); 
                $mailer->isHTML(true);
                $mailer->Subject = $subject;
                // $mailer->AddCC($email);
                $mailer->Body = '<div style="width:100%;float:left;text-align:center">
                        
                        '.htmlspecialchars_decode($msg).'
                            
                    </div>';
        
                if($mailer->send()){
                    
                    alert("Hello, ".$name.". Your message has been sent successfully! We will be in touch with you shortly...");
                    back();
                }
                
            } catch (Exception $e) {
                alert('Message could not be sent.');
                back();
            }

            //smtp mailer ends here
        }
    }
}
/**
* function to get site details
**/

function siteInfo()
{
	global $router;
	$sql = "SELECT * FROM homepages";
	if($router->countRows($sql) > 0){

		$data = $router->select($sql);

		foreach($data as $rec){
			$logo  = $rec['logo'];
			$title = $rec['homepage_title'];
			$site_description = $rec['homepage_description'];
            $site_keywords = $rec['homepage_keywords'];
            $email = $rec['email'];
            $phone = $rec['phone'];
            $address = $rec['address'];
            $date_m = $rec['updated_at'];
            $clients_num = $rec['clients_num'];
            $services_num = $rec['services_num'];
		}

	}else{
		$logo  = "";
		$title = "Amanda";
		$site_description = "";
        $site_keywords = "";
        $date_m = "";
        $email = "";
        $phone = "";
        $address = "";
        $clients_num = "";
        $services_num = "";
	}

	return ['logo'=>$logo,'site_title'=>$title,'description'=>$site_description,'keywords'=>$site_keywords,'email'=>$email,'phone'=>$phone,'address'=>$address,'date_m'=>$date_m,'clients_num'=>$clients_num,'services_num'=>$services_num];
}

function updateSiteViews()
{
    global $router;
    $sql = "SELECT count FROM site_views";
    $gc = $router->select($sql);
    //var_dump($gc);return;
    foreach($gc as $r){
        $cc = $r['count'];
        $nc = ($cc+1);
        $sql = "UPDATE site_views SET count = '$nc'";
        $router->update($sql);
    }
}

function siteInfoController()
{
	global $router;
	if(isset($_POST['updateSiteInfoBtn']))
	{
		$title = sanitize($_POST['site_title']);
		$logo = sanitize($_POST['logo']);
		$site_description = sanitize($_POST['site_description']);
	    $site_keywords = sanitize($_POST['site_keywords']);
	    $email = sanitize($_POST['email']);
	    $phone = str_replace(" ","",sanitize($_POST['phone']));
	    $address = sanitize($_POST['address']);
	    $clients_num = sanitize($_POST['clients_num']);
	    $services_num = sanitize($_POST['services_num']);
	    
	    $date = date("Y-m-d H:i:s");
	    
	    if($router->countRows("SELECT id FROM homepages") > 0)
	    {
	        $sql = "UPDATE homepages SET homepage_title = '$title', logo = '$logo', homepage_description = '$site_description', homepage_keywords = '$site_keywords', email='$email', phone='$phone', address='$address', updated_at = '$date', services_num = '$services_num', clients_num = '$clients_num'";
	       
	        if($router->update($sql) < 1){
	            alert("Oooops! The server is unable to handle your query at the moment...");
	        }else{
	            alert("Site info was updated successfully!");
	            // echo '<script>location.assign("siteInfo.php");</script>';
	            // exit();
	        }
	    }else{
	        $sql = "INSERT INTO homepages (homepage_title, logo, homepage_description, homepage_keywords, email, phone, address, updated_at, services_num, clients_num) VALUES ('$title', '$logo', '$site_description', '$site_keywords', '$email', '$phone', '$address', '$date', '$services_num', '$clients_num')";

	        if($router->insert($sql) < 1){
	            
	            alert("Oooops! The server is unable to handle your query at the moment...".$router->connection_error($sql));
	            
	        }else{
	            alert("Site info was updated successfully!");
	            // exit();
	        }
	    }
	}
}

/**
* function to get site pages
**/
function loadSitePages()
{
	global $router;
	$sql = "SELECT page_title, page_link FROM pages";
	$output = "";
	if($router->countRows($sql) > 0){
		$data = $router->select($sql);

		foreach($data as $rec){
			$title = $rec['page_title'];
			$link = $rec['page_link'];
			$output .= '<li><a href="./p/'.$link.'">'.$title.'</a></li>';
		}
	}else{
		$output .= null;
	}

	echo $output;
}

function url($addr)
{
	$server = explode('/', $_SERVER['REQUEST_URI']); 

	$env = $_ENV['APP_ENV'];

	if($env == 'local'){
		return $server[0]."/".$server[1]."/".$addr;
	}else{
		return $server[0]."/".$addr;
	}
}

function curAddr()
{

    if($_SERVER['QUERY_STRING']){
        $url = $_SERVER['REQUEST_URI'];
        $query_string_pos = strpos($url, '?');

        if ($query_string_pos !== false) {
          // The query string was found in the URL
          // You can add a slash before it like this:
          $url = substr_replace($url, '/', $query_string_pos,0);
          $link_array = explode('/', $url);
          return $link_array[count($link_array)-1];
        }
    }else{
    	$url = $_SERVER['REQUEST_URI'];
        $link_array = explode('/', $url);
        return $link_array[count($link_array)-1];
    }
        
}

function redirect($route)
{
	echo '<script>location.assign("'.$route.'");</script>';
	exit();
}

function back(){
	echo '<script>history.back();</script>';
	return;
}

function csrf()
{
	$rand = rand();
	$token = md5($rand);
	$_SESSION['_token'] = $token;

	echo '<input type="hidden" value="'.$token.'" id="_token" name="_token">';
}

function alert($stmt)
{
	echo '<script>alert("'.$stmt.'");</script>';
}

function authenticated()
{
	if(isset($_SESSION['user'])){
		return true;
	}else{
		return false;
	}
}

function auth(){
    if(isset($_SESSION['user'])){
	    return $_SESSION['user'][0];
    }else{
        return array();
    }
}

function getSiteViews()
{
	global $router;
	$sql = "SELECT count FROM site_views";
	if($router->countRows($sql) > 0){
		foreach($router->select($sql) as $rec){
			return $rec['count'];
		}
	}else{
		$router->insert("INSERT INTO site_views (count) VALUES ('1')");
		foreach($router->select($sql) as $rec){
			return $rec['count'];
		}
	}
}

function allAdmin()
{
	global $router;
	$sql = "SELECT * FROM users";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function userController()
{
    global $router;
    global $mailer;
    if(isset($_POST['addUserBtn']))
    {
        $email = strtolower(sanitize($_POST['email']));
        $level = sanitize($_POST['user_level']);
        
        $date = date("Y-m-d H:i:s");
        
        $sql = "SELECT email FROM users WHERE email = '$email'";
        
        //check if user exists
        if($router->countRows($sql) > 0){
            alert("User already exists!");
            back();
        }else{
        
            $chars = "12abcdefgh34567ijklmnop89qrstuvwxyz";
            $chars = str_shuffle($chars);
            $chars = md5(substr($chars,0,16).date("h:i:s"));
        
            //password
            $chars2 = "abcd12345efgh6789jkmn98765pqrsdcbavwxyz3345346546345";
            $chars2 = str_shuffle($chars2);
            $chars2 = substr($chars2,0,8);
        
            $pass = md5($chars2);
        
            $link = '<a href="https://yourwebsite.com/activate/token/'.$chars.'">https://yourwebsite.com/activate/token/'.$chars.'</a>';
    
            try {
                //Server settings
                //$mailer->SMTPDebug = 2;                                 // Enable verbose debug output
                $mailer->isSMTP();
                $mailer->Host = $_ENV['MAIL_HOST']; 
                $mailer->SMTPAuth = true;
                $mailer->Username = $_ENV['MAIL_USERNAME']; 
                $mailer->Password = $_ENV['MAIL_PASSWORD'];
                $mailer->SMTPSecure = 'tls';          
                $mailer->Port = $_ENV['MAIL_PORT'];
            
                //Recipients
                $mailer->setFrom('no-reply@yourwebsite.com', 'Scintillant Sparkle');
                $mailer->addAddress($email, 'Admin'); 
                $mailer->isHTML(true);
                $mailer->Subject = 'Invitation to join '.siteInfo()['site_title'];
                $mailer->Body = '<div style="width:100%;float:left;text-align:center">
                        <img src="https://yourwebsite.com/public/web-contents/images/'.siteInfo()['logo'].'" style="width:80px;height:50px;">
                     </div>
                     <div style="width:100%;float:left;text-align:left">
                        <h2 class="tgt-center" style="border-bottom:2px solid #2196F3">Invitation to join '.siteInfo()['site_title'].'</h2>
                            <p>This is to let you know that you have been invited to join '.siteInfo()['site_title'].' as an administrator. Click the link below or copy the link into your browser url in order to continue with your registration. Take note of the password created for you below:</p>
                            <p><b>Login Password:</p> <i>'.$chars2.'</i></p>
                            <p><b>Secret Link:</b> <i>'.$link.'</i></p>
                            
                            <p>For further enquiries, questions or suggestions, feel free to send a mail to <b><i>dev@yourwebsite.com</i></b></p>
                    </div>
                    
                    <br>
                    <br>
                    
                    <div  style="width:100%;float:left;text-align:center">
                    
                        <p>&copy; <a href="https://yourwebsite.com">'.siteInfo()['site_title'].'</a> | '.date("Y").'</p>
                            
                    </div>';
        
                if($mailer->send()){
                    //submit to db
                    $sql = "INSERT INTO users (email, password, level, created_at, activation_token) VALUES ('$email', '$pass', '$level', '$date', '$chars')";
                
                    if($router->insert($sql) < 1){
                        alert("Ooops! We are currently unable to handle your request at the moment! ".$router->connection_error($sql));
                        back();
                    }else{
                        alert("An invitation mail has been sent to the user with the address ".$email);
                    }
                }
            } catch (Exception $e) {
                alert('Message could not be sent.');
                echo 'Mailer Error: ' . $mailer->ErrorInfo;
            }
    
            //smtp mailer ends here
    
    	}
    }
    
    if(isset($_GET['aid']))
    {
        $id = sanitize($_GET['aid']);
        $sql = "DELETE FROM users WHERE id = '$id' LIMIT 1";
        if($router->delete($sql)){
            back();
        }else{
            alert("The server was unable to handle your request at the moment!");
            back();
        }
    }

}

function activateAccount($token)
{
    global $router;
    
    //check for token
    $sql = "SELECT * FROM users WHERE activation_token = '$token'";
    
    if($router->countRows($sql) > 0){
        $check = $router->select($sql);
        foreach($check as $res){
            $tbl_token = $res['activation_token'];
            $id = $res['id'];
        }
        if($token == $tbl_token){
            //unset token
            $unset = $router->update("UPDATE users SET activation_token = '', activated = '1' WHERE id = '$id' LIMIT 1");
            if($unset < 1){
                alert("Ooops! We are currently unable to process your request at the moment. Try again later!");
                redirect("/login");
            }else{
                alert("Your account has been successfully activated! You may now proceed to login!");
                redirect("/login");
            }
            
        }else{
            alert("The token provided is invalid or does not exist!");
            redirect("/login");
        }
    }else{
       
        alert("Ooops! The token provided has expired or is invalid!");
        redirect("/login");
    }
}

function allSubscribers()
{
	global $router;
	$sql = "SELECT * FROM subscriptions";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function countSubscribers()
{
	global $router;
	$sql = "SELECT id FROM subscriptions";
	if($router->countRows($sql) > 0){
		return $router->countRows($sql);
	}else{
		return 0;
	}
}

function allImages()
{
	global $router;
	$sql = "SELECT * FROM images";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function sliders()
{
	global $router;
	$sql = "SELECT * FROM slider";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function gallery()
{
	global $router;
	$sql = "SELECT * FROM galleries";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function allPages()
{
	global $router;
	$sql = "SELECT * FROM pages";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function sitePages()
{
	global $router;

	$sql = "SELECT page_title, page_link FROM pages WHERE page_status != '0' ORDER BY page_title DESC";

	$output = "";

	if($router->countRows($sql) > 0)
	{
		foreach($router->select($sql) as $rec)
		{
			$output .= '<li class="nav-item">
			<a class="nav-link" href="'.url('p/'.$rec['page_link']).'">
			'.$rec['page_title'].'
			</a></li>';
		}
	}

	echo $output;

	
}

function searchController()
{
	global $router;
	if(isset($_POST['st']))
	{
		$st = sanitize($_POST['st']);
		$query = $router->search('blog.post_title',$st);

		$output = "";

		$count = count($query);

		if($count > 0)
		{
			
	        $output .= '<div class="col-lg-12">
	            <h5>Your search "'.$st.'" returned the following ('.$count.')results:</h5>
	            <div class="blog-page-contant-start">
	                <div class="row">';

                        foreach($query as $rec){
                            $output .= '<div class="col-lg-6 col-md-6">
                                <article class="single-blog-post">
                                    <figure class="blog-thumb">
                                        <div class="blog-thumbnail">';
                                            if(empty($rec['post_thumbnail'])){
                                                $output .= '<img src="'.url('public/web-contents/images/no-image-found.jpg').'" alt="post image" class="img-fluid"/>';
                                            }else{
                                                $output .= '<img src="'.url('public/web-contents/images/'.$rec['post_thumbnail']).'" alt="'.$rec['post_thumbnail'].'" class="img-fluid" />';
                                            }
                                        $output .= '</div>
                                        <figcaption class="blog-meta clearfix">
                                            <a href="/b/'.$rec['post_link'].'" class="author">

                                                <div class="author-info">
                                                    <h5>'.$rec['post_author'].'</h5>
                                                    <p>'.strftime("%b %d, %Y", strtotime($rec['post_date'])).'</p>
                                                </div>
                                            </a>
                                        </figcaption>
                                    </figure>

                                    <div class="blog-content">
                                        <h3><a href="'.url('/b/'.$rec['post_link']).'">'.$rec['post_title'].'</a></h3>
                                        <div class="row p-3">'.htmlspecialchars_decode(substr($rec['post_excerpt'],0,255)).'...</div>
                                        <a href="'.url('b/'.$rec['post_link']).'" class="btn btn-brand">More</a>
                                    </div>
                                </article>
                            </div>';
                        }
	                $output .= '</div>
	            </div>
	        </div>';
		}
		else
		{
			echo "<h3>No match found...</h3>";
		}

		echo $output;
	}
	
}

function blogDrafts()
{
	global $router;
	$sql = "SELECT * FROM blog WHERE post_status != '1'";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function blogComments()
{
	global $router;
	$sql = "SELECT * FROM comments";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function postCategories()
{
	global $router;
	$sql = "SELECT * FROM categories ORDER BY title ASC";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function getCategoryTitle($link)
{
    global $router;
    $sql = "SELECT title FROM categories WHERE link = '$link' LIMIT 1";
    if($router->countRows($sql) > 0){
        foreach($router->select($sql) as $rec){
            return $rec['title'];
        }
    }else{
        return null;
    }
}

function blogCategory($link)
{
    global $router;
    $sql = "SELECT * FROM categories WHERE link = '$link' LIMIT 1";
    if($router->countRows($sql) > 0){
        $title = getCategoryTitle($link);
        $sql2 = "SELECT * FROM blog WHERE post_category = '$title' AND post_status = '1'";
        if($router->countRows($sql2) > 0){
            return $router->select($sql2);
        }else{
            return array();
        }
    }else{
        return array();
    }
}

function blogPosts()
{
	global $router;
	$sql = "SELECT * FROM blog WHERE post_status = '1' ORDER BY id DESC";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function postCategoryController()
{
	global $router;

	if(isset($_POST['addCategoryBtn']))
	{
		$title = sanitize($_POST['title']);
		$desc = sanitize($_POST['description']);

		$link = cleanUrl($title);
		$date = date("Y-m-d H:i:s");

		if(count($router->find('categories.title',$title)) > 0)
		{
			alert("Category already exists!");
			back();
		}

		$sql = "INSERT INTO categories (title, description, link, date_created) VALUES ('$title', '$desc', '$link', '$date')";
		if($router->insert($sql))
		{
			alert("Success!");
			back();
		}
		else
		{
			alert("The server is unable to handle your request at the moment!");
			back();
		}
	}

	if(isset($_POST['editCategoryBtn']))
	{
		$title = sanitize($_POST['title']);
		$desc = sanitize($_POST['description']);

		$id = $_POST['cid'];

		$sql = "UPDATE categories SET title='$title', description='$desc' WHERE id = '$id'";

		if($router->update($sql))
		{
			alert("Success!");
			back();
		}
		else
		{
			alert("The server is unable to handle your request at the moment!");
			back();
		}
	}
}

function blogPostController()
{
	global $router;
	if(isset($_POST['newPostBtn']))
	{
		$title = sanitize($_POST['post_title']);
    
	    $post_link = cleanUrl($title);
		
		$description = sanitize($_POST['post_excerpt']); 
		
		$category = sanitize($_POST['post_category']);
		
		$thumbnail = sanitize($_POST['thumbnail']);
		
		$author = sanitize($_POST['author']);
		
		$tag = sanitize($_POST['tag']);
		
		$body = sanitize($_POST['myTextArea']);
	    
	    $post_status = sanitize($_POST['post_status']);

	    if($post_status == "publish"){

	    	$post_status = 1;

	    }else{

	    	$post_status = 0;

	    }
	    
	    $date = date("Y-m-d H:i:s");
	    
	    if(empty($title) || empty($description) || empty($category) || empty($body))
	    {
	        alert("All fields must be filled!");
	        back();
	    }
	    else
	    {
	    	$sql = "INSERT INTO blog (post_author, post_title, post_excerpt, post_category, post_content, post_thumbnail, post_tags, post_status, post_link, author_email, post_date) VALUES ('$author', '$title', '$description', '$category', '$body', '$thumbnail', '$tag', '$post_status', '$post_link', '$email', '$date')";
	    	if($router->find('blog.post_link',$post_link))
	    	{
	    		alert("Post already exists");
	    		back();
	    	}

			if($router->insert($sql))
			{
				alert("Success!");
				back();
			}
			else
			{
				alert("The server cannot handle your request at the moment. Please try again later.");
				back();
			}
		
	    }
	}

	if(isset($_POST['editPostBtn']))
	{
		$title = sanitize($_POST['post_title']);

		$pid = $_POST['pid'];
		
		$description = sanitize($_POST['post_excerpt']); 
		
		$category = sanitize($_POST['post_category']);
		
		$thumbnail = sanitize($_POST['thumbnail']);
		
		$author = sanitize($_POST['author']);
		
		$tag = sanitize($_POST['tag']);
		
		$body = sanitize($_POST['myTextArea']);
	    
	    $post_status = sanitize($_POST['post_status']);

	    if($post_status == "publish"){

	    	$post_status = 1;

	    }else{

	    	$post_status = 0;

	    }
	    
	    $date = date("Y-m-d H:i:s");
	    
	    if(empty($title) || empty($description) || empty($category) || empty($body))
	    {
	        alert("All fields must be filled!");
	        back();
	    }
	    else
	    {
	    	$sql = "UPDATE blog SET post_author='$author', post_title='$title', post_excerpt='$description', post_category='$category', post_content='$body', post_thumbnail='$thumbnail', post_tags='$tag', post_status='$post_status', date_updated='$date' WHERE id = '$pid'";

			if($router->update($sql))
			{
				alert("Success!");
				back();
			}
			else
			{
				alert("The server cannot handle your request at the moment. Please try again later.");
				back();
			}
		
	    }
	}
	
	if(isset($_GET['pid'])){
	    $pid = $_GET['pid'];
	    $sql = "DELETE FROM blog WHERE id = '$pid' LIMIT 1";
	    if($router->delete($sql)){
	        back();
	    }else{
	        alert("The server was unable to handle your request...");
	        back();
	    }
	}
	if(isset($_GET['upid'])){
	    $pid = $_GET['upid'];
	    $sql = "UPDATE blog SET post_status = '0' WHERE id = '$pid' LIMIT 1";
	    if($router->update($sql)){
	        back();
	    }else{
	        alert("The server was unable to handle your request...");
	        back();
	    }
	}
	if(isset($_GET['pub_id'])){
	    $pid = $_GET['pub_id'];
	    $sql = "UPDATE blog SET post_status = '1' WHERE id = '$pid' LIMIT 1";
	    if($router->update($sql)){
	        back();
	    }else{
	        alert("The server was unable to handle your request...");
	        back();
	    }
	}
}

function loginController()
{
	global $router;
	if(isset($_POST['loginBtn'])){
		//handle login
		$email = strtolower(sanitize($_POST['email']));
		$pw = md5(sanitize($_POST['pw']));
		$token = $_POST['_token'];

		if($token != $_SESSION['_token']){
			alert("Invalid request!");
			redirect('login');
		}

		$sql = "SELECT * FROM users WHERE email = ?";

		$stmt = $router->con->prepare($sql);

		$stmt->bind_param('s',$email);

		$stmt->execute();

		$user = $stmt->get_result();

		if(!empty($user))
		{
			foreach($user as $rec)
			{
			    $tbl_pw = $rec['password'];
			    $activated = $rec['activated'];
			}
			
			if($activated == 1){
    			if($pw === $tbl_pw)
    			{
    				$_SESSION['user'] = $router->select("SELECT * FROM users WHERE email = '$email'");
    				redirect('dashboard');
    			}
    			else
    			{
    				alert("Either your password or email does not match. Try again!");
    				redirect('login');
    			}
			}else{
			    alert("Your account has not been activated yet. Please look into your mail inbox or spam for the activation link that was sent to you.");
    			redirect('login');
			}
		}
		else
		{
			alert("You are not authorized!");
			redirect('login');
		}
	}
    
    if(isset($_POST['forgotPwBtn']))
    {
        global $mailer;
        $email = sanitize($_POST['email']);
        $_token = sanitize($_POST['_token']);
        if($_token != $_SESSION['_token']){
			alert("Form has expired!");
			back();
		}
        if(count($router->find('users.email',$email)) > 0)
        {
            $chars = "12abcdefgh34567ij65547898978967376756klmnop89qrstuvwxyz";
            $chars = str_shuffle($chars);
            $chars = md5(substr($chars,0,16).date("h:i:s"));
        
            $link = '<a href="https://yourwebsite.com/resetpassword/token/'.$chars.'">https://yourwebsite.com/resetpassword/token/'.$chars.'</a>';
            
            //submit to db
            $sql = "UPDATE users SET reset_pw_token = '$chars' WHERE email = '$email'";
        
            if($router->update($sql) < 1){
                alert("Ooops! We are currently unable to handle your request at the moment!");
                back();
            }else{
                try {
                    //Server settings
                    //$mailer->SMTPDebug = 2;                                 // Enable verbose debug output
                    $mailer->isSMTP();
	                $mailer->Host = $_ENV['MAIL_HOST']; 
	                $mailer->SMTPAuth = true;
	                $mailer->Username = $_ENV['MAIL_USERNAME']; 
	                $mailer->Password = $_ENV['MAIL_PASSWORD'];
	                $mailer->SMTPSecure = 'tls';          
	                $mailer->Port = $_ENV['MAIL_PORT'];
                
                    //Recipients
                    $mailer->setFrom('no-reply@yourwebsite.com', 'Scintillant Sparkle');
                    $mailer->addAddress($email, 'Admin'); 
                    $mailer->isHTML(true);
                    $mailer->Subject = 'Forgot Password';
                    $mailer->Body = '<div style="width:100%;float:left;text-align:center">
                            <img src="https://yourwebsite.com/public/web-contents/images/'.siteInfo()['logo'].'" style="height:100px;">
                         </div>
                         <div style="width:100%;float:left;text-align:left">
                            <h2 class="tgt-center" style="border-bottom:2px solid #2196F3">Password Reset on yourwebsite.com</h2>
                                <p>Somone has requested to reset their account password. If this was not you, please ignore this message. But if it was you, kindly proceed with the link below:<br><br>Copy this link into a web browser or click on it from here:&nbsp;'.$link.'<br><br>N.B: Do not reply to this mail. This mail was automatically generated and sent to you for the purpose of resetting your account password. Thank you.<br><br></p>
                                
                                <p>For further enquiries, questions or suggestions, feel free to send a mail to <b><i>dev@yourwebsite.com</i></b></p>
                        </div>
                        
                        <br>
                        <br>
                        
                        <div  style="width:100%;float:left;text-align:center;background:blue;color:#fff">
                        
                            <p>&copy; <a href="https://yourwebsite.com" style="color:#fff">'.siteInfo()['site_title'] ?? 'Scintillant Sparkle Facility Management'.'</a> | '.date("Y").'</p>
                                
                        </div>';
            
                    $mailer->send();
                    
                    alert("A mail with your password reset link has been sent to ".$email." Kindly look in your SPAM if not found in Inbox.");
                    back();
                    
                } catch (Exception $e) {
                    alert('Message could not be sent.');
                    echo 'Mailer Error: ' . $mailer->ErrorInfo;
                }
            
            }
    
            //smtp mailer ends here
        }else{
            alert("Account not found!");
            back();
        }
        
    }
    
    if(isset($_POST['resetPwBtn']))
    {
        $password = sanitize($_POST['password']);
        $cpassword = sanitize($_POST['cpassword']);
        $token = sanitize($_POST['token']);
        // $_csrf = sanitize($_POST['_token']);
        
//         if($_SESSION['_token'] != $_csrf){
// 			alert("Form expired!");
// 			alert($_SESSION['_token'] .'!='. $_csrf);
// 			back();
// 		}
        
        if($password != $cpassword){
            alert("Your password does not match! Check your inputs!");
            back();
        }
        
        if(strlen($password) < 8){
            alert("Your password should not be less than 8 characters!");
            back();
        }
        
        $new_pass = md5($password);
        
        $records = $router->find('users.reset_pw_token',$token);
        // var_dump($records);
        if(count($records)){
            foreach($records as $rec){
                $id = $rec['id'];
            }
            //submit to db
            $sql = "UPDATE users SET reset_pw_token = '', password = '$new_pass' WHERE id = '$id' LIMIT 1";
        
            if($router->update($sql) < 1){
                alert("Ooops! We are currently unable to handle your request at the moment!");
                back();
            }else{
                alert("Your password reset was successful!");
                redirect('/login');
            }
        }else{
            alert("Either the token has expired or does not exist...");
            back();
        }
        
    }
}

function servicesController()
{
    global $router;

	if(isset($_POST['addNewServiceBtn']))
	{
		$title = sanitize($_POST['title']);
    
	    $link = cleanUrl($title);

	    if(count($router->find('services.service_link',$link))>0)
	    {
	    	alert("Service already exists!");
	    	back();
	    }
	    
	    $description = sanitize($_POST['description']); 
		
		$thumbnail = sanitize($_POST['thumbnail']);
		
		$keywords = sanitize($_POST['keywords']);
		
		$content = sanitize($_POST['content']);
		
		$status = sanitize($_POST['status']);
		
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "INSERT INTO services 
		(description, content, keywords, thumbnail, created_at, title, service_link)
		VALUES 
		('$description', '$content', '$keywords', '$thumbnail', '$date', '$title', '$link')";
		
		if($router->insert($sql) < 1) {
	       // alert("The server was unable to handle your request at the moment!");
	        $error = $router->connection_error($sql);
	        alert($error);
	       // back();
		}else{
		    alert('Service was created successfully!');
		    back();
		}
	}
	
	if(isset($_GET['deleteService']))
	{
	    $id = sanitize($_GET['deleteService']);
		$sql = "DELETE FROM services WHERE id = '$id' LIMIT 1";
    	$router->delete($sql);
    	back();
	}

	if(isset($_POST['editServiceBtn']))
	{
		$title = sanitize($_POST['title']);
	    
	    $description = sanitize($_POST['description']); 
		
		$thumbnail = sanitize($_POST['thumbnail']);
		
		$keywords = sanitize($_POST['keywords']);
		
		$content = sanitize($_POST['content']);

		$pid = sanitize($_POST['pid']);
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "UPDATE services SET description='$description', content='$content', keywords='$keywords', thumbnail='$thumbnail', updated_at='$date', title='$title' WHERE id = '$pid'";
		
		if($router->update($sql) < 1) {
	        alert("The server was unable to handle your request at the moment!");
	        back();
		}else{
		    alert('Page was edited successfully!');
		    back();
		}
	}
}

function getServices()
{
    global $router;
	$sql = "SELECT * FROM services";
	if($router->countRows($sql) > 0){
		return $router->select($sql);
	}else{
		return array();
	}
}

function pageController()
{
	global $router;

	if(isset($_POST['addNewPageBtn']))
	{
		$title = sanitize($_POST['title']);
    
	    $link = cleanUrl($title);

	    if(count($router->find('pages.page_link',$link))>0)
	    {
	    	alert("Page already exists!");
	    	back();
	    	return;
	    }
	    
	    $description = sanitize($_POST['description']); 
		
		$thumbnail = sanitize($_POST['thumbnail']);
		
		$keywords = sanitize($_POST['page_keywords']);
		
		$content = sanitize($_POST['content']);
		
		$status = sanitize($_POST['status']);
		
		if($status === "publish"){
		    $status = 1;
		}else{
		    $status = 0;
		}
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "INSERT INTO pages 
		(page_description, page_content, page_keywords, page_thumbnail, created_at, page_title, page_link, page_status)
		VALUES 
		('$description', '$content', '$keywords', '$thumbnail', '$date', '$title', '$link', '$status')";
		
		if($router->insert($sql) < 1) {
	       // alert("The server was unable to handle your request at the moment!");
	        $error = $router->connection_error($sql);
	        alert($error);
	       // back();
		}else{
		    alert('Page was created successfully!');
		    back();
		}
	}
	
	if(isset($_GET['deletePage']))
	{
	    $id = sanitize($_GET['deletePage']);
		$sql = "DELETE FROM pages WHERE id = '$id' LIMIT 1";
    	$router->delete($sql);
    	back();
	}

	if(isset($_POST['editPageBtn']))
	{
		$title = sanitize($_POST['title']);
	    
	    $description = sanitize($_POST['description']); 
		
		$thumbnail = sanitize($_POST['thumbnail']);
		
		$keywords = sanitize($_POST['page_keywords']);
		
		$content = sanitize($_POST['content']);
		
		$status = sanitize($_POST['status']);

		$pid = sanitize($_POST['pid']);
		
		if($status === "publish"){
		    $status = 1;
		}else{
		    $status = 0;
		}
		
		$date = date("Y-m-d H:i:s");
		
		$sql = "UPDATE pages SET page_description='$description', page_content='$content', page_keywords='$keywords', page_thumbnail='$thumbnail', updated_at='$date', page_title='$title', page_status='$status' WHERE id = '$pid'";
		
		if($router->update($sql) < 1) {
	        alert("The server was unable to handle your request at the moment!");
	        back();
		}else{
		    alert('Page was edited successfully!');
		    back();
		}
	}

}

function profileController()
{
    global $router;
    
    if(isset($_POST['changeImgBtn']))
    {
        $id = auth('user')[0]['id'];
        $rand = rand();
        $link = auth('user')[0]['display_name'].'_'.$rand;
        //handle image upload
        $directory = "./public/web-contents/images/";

	    if(!isset($_FILES["pic"]["tmp_name"])){
	    
	    $upload = 0;
	    	alert('No image selected!!!');
	    	exit();
	    }
	    
	    $upload = 1;

	    $opic = $directory.basename($_FILES["pic"]["name"]);
	    
	    $type = pathinfo($opic, PATHINFO_EXTENSION);

	    $link = $link.".".$type;

	    $pic = $directory.$link;
	    
	    $check = getimagesize($_FILES["pic"]["tmp_name"]);
	    
	    if($check !== false){
	    	
	    	$upload = 1;
	    
	    }else{
	    
	    	alert("File is not an image!!!"); 
	    	$upload = 0;
	    	exit();
	    }
	    if(file_exists($pic)){
	    
	    	alert("Image already exists!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($_FILES["pic"]["size"] > 20000000){
	    
	    	alert("Image is more than 2mb!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($type != "jpg" && $type != "png" && $type != "jpeg" && $type != "gif"){
	    
	    	alert("Sorry, only JPG, JPEG, PNG and GIF images are allowed!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($upload ===0){
	    
	    	alert("Sorry. Your image was not uploaded!!!");
	    	exit();
	    
	    }else{
	        //$name = htmlspecialchars($_POST['name']);
	    
	    	if(move_uploaded_file($_FILES["pic"]["tmp_name"], $pic)){
	        	$sql = "UPDATE users SET image_link = '$link' WHERE id = '$id' LIMIT 1";
	        	if($router->insert($sql))
	        	{
	        	    unlink($directory.auth('user')[0]['user_img']);
	        	    $_SESSION['user'] = $router->find('users.id',$id);
	        	    alert("Image was uploaded successfully!");
	                back();
	        	}
	        }else{
	    
	    		alert("Image was not uploaded!!!");
	    		exit();
		    }
	    }
    }
    
    if(isset($_POST['saveProfileBtn']))
    {
        $fname = sanitize($_POST['name']);
        $gender = sanitize($_POST['gender']);
        $email = sanitize($_POST['email']);
        $tw_url = sanitize($_POST['tw_url']);
        $fb_url = sanitize($_POST['fb_url']);
        $bio = sanitize($_POST['bio']);
        $phone = sanitize($_POST['phone']);
        
        $sql = "UPDATE users SET name = '$fname', gender = '$gender', phone = '$phone', fb_url = '$fb_url', tw_url = '$tw_url', bio = '$bio' WHERE email = '$email'";
        
        if($router->update($sql) < 1){
            alert("Ooops! We are currently unable to handle your request at the moment. Try again later!");
            back();
        }else{
            $_SESSION['user'] = $router->select("SELECT * FROM users WHERE email = '$email'");
            alert("Your profile was updated successfully!");
            back();
        }
    }
}

function imageController()
{
	global $router;

	if(isset($_POST['uploadImgBtn']))
	{
		$title = sanitize($_POST['title']);
		$desc = sanitize($_POST['description']);

		$link = cleanUrl($title);

		$date = date("Y-m-d");

		$directory = "./public/web-contents/images/";

	    if(!isset($_FILES["pic"]["tmp_name"])){
	    
	    $upload = 0;
	    	alert('No image selected!!!');
	    	exit();
	    }
	    
	    $upload = 1;

	    $opic = $directory.basename($_FILES["pic"]["name"]);
	    
	    $type = pathinfo($opic, PATHINFO_EXTENSION);

	    $link = $link.".".$type;

	    $pic = $directory.$link;
	    
	    $check = getimagesize($_FILES["pic"]["tmp_name"]);
	    
	    if($check !== false){
	    	
	    	$upload = 1;
	    
	    }else{
	    
	    	alert("File is not an image!!!"); 
	    	$upload = 0;
	    	exit();
	    }
	    if(file_exists($pic)){
	    
	    	alert("Image already exists!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($_FILES["pic"]["size"] > 20000000){
	    
	    	alert("Image is more than 2mb!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($type != "jpg" && $type != "png" && $type != "jpeg" && $type != "gif"){
	    
	    	alert("Sorry, only JPG, JPEG, PNG and GIF images are allowed!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($upload ===0){
	    
	    	alert("Sorry. Your image was not uploaded!!!");
	    	exit();
	    
	    }else{
	        //$name = htmlspecialchars($_POST['name']);
	    
	    	if(move_uploaded_file($_FILES["pic"]["tmp_name"], $pic)){
	        	$sql = "INSERT INTO images 
	        	(title,description,created_at,link)
	        	VALUES 
	        	('$title','$desc','$date','$link')";
	        	if($router->countRows("SELECT id FROM images WHERE title = '$title'") > 0){
	        		alert("Image already exists!");
	        		back();
	        	}else{
	        		$router->insert($sql);
	        	}
	            alert("Image was uploaded successfully!");
	            back();
	    
	    	}else{
	    
	    		alert("Image was not uploaded!!!");
	    		exit();
		    }
	    }
	}

	if(isset($_GET['action']) && $_GET['action'] == 'deleteImg')
	{
		$link = sanitize($_GET['link']);
		$sql = "DELETE FROM images WHERE link = '$link' LIMIT 1";
		$p_link = "./public/web-contents/images/".$link;
    	unlink($p_link); 
    	$router->delete($sql);
    	back();
	}
}

function sliderController()
{
	global $router;

	if(isset($_POST['addSliderBtn']))
	{
		$img = sanitize($_POST['img']);

		$sql = "INSERT INTO slider (img) VALUES ('$img')";

		$router->insert($sql);

		back();
	}

	if(isset($_GET['remove']))
	{
		$link = $_GET['remove'];
		$sql = "DELETE FROM slider WHERE img = '$link' LIMIT 1";
		$router->delete($sql);
		back();
	}
}

function galleryController()
{
	global $router;

	if(isset($_POST['addGalleryBtn']))
	{
		$img = sanitize($_POST['img']);

		$details = $router->select("SELECT title, description FROM images WHERE link = '$img'");

		foreach($details as $rec){
			$desc = $rec['description'];
			$title = $rec['title'];
		}

		$date = date("Y-m-d");

		$sql = "INSERT INTO galleries (img, title, description, created_at) VALUES ('$img','$title','$desc','$date')";

		$router->insert($sql);

		back();
	}

	if(isset($_GET['remove']))
	{
		$link = $_GET['remove'];
		$sql = "DELETE FROM galleries WHERE img = '$link' LIMIT 1";
		$router->delete($sql);
		back();
	}
}

function newsLetterSub()
{
    global $router;

    if(isset($_POST['subscribeBtn'])){

        $email = sanitize($_POST['email']);

        $date = date("Y-m-d H:i:s");

        //check if email already subscribed
        
        $sql = "SELECT email FROM subscriptions WHERE email = '$email'";

        $check = $router->select($sql);
        
        if($router->countRows($sql) > 0){

            echo '<script>alert("Sorry, the email you provided has already been subscribed!\nYou can use a different email...");history.back();</script>';

        }else{

            $data = $router->insert("INSERT INTO subscriptions (email, updated_at) VALUES ('$email', '$date')");

            if($data < 1){

                echo '<script>alert("Subscription failed. Try again later!");history.back();</script>';

            }else{

               alert("Your newsletter subscription with the following email ".$email." was successful");
               back();

            }
        }
    }
}

function testimonialController()
{
    global $router;
    
    if(isset($_POST['submitTestimonialBtn']))
	{
		$fname = sanitize($_POST['fname']);
		$testimonial = sanitize($_POST['testimonial']);

		$date = date("Y-m-d");
		
		$link = cleanUrl($fname);

		$directory = "./public/web-contents/images/testimonials/";

	    if(!isset($_FILES["image"]["tmp_name"])){
	    
	    $upload = 0;
	    	alert('No image selected!!!');
	    	back();
	    	exit();
	    }
	    
	    $upload = 1;

	    $opic = $directory.basename($_FILES["image"]["name"]);
	    
	    $type = pathinfo($opic, PATHINFO_EXTENSION);

	    $link = $link.".".$type;

	    $pic = $directory.$link;
	    
	    $check = getimagesize($_FILES["image"]["tmp_name"]);
	    
	    if($check !== false){
	    	
	    	$upload = 1;
	    
	    }else{
	    
	    	alert("File is not an image!!!"); 
	    	$upload = 0;
	    	exit();
	    }
	    if(file_exists($pic)){
	    
	    	alert("Image already exists!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($_FILES["image"]["size"] > 20000000){
	    
	    	alert("Image is more than 2mb!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($type != "jpg" && $type != "png" && $type != "jpeg" && $type != "gif"){
	    
	    	alert("Sorry, only JPG, JPEG, PNG and GIF images are allowed!!!");
	    	$upload = 0;
	    	exit();
	    
	    }
	    if($upload ===0){
	    
	    	alert("Sorry. Your image was not uploaded!!!");
	    	exit();
	    
	    }else{
	        //$name = htmlspecialchars($_POST['name']);
	    
	    	if(move_uploaded_file($_FILES["image"]["tmp_name"], $pic)){
	        	$date = date("Y-m-d H:i:s");
                
                $sql = "INSERT INTO testimonials (fname, testimonial, image, date_submitted) VALUES ('$fname', '$testimonial', '$link', '$date')";
            
                if($router->insert($sql) < 1){
                    //$db->connection_error($sql);
                    alert("Oooops! Something is not right. Try again!");
                    back();
                }else{
                    alert("Testimonial was submitted successfully!");
                    back();
                }
	    
	    	}else{
	    
	    		alert("The server was unable to handle your request at the moment.");
	    		back();
		    }
	    }
	}
    
    if(isset($_GET['tid'])){
        $tid = sanitize($_GET['tid']);
        $sql = "DELETE FROM testimonials WHERE id = '$tid'";
        if($router->delete($sql) > 0){
            alert("Testimonial was deleted successfully!");
            back();
            //exit();
        }
    }
    
}

function faqsController()
{
    global $router;
    
    if(isset($_POST['updateFAQBtn'])){
        
        $q = sanitize($_POST['q']);
        $ans = sanitize($_POST['ans']);
        $id = $_POST['id'];
            
        //submit to db
        $sql = "UPDATE faqs SET faq='$q', ans='$ans' WHERE id='$id'";
        
        $insert = $router->update($sql);
        
        if($insert < 1){
            alert("Ooops! We are currently unable to handle your request at the moment!");
        }else{
        
            alert("FAQ was edited successfully!");
        }
    
    }
    
    if(isset($_GET['delete_faq'])){
        
        $id = $_GET['delete_faq'];
        
        $sql = "DELETE FROM faqs WHERE id = '$id'";
        $d = $router->delete($sql);
        
        if($d < 1){
            alert("Ooops! We are currently unable to handle your request! Try again later!");
        }else{
            alert("FAQ was deleted successfully!");
            back();
        }
    }
    
    if(isset($_POST['saveFAQBtn'])){
        	        
        $q = sanitize($_POST['q']);
        $ans = sanitize($_POST['ans']);
        
        $date = date("Y-m-d");
            
        //submit to db
        $sql = "INSERT INTO faqs (faq, ans, date_added) 
        VALUES 
        ('$q', '$ans', '$date')";
        
        $insert = $router->insert($sql);
        
        if($insert < 1){
            alert("Ooops! We are currently unable to handle your request at the moment!");
        }else{
        
            alert("FAQ was added successfully!");
        }
    
    }
    
}

function clientController()
{
    global $router;

	if(isset($_POST['addClientBtn']))
	{
		$img = sanitize($_POST['img']);

		$details = $router->select("SELECT title, description FROM images WHERE link = '$img'");

		foreach($details as $rec){
			$desc = $rec['description'];
			$title = $rec['title'];
		}

		$date = date("Y-m-d");

		$sql = "INSERT INTO clients (img, title, description, created_at) VALUES ('$img','$title','$desc','$date')";

		$router->insert($sql);

		back();
	}

	if(isset($_GET['remove']))
	{
		$link = $_GET['remove'];
// 		unlink('./public/web-contents/images/'.$link);
		$sql = "DELETE FROM clients WHERE img = '$link' LIMIT 1";
		$router->delete($sql);
		back();
	}
}


function gCaptcha($user_response){
    $fileds_string = '';
    $fields = array(
        'secret' => 'your-secret-key',
        'response' => $user_response
        );
    
    foreach($fields as $key => $value)
    $fields_string.= $key . "=" . $value . '&';
    $fields_string = rtrim($fields_string, '&');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($result, true);
}

/**
*controllers
**/ 
function accountController()
{
	$response['status'] = "Failed";
	$response['message'] = "Email is ".$router->input('loginId');

	echo json_encode($response);
}