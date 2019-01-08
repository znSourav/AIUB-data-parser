<?php

//User Must Include simple_dom parser//
include "simple_html_dom.php";

$count=0;
$count2=0;
$countInside=0;
$coursedone=0;
$courseCheckFlag=0;
$courseName="";
$courseFaculty="";
$courseFacultyPic="";
$courseDay1="";
$courseDay1Type="";
$courseDay1Room="";
$courseDay2="";
$courseDay2Type="";
$courseDay2Room="";
$pic="";
$course=array();


session_start();

	$_SESSION['us']=$_POST['userid'];
	$_SESSION['ps']=$_POST['password'];



function login($url,$data)
{
  $fp = fopen("cookie.txt", "w");
  fclose($fp);
  $cookie_file = 'cookie.txt';
  $login = curl_init();
  curl_setopt($login, CURLOPT_COOKIEJAR, realpath($cookie_file));
  curl_setopt($login, CURLOPT_COOKIEFILE, realpath($cookie_file));

  curl_setopt($login, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($login, CURLOPT_SSL_VERIFYPEER, true);


  curl_setopt($login, CURLOPT_TIMEOUT, 40000);
  curl_setopt($login, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($login, CURLOPT_URL, $url);
  curl_setopt($login, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  curl_setopt($login, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($login, CURLOPT_POST, TRUE);
  curl_setopt($login, CURLOPT_POSTFIELDS, $data);
  ob_start();

  $str = curl_exec($login);
	ob_end_clean();  // stop preventing output
	curl_close ($login);
	unset($login);

	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

	curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($cookie_file));
	curl_setopt($ch, CURLOPT_URL,"https://portal.aiub.edu/Student/Home/Profile");

	$buf2 = curl_exec($ch);
	ob_end_clean();  // stop preventing output
	$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);

	//CHECKING LOGIN SUCCESS:::::::::::::::::
	if($httpcode!=200)
	{
		//LOGIN FAILED::::::::::::::::::::::
		curl_close ($ch);
		unset($ch);
		echo "failed";
		exit();
	}
	else
	{
    $courseCheckFlag=0;
		curl_close ($ch);
		$dom = new simple_html_dom();
	  // Load HTML from a string
	  $dom->load($buf2);

		unset($ch);

		//STORING IMAGE:::::::::::::::::::::::
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER,1);

		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, true);

		curl_setopt($ch2, CURLOPT_COOKIEFILE, realpath($cookie_file));
		curl_setopt($ch2, CURLOPT_URL,"https://portal.aiub.edu/Common/User/ProfileImage");
		$buf3 = curl_exec ($ch2);
		curl_close ($ch2);
		unset($ch2);

		if (!file_exists($_SESSION['us'])) 
		{
    	mkdir($_SESSION['us'], 0777, true);
    	mkdir($_SESSION['us'].'/'.'uploads', 0777, true);
    	$fp = fopen($_SESSION['us'].'/'.$_SESSION['us'].".jpeg",'w');
    	fwrite($fp, $buf3);
    	fclose($fp);
		}
		else
		{
			$fp = fopen($_SESSION['us'].'/'.$_SESSION['us'].".jpeg",'w');
	    fwrite($fp, $buf3);
	    fclose($fp);
		}

		//STORING COURSE INFO:::::::::::::::::::::::::::::::

		$chx = curl_init();
		curl_setopt($chx, CURLOPT_RETURNTRANSFER,1);

		curl_setopt($chx, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($chx, CURLOPT_SSL_VERIFYPEER, true);

		curl_setopt($chx, CURLOPT_COOKIEFILE, realpath($cookie_file));
		curl_setopt($chx, CURLOPT_URL,"https://portal.aiub.edu/Student");

		$bufx = curl_exec($chx);
		curl_close ($chx);
		$domx = new simple_html_dom();
    // Load HTML from a string
    $domx->load($bufx);
    $c=0;

		foreach($domx->find('div[class=col-md-6]') as $articlex) 
		{
    	foreach($articlex->find('a') as $linkx)
    	{
    		$courseCheckFlag=1;
    		$course[$c]="https://portal.aiub.edu".$linkx->href;
    		$c++;
      }
	  }

	  if($courseCheckFlag==1)
	  {
	  	$course=array_unique($course);
	  	$coursedone=0;
	    foreach ($course as $key) 
	    {
	    	$ch2x = curl_init();
				curl_setopt($ch2x, CURLOPT_RETURNTRANSFER,1);

				curl_setopt($ch2x, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch2x, CURLOPT_SSL_VERIFYPEER, true);

				curl_setopt($ch2x, CURLOPT_COOKIEFILE, realpath($cookie_file));
				curl_setopt($ch2x, CURLOPT_URL,$key);
				//echo $key."<br>";

				$buf2x = curl_exec($ch2x);

				curl_close ($ch2x);
				$dom2x = new simple_html_dom();
	    	// Load HTML from a string
	    	$dom2x->load($buf2x);
	    	$count2=0;
	    	$countInside=0;
	    	foreach($dom2x->find('div[class=col-md-6]') as $articlex) 
				{
					foreach($articlex->find('label') as $link)
		    	{
		    		$countInside++;
	        	if($countInside==1)
	        	{
	        		$courseName=$link->plaintext;
	        	}
	        	else if($countInside==2)
	        	{
	        		$courseFaculty=$link->plaintext;
	        	}		
					}

	        foreach($articlex->find('img') as $link)
	    		{
	    			$courseFacultyPic="https://portal.aiub.edu".$link->src;
	        }

	        foreach($articlex->find('table') as $link)
	    		{
	    			$courseDay1="";
						$courseDay1Type="";
						$courseDay1Room="";
						$courseDay2="";
						$courseDay2Type="";
						$courseDay2Room="";
	    			foreach ($link->find('td') as $key) 
	    			{
	    				$count2++;
	    				if($count2==1)
	    				{
	    					$courseDay1=$key->plaintext;
	    				}
	    				else if($count2==2)
	    				{
	    					$courseDay1Type=$key->plaintext;
	    				}
	    				else if($count2==3)
	    				{
	    					$courseDay1Room=$key->plaintext;
	    				}
	    				else if($count2==4)
	    				{
	    					$courseDay2=$key->plaintext;
	    				}
	    				else if($count2==5)
	    				{
	    					$courseDay2Type=$key->plaintext;
	    				}
	    				else if($count2==6)
	    				{
	    					$courseDay2Room=$key->plaintext;
	    				}
	    			}
	        }
	    	}

	    	$courseName=trim(str_replace("&amp;", "&",$courseName));$courseFaculty=trim($courseFaculty);
	    	$courseDay1=trim($courseDay1);$courseDay1Room=trim($courseDay1Room);$courseDay1Type=trim($courseDay1Type);
	    	$courseDay2=trim($courseDay2);$courseDay2Room=trim($courseDay2Room);$courseDay2Type=trim($courseDay2Type);

	    	$con = mysqli_connect("localhost", "root", "", "demoatp");
	    	if (!$con)
	   		{
	        die("Connection Error :".mysqli_connect_error());
	    	} 
	    	else 
	    	{
	        $q1 = "SELECT * FROM course_done WHERE userId= '".$_SESSION['us']."';";
	        $result = mysqli_query($con, $q1);
	      	if (mysqli_num_rows($result) > 0) 
	      	{
	      		$coursedone=1;
	      	} 
	      	else 
	     		{
	        	$q1="INSERT INTO `courses`(`student_id`, `c_name`, `c_faculty`, `faculty_pic`, `c_day1`, `c_day1_type`, `c_day1_room`, `c_day2`, `c_day2_type`, `c_day2_room`) VALUES ('".$_SESSION['us']."','".$courseName."','".$courseFaculty."','".$courseFacultyPic."','".$courseDay1."','".$courseDay1Type."','".$courseDay1Room."','".$courseDay2."','".$courseDay2Type."','".$courseDay2Room."');";

	        	mysqli_query($con, $q1);
	     		} 	
	      }
	    }

	    if($coursedone!=1)
	    {
	    	$q1="INSERT INTO `course_done`(`userId`) VALUES ('".$_SESSION['us']."');";
	    	mysqli_query($con, $q1);	
	    }

			return $dom;	
	  }
	  else
	  {
			return $dom;	  	
	  }
	}
}     

	$data = login("https://portal.aiub.edu","UserName=".$_SESSION['us']."&Password=".$_SESSION['ps']."&CaptchaDeText=a5e235ceaa014aaf9536f4f4cebf0a04&CaptchaInputText=");

//STORE DATA IN JSON::::
	 
	//START
	foreach($data->find('td, legend ') as $link)
	{
		if($count==0)
		{
			$_SESSION['name']=$link->plaintext;
			$_SESSION['name']=trim($_SESSION['name']);
		}
		else if($count==2)
		{
			$_SESSION['id']=$link->plaintext;
			$_SESSION['id'] = trim($_SESSION['id']);
		}
		else if($count==4)
		{
			$_SESSION['cgpa']=$link->plaintext;
			$_SESSION['cgpa']=trim($_SESSION['cgpa']);
		}
		else if($count==8)
		{
			$_SESSION['prog']=$link->plaintext;
			$_SESSION['prog']=trim($_SESSION['prog']);
		}
		else if($count==10)
		{
			$_SESSION['dept']=$link->plaintext;
			$_SESSION['dept']=trim($_SESSION['dept']);
		}
		else if($count==36)
		{
			$_SESSION['email']=$link->plaintext;
			$_SESSION['email']=trim($_SESSION['email']);
		}
		else if($count==42)
		{
			$_SESSION['gender']=$link->plaintext;
			$_SESSION['gender']=trim($_SESSION['gender']);
		}
		else if($count==46)
		{
			$_SESSION['religion']=$link->plaintext;
			$_SESSION['religion']=trim($_SESSION['religion']);
		}
		else if($count==50)
		{
			$_SESSION['blood']=$link->plaintext;
			$_SESSION['blood']=trim($_SESSION['blood']);
		}
		$count++;	
	}	


	//INSERT INTO DATABASE::::::::::;
	$con = mysqli_connect("localhost", "root", "", "demoatp");
    if (!$con) 
    {
        die("Connection Error :".mysqli_connect_error());
    } 
    else 
    {
    	$q1 = "SELECT * FROM users WHERE userId='".$_SESSION['id']."';";
    	// searching in user table
    	$result = mysqli_query($con, $q1);
    	if (mysqli_num_rows($result) > 0) 
    	{
	        //IF FOUND DO NOTHING
        } 
        else
        {
        	/*$pic=getcwd();
        	$pic=str_replace('\\', '##',$pic);
        	$pic=$pic.'##'.$_SESSION['us'].'##'.$_SESSION['us'].".jpeg";*/
        	$q1 = "INSERT INTO `users`(`userId`, `userName`, `cgpa`, `program`,`department`, `email`, `gender`, `religion`, `bloodGroup`, `pic`) VALUES ('".$_SESSION['id']."','".$_SESSION['name']."','".$_SESSION['cgpa']."','".str_replace("&amp;", "&", $_SESSION['prog'])."','".str_replace("&amp;", "&", $_SESSION['dept'])."','".$_SESSION['email']."','".$_SESSION['gender']."','".$_SESSION['religion']."','".$_SESSION['blood']."','".$_SESSION['us'].'/'.$_SESSION['us'].".jpeg"."')";
        	mysqli_query($con, $q1);
        }
    }




    //MAKING JSON OF DATA:::::::
	$info[] = array('Name'=> $_SESSION['name'], 'ID'=> $_SESSION['id'] ,'CGPA'=> $_SESSION['cgpa'], 'Program'=>str_replace("&amp;", "&", $_SESSION['prog']),'Department'=>str_replace("&amp;", "&", $_SESSION['dept']),'Email'=> $_SESSION['email'],'Gender'=> $_SESSION['gender'],'Religion'=> $_SESSION['religion'],'Blood Group'=> $_SESSION['blood']);
	$fp = fopen($_SESSION['us'].'/'.$_SESSION['id'].".json", 'w');
	$filename= $_SESSION['id'].".json";
	fwrite($fp, json_encode($info));
	fclose($fp);
	//END

	//REDIRECT TO SUCCESS NODE::::::::::::::

	echo "success";
	exit();
	session_destroy();
?>