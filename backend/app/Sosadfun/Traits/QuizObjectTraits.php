<?php
namespace App\Sosadfun\Traits;


use DB;
use Cache;
use App\Models\Quiz;
use App\Models\QuizOption;
use Illuminate\Database\Eloquent\Collection;

trait QuizObjectTraits{
    use DelayCountModelTraits;

    private function delay_count($model_class, $key, $attribute_name, $value){
        return $this->delay_modify_attribute_for_model($model_class, $key, $attribute_name, $value);
    }

    public static function random_quizzes($level=-1, $quizType='', $number=5)
    {
        return Cache::remember('random_quizzes'.'|level:'.$level.'|type:'.$quizType.'|number:'.$number, 3, function () use ($level, $quizType, $number) {
            $quizzes = Quiz::withQuizLevel($level)
            ->withQuizType($quizType)
            ->isOnline()
            ->inRandomOrder()
            ->take($number)->get();
            if (in_array($quizType, config('constants.quiz_has_option'))) {
                $quizzes->load('quiz_options');
            }
            return $quizzes;
        });
    }

    public static function all_quiz_answers()
    {
        return Cache::remember('all_quiz_answers', 20, function() {
            return DB::table('quiz_options')->select('id', 'quiz_id', 'is_correct')->get();
        });
    }

    public static function find_quiz_set($quiz_id)
    {
        return Cache::remember('quiz-'.$quiz_id, 20, function() use($quiz_id) {
            $quiz = Quiz::with('quiz_options')->find($quiz_id);
            return $quiz;
        });
    }

    /**
     * Check whether submitted quiz is passed or not.
     * @param mixed $quizzes an array with quizzes' id and user's answer
     * @param mixed $recorded_questions question_ids recorded that should be answered
     * @param int $number_to_pass How many questions have to be correct
     * @return bool true if passed, false if not passed
     */
    public static function check_quiz_passed_or_not($quizzes, $recorded_questions ,int $number_to_pass)
    {
        // 开始核对题目和答案
        $correct_quiz_number = 0;
        $submitted_quiz_ids = [];
        if (!$quizzes || !is_array($quizzes)) {
            abort(422, '请求数据格式有误。');
        }
        if (!$recorded_questions) {
            abort(444, '回答的题目和数据库中应该回答的题不符合。');
        }

        $counter = [
            "quiz_count" => [],
            "correct_count" => [],
            "select_count" => []
        ];
        $expected_quiz_ids = array_map('intval', explode(',', $recorded_questions));
        sort($expected_quiz_ids);
        $selected_qna = Quiz::with('quiz_options')->findOrFail($expected_quiz_ids)->keyBy('id');
        foreach ($quizzes as $quiz) {
            if (!is_array($quiz) || !array_key_exists('id', $quiz) || !array_key_exists('answer', $quiz) || !is_int($quiz['id']) || !$quiz['answer']) {
                abort(422, '请求数据格式有误。');
            }
            $submitted_quiz_ids[] = $quiz['id'];
            $correct_quiz_number += self::is_answer_correct($quiz['id'],$quiz['answer'],$counter,$selected_qna);
        }

        // 检查答的题目是不是数据库中记录的题目
        sort($submitted_quiz_ids);
        if ($expected_quiz_ids != $submitted_quiz_ids) {
            abort(444, '回答的题目和数据库中应该回答的题不符合。');
        }

        self::perform_counter($counter);
        return $correct_quiz_number >= $number_to_pass;
    }

    /**
     * @param int $id The quiz_id
     * @param string $answer The answer user submitted
     * @param array $counter The counter
     * @param Collection $qna The question-and-answer collections. [1=>['id'=>1,'type'=>'register',...],2=>...]
     * @return bool Whether the answer is correct or not
     */
    public static function is_answer_correct(int $id, string $answer, array &$counter, Collection &$qna) {
        if (!$qna->has($id)) {
            abort(444, '请求数据有误。');
        }
        $quiz = $qna->get($id);
        $possible_answers = $quiz->quiz_options;
        $correct_answers = $possible_answers->where('is_correct',true)->pluck('id')->toArray();
        self::update_counter($counter, 'quiz_count', $id);
        $user_answers = array_map('intval', explode(',', $answer));
        sort($correct_answers);
        sort($user_answers);

        // 如果用户的选项存在不是本题的所有选项的话
        if (!empty(array_diff($user_answers,$possible_answers->pluck('id')->toArray()))) {
            abort(422, '请求数据有误。');
        }
        // 统计每一个选项被选择的次数
        foreach ($user_answers as $user_answer) {
            if ($user_answer <= 0) {
                abort(422, '请求数据格式有误。');
            }
            self::update_counter($counter, 'select_count', $user_answer);
        }
        if ($correct_answers == $user_answers) {
            self::update_counter($counter, 'correct_count', $id);
            return true;
        }
        return false;
    }

    /**
     * @param array $counter The counter
     * @param string $type 'quiz_count', 'correct_count' or 'select_count'
     * @param int $id quiz_id or option_id
     */
    public static function update_counter(array &$counter, string $type, int $id) {
        if (array_key_exists($id,$counter[$type])) {
            $counter[$type][$id] += 1;
        } else {
            $counter[$type][$id] = 1;
        }
    }

    /**
     * @param array $counter The counter
     */
    public static function perform_counter(array &$counter) {
        foreach ($counter['quiz_count'] as $id => $value) {
            (new self)->delay_count('App\Models\Quiz', $id, 'quiz_count', $value);
        }
        foreach ($counter['correct_count'] as $id => $value) {
            (new self)->delay_count('App\Models\Quiz', $id, 'correct_count', $value);
        }
        foreach ($counter['select_count'] as $id => $value) {
            (new self)->delay_count('App\Models\QuizOption', $id, 'select_count', $value);
        }
    }

    public static function save_quiz($quiz, $orig_quiz = null) {
        // 先检查有没有至少一个正确选项
        if (!array_key_exists('option', $quiz)) {
            $quiz['option'] = [];
        }
        $has_correct_answer = false;
        foreach ($quiz['option'] as $option) {
            if (array_key_exists('is_correct', $option) && $option['is_correct']) {
                $has_correct_answer = true;
            }
        }
        if (!$has_correct_answer && in_array($quiz['type'], config('constants.quiz_has_option'))) {
            return null;
        }
        $quiz_data['body'] = $quiz['body'];
        $quiz_data['quiz_level'] = $quiz['level'] ?? -1;
        $quiz_data['hint'] = $quiz['hint'] ?? null;
        $quiz_data['type'] = $quiz['type'];
        $quiz_data['is_online'] = $quiz['is_online'] ?? true;
        $new_quiz = null;
        if (!$orig_quiz) {
            $new_quiz = Quiz::create($quiz_data);
        } else {
            $orig_quiz->update($quiz_data);
            $new_quiz = $orig_quiz->refresh();
            $orig_quiz->quiz_options()->delete();
        }
        $quiz_options = [];
        foreach ($quiz['option'] as $index => $option) {
            $option_data = [
                'quiz_id' => $new_quiz->id,
                'body' => $option['body'],
                'explanation' => $option['explanation'],
                'is_correct' => $option['is_correct'] ?? false
            ];
            $quiz_options[] = QuizOption::create($option_data);
        }
        $new_quiz['quiz_options'] = $quiz_options;
        return $new_quiz;
    }

}
