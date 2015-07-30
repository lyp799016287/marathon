<?php
namespace Survey\Model;
use Think\Model;

class FeedbackModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_survey';

    // public function _initialize()
    // {
    //     $this->imed_config = C('DB_IMED');
    // }

    ## 获取问题的类型
    public function getQuestionType($id)
    {
        $sql = "SELECT `answer_type` FROM t_survey_question WHERE id = " . $id;
        $result = $this->query($sql);
        return $result;
    }

}
