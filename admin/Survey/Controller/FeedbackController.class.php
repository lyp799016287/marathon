<?php
namespace Survey\Controller;
use Think\Controller;
use Survey\Model\FeedbackModel as FeedbackModel;

class FeedbackController extends Controller {

	public function _initialize(){
		$this->survey = D('Feedback');
	}

	## 传过来的结果进行拆分
	public function departResult()
	{
		$param = I('result');
		var_dump($param);
		// $param = '{"user_id": "1561895685", "survey_id":1, "feedback":[{"question_id":1, "result":"2","addition":""},{"question_id":6,"result":"26,28","addition":""}]}';

		$result = json_decode($param, true);
		// var_dump($result); exit;
		$user_id = $result['user_id']; ## 字符串
		$survey_id = intval($result['survey_id']);
		$feedback = $result['feedback'];
		for($i = 0; $i < count($feedback); $i++)
		{
			$question_id = intval($feedback[$i]['question_id']);
			$optionList = $feedback[$i]['result'];
			$addition = '';
			if(isset($feedback[$i]['addition'])) ## 单选 多选时，有该参数
				$addition = $feedback[$i]['addition'];
			$q_type = $this->survey->getQuestionType($question_id);
			if(empty($q_type))
				$this->ajaxReturn(array('code'=>-2, 'message'=>'查询失败'));
			$type = $q_type[0]['answer_type']; ## 题目的类型
			$result = array();
			if($type == 1)
			{
				$option = intval($optionList);
				$result = $this->singleClosed($user_id, $survey_id, $question_id, $option, $addition);
			}
			elseif($type == 2)
			{
				$option_ary = $this->split_option($optionList);
				for($i = 0; $i < count($option_ary); $i++)
				{
					$option = $option_ary[$i];
					$result = $this->singleClosed($user_id, $survey_id, $question_id, $option, $addition);
					if(empty($result))
						$this->ajaxReturn(array('code'=>-1, 'message'=>'提交失败'));
				}
			}
			elseif($type == 3)
			{
				$result = $this->openTask($user_id, $survey_id, $question_id, $optionList);
			}
			
			if(empty($result))
				$this->ajaxReturn(array('code'=>-1, 'message'=>'提交失败'));
		}

		$this->ajaxReturn(array('code'=>1, 'message'=>'提交成功'));

	}

	private function singleClosed($user_id, $survey_id, $question_id, $option, $addition)
	{
		// var_dump($user_id);
		// var_dump($survey_id);
		// var_dump($question_id);
		// var_dump($option);
		// var_dump($addition);
		$closed = M('t_survey_closed_result', '', 'DB_IMED');
		$result = $closed->add(array('user_id'=>$user_id, 'survey_id'=>$survey_id, 'survey_question_id'=>$question_id, 'survey_question_option_id'=>$option, 'addition'=>$addition));
		return $result;
	}

	private function split_option($optionList)
	{
		$option_ary = explode(",", $optionList);
		$return_ary = array();
		for($i = 0; $i < count($option_ary); $i++)
			$return_ary[] = intval(trim($option_ary[$i]));
		return $return_ary;
	}

	private function openTask($user_id, $survey_id, $question_id, $answer)
	{
		$open = M('t_survey_open_result', '', 'DB_IMED');
		$result = $open->add(array('user_id'=>$user_id, 'survey_id'=>$survey_id, 'survey_question_id'=>$question_id, 'answer'=>$answer));
		return $result;
	}

	// ## 提交封闭问题答案
	// public function submitClosed()
	// {
	// 	$survey_id = I('survey_id', 0, 'intval');
	// 	$question_id = I('question_id', 0, 'intval');
	// 	$option_id = I('option_id', 0, 'intval');
	// 	$user_id = I('user_id'); ## string类型
	// 	$addition = I('addition', '');
	// 	if($user_id == '')
	// 		$this->ajaxReturn(array('code'=>-99, 'message'=>'输入参数错误'));
	// 	$result = $this->survey->closeSubmit($survey_id, $question_id, $option_id, $user_id, $addition);
	// 	if($result === false)
	// 		$this->ajaxReturn(array('code'=>-1, 'message'=>'提交封闭问题答案失败'));
	// 	else
	// 		$this->ajaxReturn(array('code'=>1, 'message'=>'提交成功'));
	// }
	
}
