<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dispatch extends CI_Controller {
    const FLAG_DAILY   = 1;
    const FLAG_WEEKLY  = 2;
    const FLAG_MONTHLY = 4;
    const FLAG_OLYMP   = 8;

    const STAT_DAILY   = "daily";
    const STAT_WEEKLY  = "weekly";
    const STAT_MONTHLY = "monthly";
    const STAT_OLYMP   = "olympic";

    var $mLocale;
    var $localizationData;
    var $levelData;

    function __construct() {
	parent::__construct();
	$this->mLocale = new Model_Locale;
	$localizationFile = "resources/dispath-localization.json";
	$localizationFileData = "{".rtrim(trim(trim(trim(file_get_contents($localizationFile))), "{"), "}")."}";
	$this->localizationData = (array)json_decode($localizationFileData);

	$locales = $this->mLocale->getLocales();
	$this->levelData = array();
	foreach($locales as $localeId => $localeName) {
	    $olympiad = false;
	    $level = 1;
	    $dataFileName = $olympiad ? "resources/Level_".$level."/Levels/".$localeName.".lproj/OlympiadLevels.txt" : "resources/Level_".$level."/Levels/".$localeName.".lproj/Level_".$level.".txt";
	    $jsonFileData = rtrim(trim(trim(trim(file_get_contents($dataFileName))), "{"), "}");
	    if($jsonFileData) $jsonFileData = "{".$jsonFileData."}";
	    $this->levelData[$localeName][$level] = json_decode($jsonFileData);
	}
    }

    public function index() {
      echo 'index';
    }

    public function activationreminder() {
/*	if(!$this->input->is_cli_request()){
	    echo "This script can only be accessed via the command line" . PHP_EOL;
	    return;
	}*/
        $mUser = new Model_User;
        $users = $mUser->getUsers(array('is_confirm' => 0, 'email' => 'alexby10@gmail.com'));
    	foreach($users as $user) {
    	    if($user['confirm_count'] < 3) {
		echo $user['confirm_count']."<br/>";
    		$mMail = new Model_Mail;
        	$mMail->sendConfirmation($user['id'], null, $user['locale_id']);

    		$saveItem = array();
        	$saveItem['confirm_count'] = ++$user['confirm_count'];
        	$this->db->where('id',$user['id'])->update('users',$saveItem);
    	    }
    	}
    }

    public function statistics() {
	$this->load->model('Request');
	$reportType = !$this->Request->getData('type') ? 'yearly' : $this->Request->getData('type');

	$mChild = new Model_Child;
	$childs = $mChild->getAllChilds();

	echo "<h2>Building <strong>".$reportType."</strong> statistics</h2>";
	$datesRange = $this->_getDatesRange($reportType);
	echo "<p>Start date: " . date('d/m/Y H:i:s', $datesRange[0]) . "<br/>";
	echo "End date: " . date('d/m/Y H:i:s', $datesRange[1]) . "</p>";
//	echo $yTimeEnd - $yTimeStart."<br/>";

	$totalUsers = 0;
	foreach($childs as $childData){
//	    echo "Fetching... ".$childData['name']." " . $childData['id']. "<br/>";
/*	    if($childData['id'] == 371) {
		echo "Here";
		echo " " . $childData['name'];
		echo " " . $childData['send_statistics_type'];

		if(($childData['send_statistics_type'] & self::FLAG_DAILY) == self::FLAG_DAILY) {
		    echo "Daily stat is on<br/>";
		}
		if(($childData['send_statistics_type'] & self::FLAG_WEEKLY) == self::FLAG_WEEKLY) {
		    echo "Weekly stat is on<br/>";
		}
		if(($childData['send_statistics_type'] & self::FLAG_MONTHLY) == self::FLAG_MONTHLY) {
		    echo "Monthly stat is on<br/>";
		}
		if(($childData['send_statistics_type'] & self::FLAG_OLYMP) == self::FLAG_OLYMP) {
		    echo "Olympiad stat is on<br/>";
		}
	    }
	    else {
		continue;
	    }*/
	    // check permissions
	    if(($reportType == self::STAT_DAILY && (($childData['send_statistics_type'] & self::FLAG_DAILY) == self::FLAG_DAILY)) ||
	       ($reportType == self::STAT_WEEKLY && (($childData['send_statistics_type'] & self::FLAG_WEEKLY) == self::FLAG_WEEKLY)) ||
	       ($reportType == self::STAT_MONTHLY && (($childData['send_statistics_type'] & self::FLAG_MONTHLY) == self::FLAG_MONTHLY))) {

    		$mUser = new Model_User;
    		$userData = $mUser->getUserById($childData['user_id']);
	        $stats = $this->_fetchStatistics($childData['id'], $reportType, $this->mLocale->getLocaleById($userData['locale_id']));
	        if(!$this->_isStatEmpty($stats)) {
		    $stats['child_name'] = $childData['name'];
		    $stats['reportType'] = $reportType;
		    if($childData['avatar']) {
			$p = explode("@", $childData['avatar']);
			$pp = explode("avatar_", $p[0]);
			$stats['avatar'] = strtolower('avatar-bg-'.$pp[1].'.gif');
		    }
		    else $stats['avatar'] = 'avatar-bg.gif';
		    
		    echo "<pre>";
		    print_r($stats);
		    echo "</pre>";

		    $totalUsers++;
    		    $mMail = new Model_Mail;
    		    $mMail->sendStatistics($childData['user_id'], json_decode($childData['send_statistics_accounts']), $stats, $userData['locale_id']);
    	        }
    	    }
        }
        echo "<h2>Found ".$totalUsers." users with non-empty statistics within the selected period</h2>";
    }

    public function _isStatEmpty($stats) {
//	return false;
	foreach($stats as $name => $item){
	    if($item && $name != "totalTime")
		return false;
	}
	
	return true;
    }

    protected function _getDatesRange($statType = "daily") {
	switch($statType) {
	    case self::STAT_WEEKLY:
		$yTimeStart = strtotime('-1 Sunday');
//		$yTimeStart = mktime(0, 0, 0,  date('n', strtotime('first day of last week')), date('j', strtotime('first day of last week')), date('Y', strtotime('first day of last week')));
		$yTimeEnd   = strtotime("+1 week", $yTimeStart)-1;
//		$yTimeEnd = mktime(23, 59, 59,  date('n', strtotime('last day of last week')), date('j', strtotime('last day of last week')), date('Y', strtotime('last day of last week')));
		
/*		$ts = strtotime(time());
		$start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
		$yTimeStart = mktime(0,0,0, date('n', $start), date('j', $start), date('Y', $start));
		$yTimeEnd = mktime(23,59,59, date('n', strtotime('next sunday', $start)), date('j', strtotime('next sunday', $start)), date('Y', strtotime('next sunday', $start)));*/
		break;

	    case self::STAT_MONTHLY:
//		$yTimeStart = mktime(0, 0, 0, date('n')-1, 1, date('Y'));
		$yTimeStart = mktime(0, 0, 0,  date('n', strtotime('first day of last month')), date('j', strtotime('first day of last month')), date('Y', strtotime('first day of last month')));
//		$yTimeEnd   = mktime(23, 59, 59, date('n')-1, cal_days_in_month(CAL_GREGORIAN, date('n')-1, date('Y')-1), date('Y'));
		$yTimeEnd = mktime(23, 59, 59,  date('n', strtotime('last day of last month')), date('j', strtotime('last day of last month')), date('Y', strtotime('last day of last month')));
		break;

	    case 'yearly':
		$yTimeStart = mktime(0, 0, 0, 1, 1, date('Y')-1);
		$yTimeEnd   = mktime(23, 59, 59, 12, 31, date('Y')-1);
		break;

	    case self::STAT_DAILY:
	    default:
//		$yTimeStart = mktime(0, 0, 0, date('n'), date('j')-1, date('Y'));
//		$yTimeEnd   = mktime(23, 59, 59, date('n'), date('j')-1, date('Y'));
		$yTimeStart = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
		$yTimeEnd   = mktime(23, 59, 59, date('n'), date('j'), date('Y'));
		break;
	}
	
	return array($yTimeStart, $yTimeEnd);
    }

    public function _fetchStatistics($childId, $statType = "daily", $locale_code) {
	date_default_timezone_set('GMT');
        
        $datesRange = $this->_getDatesRange($statType);
        $yTimeStart = $datesRange[0];
        $yTimeEnd   = $datesRange[1];


        $result = $this->db->from('account_level_path as lp')
            ->select('
            lp.id AS lp_level_path_id,
            lp.identifier_number AS lp_identifier_number,
            lp.is_all_levels_solved AS lp_is_all_levels_solved,
            lp.last_change_date AS lp_last_change_date,
            l.id AS l_level_id,
            l.identifier_string AS l_identifier_string,
            l.count_solved_tasks AS l_count_solved_tasks,
            l.count_started_tasks AS l_count_started_tasks,
            l.current_score AS l_current_score,
            l.is_all_tasks_solved AS l_is_all_tasks_solved,
            l.is_selected AS l_is_selected,
            l.last_change_date AS l_last_change_date,
            t.id AS t_id,
            t.level_id AS t_level_id,
            t.identifier_string AS t_identifier_string,
            t.count_solved_actions AS t_count_solved_actions,
            t.current_score AS t_current_score,
            t.seconds_per_task AS t_seconds_per_task,
            t.status_number AS t_status_number,
            t.last_change_date AS t_last_change_date,
            a.id AS a_id,
            a.parent_id AS a_parent_id,
            a.task_id AS a_task_id,
            a.identifier_string AS a_identifier_string,
            a.answer AS a_answer,
            a.error_number AS a_error_number,
            a.string_represent AS a_string_represent,
            a.type_number AS a_type_number,
            a.is_correct AS a_is_correct,
            a.etalon AS a_etalon,
            ae.id AS ae_id,
            ae.parent_id AS ae_parent_id,
            ae.task_error_id AS ae_task_error_id,
            ae.identifier_string AS ae_identifier_string,
            ae.answer AS ae_answer,
            ae.error_number AS ae_error_number,
            ae.string_represent AS ae_string_represent,
            ae.type_number AS ae_type_number,
            ae.is_correct AS ae_is_correct,
            ae.etalon AS ae_etalon
            ')
            ->join('account_levels as l','l.level_path_id = lp.id','left')
            ->join('account_tasks as t','t.level_id = l.id','left')
            ->join('account_actions as a','a.task_id = t.id AND a.parent_id = 0','left')
            ->join('account_task_errors as te','te.task_id = t.id','left')
            ->join('account_actions as ae','ae.task_error_id = te.id AND ae.parent_id = 0','left')
            // only related to user, solved ones, and correct actions
            ->where('lp.child_id',$childId)
            ->where('(t.status_number = 4 OR t.status_number = 1)')
            ->where('t.seconds_per_task >', 0)
            ->where('a.is_correct', 1)
            ->where('t.last_change_date >= ' .  $yTimeStart)->where('t.last_change_date <= ' . $yTimeEnd)
            ->get()->result_array();

//	echo "<pre>".print_r($result, true)."</pre>";

	$stats = array();
	$stats['taskSolved'] = 0;
	$stats['taskTestSolved'] = 0;
	$stats['taskOlympicSolved'] = 0;
	$stats['taskTime'] = 0;
	$stats['taskEasy'] = 0;
	$stats['taskEasyId'] = 0;
	$stats['taskDifficult'] = 0;
	$stats['taskDifficultId'] = 0;
	$stats['taskSpeed'] = 0;
	$stats['solutionAction'] = 0;
	$stats['errorAction'] = 0;
	$stats['solutionExpression'] = 0;
	$stats['errorExpression'] = 0;
	$stats['score'] = 0;
	$stats['totalTime'] = 0;

        $data = array();
        $j = 0;
        foreach($result as $item){
            $level_path_id = $item['lp_level_path_id'];
            $data[$level_path_id]['identifierNumber'] = (int)$item['lp_identifier_number'];
            $data[$level_path_id]['isAllLevelsSolved'] = (int)$item['lp_is_all_levels_solved'];
            $data[$level_path_id]['lastChangeDate'] = (int)$item['lp_last_change_date'];
            if($item['l_level_id']){
                $levelId = $item['l_level_id'];
                $data[$level_path_id]['levels'][$levelId]['identifierString'] = $item['l_identifier_string'];
                $data[$level_path_id]['levels'][$levelId]['countSolvedTasks'] = (int)$item['l_count_solved_tasks'];
                $data[$level_path_id]['levels'][$levelId]['countStartedTasks'] = (int)$item['l_count_started_tasks'];
                $data[$level_path_id]['levels'][$levelId]['currentScore'] = (float)$item['l_current_score'];
                $data[$level_path_id]['levels'][$levelId]['isAllTasksSolved'] = (int)(bool)$item['l_is_all_tasks_solved'];
                $data[$level_path_id]['levels'][$levelId]['isSelected'] = (int)(bool)$item['l_is_selected'];
                $data[$level_path_id]['levels'][$levelId]['lastChangeDate'] = (float)$item['l_last_change_date'];
                $data[$level_path_id]['levels'][$levelId]['lastChangeDateHuman'] =  date('d/m/Y H:i:s', $data[$level_path_id]['levels'][$levelId]['lastChangeDate']);
                if($item['t_id']){
                    $taskId = $item['t_id'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['identifierString'] = $item['t_identifier_string'];

                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['countSolvedActions'] = (int)$item['t_count_solved_actions'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['currentScore'] = (float)$item['t_current_score'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['lastChangeDate'] = (float)$item['t_last_change_date'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['lastChangeDateHuman'] = date('d/m/Y H:i:s', $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['lastChangeDate']);
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['secondsPerTask'] = (int)$item['t_seconds_per_task'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['statusNumber'] = (int)$item['t_status_number'];

                    if($item['a_id']){
                        $actionId = $item['a_id'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['typeNumber'] = (int)$item['a_type_number'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['isCorrect'] = (int)$item['a_is_correct'];
                    }

                    if($item['ae_id']){
                        $actionErrorId = $item['ae_id'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['errors'][$actionErrorId]['typeNumber'] = (int)$item['ae_type_number'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['errors'][$actionErrorId]['isCorrect'] = (int)$item['ae_is_correct'];
                    }
                }
            }
            $j++;
	}

//	if(!empty($data)) echo "<pre>" . print_r($data, true) . "</pre>";
	
        foreach($data as $levelPath){
            if(isset($levelPath['levels']) && is_array($levelPath['levels'])){
                foreach($levelPath['levels'] as $level){
                    if(isset($level['tasks']) && is_array($level['tasks'])){
                        foreach($level['tasks'] as $task){
			    $stats['taskSolved'] += 1;

			    $pp = explode(".", $task['identifierString']);
			    $ppp = explode("-", $pp[0]);
			    if($ppp[3] == 1) $stats['taskTestSolved'] += 1;
			    
			    $stats['score'] += $task['currentScore'];
			    $stats['taskTime'] += $task['secondsPerTask'];
			    if($task['secondsPerTask'] > $stats['taskDifficult']) {
				$stats['taskDifficult'] = $task['secondsPerTask'];
				$stats['taskDifficultId'] = $task['identifierString'];
			    }
			    if($task['secondsPerTask'] < $stats['taskEasy'] || $stats['taskEasy'] == 0) {
				$stats['taskEasy'] = $task['secondsPerTask'];
				$stats['taskEasyId'] = $task['identifierString'];
			    }
                            if(isset($task['actions']) && is_array($task['actions'])){
                                foreach($task['actions'] as $action){
				    switch($action['typeNumber']) {
					case 0:
					    ++$stats['solutionExpression'];
					    break;
					case 1:
					    ++$stats['solutionAction'];
					    break;
				    }
                                }
                            }
                            if(isset($task['errors']) && is_array($task['errors'])){
                                foreach($task['errors'] as $error){
				    switch($error['typeNumber']) {
					case 0:
					    ++$stats['errorExpression'];
					    break;
					case 1:
					    ++$stats['errorAction'];
					    break;
				    }
                                }
                            }
                        }
                    }
                }
            }
        }

	if($stats['taskSolved'])
	    $stats['taskSpeed'] = round(($stats['taskTime'] / $stats['taskSolved']) / 60, 1);

	if($stats['taskEasyId']) {
	    $pp = explode(".", $stats['taskEasyId']);
	    $stats['taskEasyNumber'] = $pp[1];
	    $ppp = explode("-", $pp[0]);
	    $stats['taskEasyType'] = $ppp[3];
	    $stats['taskEasyPath'] = $ppp[1];
	    $stats['taskEasyName'] = (($ppp[3] == 1) ? $this->localizationData[$locale_code]->training_task . " " : "") . $this->localizationData[$locale_code]->task . " " . $stats['taskEasyNumber'];
	    
	    // get path name
	    $path = $ppp[0]."-".$ppp[1]."-".$ppp[2];
	    foreach($this->levelData[$locale_code][$ppp[0]]->paths as $pathItem) {
		if(array_key_exists($path, $pathItem->levels)) {
		    $stats['taskEasyPathName'] = $pathItem->levels->$path->Name;
		    break;
		}
	    }
	}
	if($stats['taskDifficultId']) {
	    $pp = explode(".", $stats['taskDifficultId']);
	    $stats['taskDifficultNumber'] = $pp[1];
	    $ppp = explode("-", $pp[0]);
	    $stats['taskDifficultType'] = $ppp[3];
	    $stats['taskDifficultPath'] = $ppp[1];
	    $stats['taskDifficultName'] = (($ppp[3] == 1) ? $this->localizationData[$locale_code]->training_task . " " : "") . $this->localizationData[$locale_code]->task . " " . $stats['taskDifficultNumber'];
	    // get path name
	    $path = $ppp[0]."-".$ppp[1]."-".$ppp[2];
	    foreach($this->levelData[$locale_code][$ppp[0]]->paths as $pathItem) {
		if(array_key_exists($path, $pathItem->levels)) {
		    $stats['taskDifficultPathName'] = $pathItem->levels->$path->Name;
		    break;
		}
	    }
	}
	
	if(!$this->_isStatEmpty($stats)) {
	    $stats['startDate'] = date('d/m/Y H:i:s', $yTimeStart);
	    $stats['endDate'] = date('d/m/Y H:i:s', $yTimeEnd);
	}

	// fetch time spent in app
	if($yTimeEnd > $yTimeStart) {
	    $sDate = date('Y-m-d', $yTimeStart);
	    $eDate = date('Y-m-d', $yTimeEnd);
	    $cDate = $sDate;
	    $tTotal = 0;
	    while(1) {
		$year = date ("Y", strtotime ($cDate));
		$month = date ("m", strtotime($cDate));
		$day = date ("d", strtotime($cDate));
		$result = $this->db->from('account_time at')
	                ->select('at.time as time')
	                ->where('at.child_id',$childId)
	                ->where('at.year', $year)
	                ->where('at.month', $month)
	                ->where('at.day', $day)
	                ->get()->result_array();
		foreach($result as $item){
		    $tTotal += $item['time'];
		}
	                
		if($cDate == $eDate) break;
		$cDate = date ("Y", strtotime ("+1 day", strtotime($cDate)))."-".date ("m", strtotime ("+1 day", strtotime($cDate)))."-".date ("d", strtotime ("+1 day", strtotime($cDate)));
	    }
	}
	$stats['totalTime'] = round($tTotal / 60 / 60 , 2);


	// get olympic stats
        $result = $this->db->from('account_olymp_levels as l')
            ->select('
            l.id AS l_id,
            l.identifier_string AS l_identifier_string,
            l.is_all_tasks_solved AS l_is_all_tasks_solved,
            l.last_change_date AS l_last_change_date,
            t.id AS t_id,
            t.level_id AS t_level_id,
            t.identifier_number AS t_identifier_number,
            t.current_score AS t_current_score,
            t.status_number AS t_status_number,
            t.try_counter AS t_try_counter,
            t.last_change_date AS t_last_change_date,
            a.id AS a_id,
            a.task_id AS a_task_id,
            a.identifier_number AS a_identifier_number,
            a.is_correct AS a_is_correct
            ')
            ->join('account_olymp_tasks as t','t.level_id = l.id','left')
            ->join('account_olymp_actions as a','a.task_id = t.id','left')
            ->where('l.child_id',$childId)
            ->where('(t.status_number = 4 OR t.status_number = 1)')
//            ->where('t.seconds_per_task >', 0)
            ->where('a.is_correct', 1)
            ->where('t.last_change_date >= ' .  $yTimeStart)->where('t.last_change_date <= ' . $yTimeEnd)

            ->get()->result_array();

        $data = array();
        foreach($result as $item){
            $levelId = $item['l_id'];
            $list = array();
            $data[$levelId]['identifierString'] = $item['l_identifier_string'];
            $data[$levelId]['isAllTasksSolved'] = (int)(bool)$item['l_is_all_tasks_solved'];
            $data[$levelId]['lastChangeDate'] = (float)$item['l_last_change_date'];
            if($item['t_id']){
                $taskId = $item['t_id'];
                $data[$levelId]['tasks'][$taskId]['identifierNumber'] = (int)$item['t_identifier_number'];
                $data[$levelId]['tasks'][$taskId]['currentScore'] = (float)$item['t_current_score'];
                $data[$levelId]['tasks'][$taskId]['statusNumber'] = (int)$item['t_status_number'];
                $data[$levelId]['tasks'][$taskId]['tryCounter'] = (int)$item['t_try_counter'];
                $data[$levelId]['tasks'][$taskId]['lastChangeDate'] = (float)$item['t_last_change_date'];
                if($item['a_id']){
                    $actionId = $item['a_id'];
                    $data[$levelId]['tasks'][$taskId]['actions'][$actionId]['identifierNumber'] = (int)$item['a_identifier_number'];
                    $data[$levelId]['tasks'][$taskId]['actions'][$actionId]['isCorrect'] = (int)(bool)$item['a_is_correct'];
                    if($item['h_id']){
                        $hintId = $item['h_id'];
                        $data[$levelId]['tasks'][$taskId]['actions'][$actionId]['hints'][$hintId]['identifierNumber'] = (int)$item['h_identifier_number'];
                        $data[$levelId]['tasks'][$taskId]['actions'][$actionId]['hints'][$hintId]['userInput'] = $item['h_user_input'];
                    }
                }
            }
        }
        foreach($data as $level){
            if(isset($level['tasks']) && is_array($level['tasks'])){
                foreach($level['tasks'] as $task){
		    $stats['taskSolved'] += 1;
		    $stats['taskOlympicSolved'] += 1;
		    $stats['score'] += $task['currentScore'];
                }
            }
        }

	return $stats;
    }


/*
typedef NS_ENUM(NSUInteger, TaskStatus) {
    kTaskStatusUndefined = 0,
        kTaskStatusSolved  = 1,
            kTaskStatusError   = 2,
                kTaskStatusStarted = 3,
                    kTaskStatusSolvedNotAll = 4
                    };


typedef NS_ENUM(NSUInteger, ActionType) {
    kActionTypeExpression = 0,
        kActionTypeSolution   = 1,
            kActionTypeAnswer     = 2
            };
*/
}
