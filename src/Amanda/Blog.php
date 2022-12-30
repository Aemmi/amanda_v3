<?php 
/**
 * Blog class
 **/
namespace Src\Amanda;

use Src\Amanda\DB;

class Blog extends DB{
	public function __construct($cat=null,$postLink=null){
		parent::__construct();
		if($cat == null){
			$this->cat = "";
		} else {
			$this->cat = $cat;
		}

		if($postLink == null){
			$this->postLink = "";
		}else{
			$this->postLink = $postLink;
		}
	}

	public function all(){
		$this->sql = "SELECT * FROM blog ORDER BY id DESC";
		if($this->countRows($this->sql) > 0){
			return $this->select($this->sql);
		}else{
			return [];
		}
	}

	public function loadPosts(){
		$this->sql = "SELECT * FROM blog WHERE post_category = '$this->cat' LIMIT 9";
		if($this->countRows($this->sql) > 0){
			return $this->select($this->sql);
		}else{
			return [];
		}
	}

	public function latestPost($index){
		$this->sql = "SELECT * FROM blog WHERE post_category = '$this->cat' LIMIT $index";
		if($this->countRows($this->sql) > 0){
			return $this->select($this->sql);
		}else{
			return [];
		}
	}

	public function getPost(){
		$this->sql = "SELECT * FROM blog WHERE post_link = '$this->postLink'";
		
		return $this->select($this->sql);
		
	}

	public function getPostTitle(){
		foreach($this->select() as $rec){
			return $rec['post_title'];
		}
	}

	public function getPostDescription(){
		foreach($this->select() as $rec){
			return $rec['post_excerpt'];
		}
	}

	public function getPostContent(){
		foreach($this->select() as $rec){
			return $rec['post_content'];
		}
	}

	public function getPostKeyWords(){
		foreach($this->select() as $rec){
			return $rec['post_tags'];
		}
	}

	public function getDatePosted(){
		foreach($this->select() as $rec){
			return strftime("%b %d, %Y", strtotime($rec['post_date']));
		}
	}

	public function getTimePosted(){
		foreach($this->select() as $rec){
			return date('h:i a', strtotime($rec['post_date']));
		}
	}

	public function getPostViews(){
		foreach($this->select() as $rec){
			return $rec['views'];
		}
	}

	public function updatePostView(){
		foreach($this->select() as $rec){
			$nv = ($rec['views']+1);
			$this->update("UPDATE blog SET views = '$nv' WHERE post_link = '$this->postLink'");
		}
	}
}