<?php
namespace Login\Controller;
use Think\Controller;
use Login\Model\LoginModel as LoginModel;

class LoginController extends Controller {

	## 验证用户名密码
	public function varify($userName, $password)
	{
		$userName = I('post.userName','','string');
		$password = I('post.password','','string');
		$login = new LoginModel();
		$result = $login->loginVarify($userName, $password);
		// var_dump($result);
		//if($result === true)
		if(is_array($result))
		{
			$user_id = $result[0]['id'];

			$expire = 3600;
			$this->loginFlow($userName, 1);
			session('userName',$userName);
			session('userId', $user_id);
			cookie('userName',$userName,$expire);
			cookie('password',$password,$expire);

			//获取用户权限列表
			$rbac = new \Org\Util\Rbac;	//导入权限验证类
			$_SESSION[C('USER_AUTH_KEY')] = $user_id;
			//获取用户的权限
			$rbac->saveAccessList($user_id);

			$this->ajaxReturn(array('code'=>1, 'message'=>'登录成功'));
		}
		elseif($result === -1)
		{
			$this->loginFlow($userName, -1);
			$this->ajaxReturn(array('code'=>-1, 'message'=>'密码错误'));
		}
		elseif($result === -2)
		{
			$this->loginFlow($userName, -2);
			$this->ajaxReturn(array('code'=>-2, 'message'=>'未注册', 'data'=>array()));
		}
			
	}

	## 登录流水
	private function loginFlow($name, $status)
	{
		$time = date('Y-m-d H:i:s', time());
		$stat = M('t_login_flow', '', 'DB_ADMIN');
		$stat->add(array('user_name'=>$name, 'login_time'=>$time, 'login_status'=>$status));
	}

	## 注册 添加用户信息
	public function regist($name, $password)
	{
		$name = I('post.name','','string');
		$password = I('post.password','','string');
		if(empty($name)||empty($password)){
			$this->ajaxReturn(array('code'=>-3, 'message'=>'用户名和密码不能为空'));
		}
		$stat = M('t_user', '', 'DB_ADMIN');
		
		$login = new LoginModel();
		$isReg = $login->isRegVarify($name);
		if($isReg){
			$this->ajaxReturn(array('code'=>-2, 'message'=>'用户已存在'));
		}
		$re = $stat->add(array('user_name'=>$name, 'user_psw'=>$password));
		if($re)
			$this->ajaxReturn(array('code'=>1, 'message'=>''));
		else
			$this->ajaxReturn(array('code'=>-1, 'message'=>'注册失败'));
	}

	public function sessionVarify(){
		$userName = cookie('userName');
		$password = cookie('password');

		if(empty($userName)||empty($password)||session('userName')!=$userName){
			cookie(null);
			session(null); 
			$this->ajaxReturn(array('code'=>-1, 'message'=>'登录验证失败')); 
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'登录验证成功')); 
		}
	}

	
}
