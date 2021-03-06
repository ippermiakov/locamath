<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Level extends API_Model {
    
    protected $_levelsIdentifiers = null;
    protected $_tasksIdentifiers = null;
    
    protected $_olympTasksIdentifiers = null;
    protected $_olympActionsIdentifiers = null;
    protected $_olympHintsIdentifiers = null;

    public function updateChildLevels($childId,$data = ''){
//	echo "updateChildLevels, childId: "  + $childId + "\n";
//	print_r($data);
        if($childId && $data){
            $data = json_decode($data,true);
            if($data && is_array($data)){
                $aLevelPathes = $this->getLevelPathIdentifiers($childId);
                foreach($data as $levelPath){
//            	    echo "iteration levelPath...\n";
                    $saveItem = array();
                    if(!isset($levelPath['identifierNumber']) || !$levelPath['identifierNumber']){
                        $this->Response->addError(1022);
                        $this->Response->send();
                    }
                    $saveItem['child_id'] = $childId;
                    $saveItem['identifier_number'] = $levelPath['identifierNumber'];
                    if(isset($levelPath['isAllLevelsSolved'])){
                        $saveItem['is_all_levels_solved'] = (int)(bool)$levelPath['isAllLevelsSolved'];
                    }
                    if(isset($levelPath['lastChangeDate'])){
                        $saveItem['last_change_date'] = $levelPath['lastChangeDate'];
                    }
                    if(isset($aLevelPathes[$levelPath['identifierNumber']])){
                        $levelPathId = $aLevelPathes[$levelPath['identifierNumber']];
                        $this->db->where('id',$levelPathId)->update('account_level_path',$saveItem);
                    }else{
                        $this->db->insert('account_level_path',$saveItem);
                        $levelPathId = $this->db->insert_id();
                    }
                    if(isset($levelPath['levels']) && is_array($levelPath['levels'])){
                        $aLevels = $this->getLevelsIdentifiers($levelPathId);
                        foreach($levelPath['levels'] as $level){
//            		    echo "\titeration level...\n";
                            $saveItem = array();
                            if(!isset($level['identifierString']) || !$level['identifierString']){
                                $this->Response->addError(1014);
                                $this->Response->send();
                            }
                            $saveItem['level_path_id'] = $levelPathId;
                            $saveItem['identifier_string'] = $level['identifierString'];
                            if(isset($level['countSolvedTasks'])){
                                $saveItem['count_solved_tasks'] = (int)$level['countSolvedTasks'];
                            }
                            if(isset($level['countStartedTasks'])){
                                $saveItem['count_started_tasks'] = (int)$level['countStartedTasks'];
                            }
                            if(isset($level['currentScore'])){
                                $saveItem['current_score'] = (float)$level['currentScore'];
                            }
                            if(isset($level['isAllTasksSolved'])){
                                $saveItem['is_all_tasks_solved'] = (int)(bool)$level['isAllTasksSolved'];
                            }
                            if(isset($level['isSelected'])){
                                $saveItem['is_selected'] = (int)(bool)$level['isSelected'];
                            }
                            if(isset($level['lastChangeDate'])){
                                $saveItem['last_change_date'] = $level['lastChangeDate'];
                            }
                            if(isset($aLevels[$level['identifierString']])){
                                $levelId = $aLevels[$level['identifierString']];
                                $this->db->where('id',$levelId)->update('account_levels',$saveItem);
                            }else{
                                $this->db->insert('account_levels',$saveItem);
                                $levelId = $this->db->insert_id();
                            }
                            if(isset($level['tasks']) && is_array($level['tasks'])){
                                $aTasks = $this->getTasksIdentifiersByLevelId($levelId);
                                foreach($level['tasks'] as $task){
//            			    echo "\t\titeration task " . $task['identifierString'] . " ...\n";
                                    $saveItem = array();
                                    if(!isset($task['identifierString']) || !$task['identifierString']){
                                        $this->Response->addError(1015);
                                        $this->Response->send();
                                    }
                                    $saveItem['level_id'] = $levelId;
                                    $saveItem['identifier_string'] = $task['identifierString'];
                                    if(isset($task['countSolvedActions'])){
                                        $saveItem['count_solved_actions'] = (int)$task['countSolvedActions'];
                                    }
                                    if(isset($task['currentScore'])){
                                        $saveItem['current_score'] = (float)$task['currentScore'];
                                    }
                                    if(isset($task['lastChangeDate'])){
                                        $saveItem['last_change_date'] = $task['lastChangeDate'];
                                    }
                                    if(isset($task['secondsPerTask'])){
                                        $saveItem['seconds_per_task'] = (int)$task['secondsPerTask'];
                                    }
                                    if(isset($task['statusNumber'])){
                                        $saveItem['status_number'] = (int)$task['statusNumber'];
                                    }
                                    if(isset($task['statusNumber'])){
                                        $saveItem['status_number'] = (int)$task['statusNumber'];
                                    }
                                    if(isset($aTasks[$task['identifierString']])){
                                        $taskId = $aTasks[$task['identifierString']];
                                        $this->db->where('id',$taskId)->update('account_tasks',$saveItem);
                                    }else{
                                        $this->db->insert('account_tasks',$saveItem);
                                        $taskId = $this->db->insert_id();
                                    }

				    /* Handling of errors */
                                    if(isset($task['taskErrors']) && is_array($task['taskErrors'])){
                                        foreach($task['taskErrors'] as $error){
//            				    echo "\t\t\titeration error...\n";
                                    	    $saveItem = array();
                                    	    $saveItem['task_id'] = $taskId;
                                    	    $saveItem['identifier_number'] = (int)$error['identifier'];
                                    	    if(isset($error['errorType'])){
                                    		$saveItem['error_type'] = (int)$error['errorType'];
                                    	    }
                                    	    if(isset($error['lastChangeDate'])){
                    				$saveItem['last_change_date'] = $error['lastChangeDate'];
                			    }
                			    
                			    $taskErrorId = $this->taskErrorsByIdentifierExists((int)$error['identifier']);
                			    if($taskErrorId){
                                    		$this->db->where('id',$taskErrorId)->update('account_task_errors',$saveItem);
                                	    }else{
                                    		$this->db->insert('account_task_errors',$saveItem);
                                    		$taskErrorId = $this->db->insert_id();
                                	    }

                                	    if(isset($error['actions']) && is_array($error['actions'])){
                                    		foreach($error['actions'] as $action){
//            					    echo "\t\t\t\titeration action...\n";
                                        	    $saveItem = array();
                                        	    if(!isset($action['identifierString']) || !$action['identifierString']){
                                            		$this->Response->addError(1016);
                                            		$this->Response->send();
                                        	    }
                                        	    $saveItem['task_error_id'] = $taskErrorId;
                                        	    $saveItem['parent_id'] = 0;
                                        	    $saveItem['identifier_string'] = $action['identifierString'];
                                        	    if(isset($action['answer'])){
                                            		$saveItem['answer'] = (string)$action['answer'];
                                        	    }
                                        	    if(isset($action['errorNumber'])){
                                            		$saveItem['error_number'] = (string)$action['errorNumber'];
                                        	    }
                                        	    if(isset($action['stringRepresentation'])){
                                            		$saveItem['string_represent'] = (string)$action['stringRepresentation'];
                                        	    }
                                        	    if(isset($action['typeNumber'])){
                                            		$saveItem['type_number'] = (int)$action['typeNumber'];
                                        	    }
                                        	    if(isset($action['isCorrect'])){
                                            		$saveItem['is_correct'] = (int)(bool)$action['isCorrect'];
                                        	    }
                                        	    if(isset($action['etalon'])){
                                            		$saveItem['etalon'] = $action['etalon'];
                                        	    }
                                        	    $actionId = $this->actionByIdentifierExists($action['identifierString']);
                                    		    if($actionId){
                                            		$this->db->where('id',$actionId)->update('account_actions',$saveItem);
                                        	    }else{
                                            		$this->db->insert('account_actions',$saveItem);
                                            		$actionId = $this->db->insert_id();
                                        	    }

						    // delete all existing subactions (we get all the current list each time)
                                        	    $this->db->delete('account_actions', array('task_error_id' => $taskErrorId, 'parent_id' => $actionId));
                                        	    if(isset($action['subActions']) && is_array($action['subActions'])){
                                            		foreach($action['subActions'] as $subAction){
                                                	    $saveItem = array();
                                                	    if(!isset($subAction['identifierString']) || !$subAction['identifierString']){
                                                    		$this->Response->addError(1017);
                                                    		$this->Response->send();
                                                	    }
                                                	    $saveItem['task_error_id'] = $taskErrorId;
                                                	    $saveItem['parent_id'] = $actionId;
                                                	    $saveItem['identifier_string'] = $subAction['identifierString'];
                                                	    if(isset($subAction['answer'])){
                                                    		$saveItem['answer'] = (string)$subAction['answer'];
                                                	    }
                                                	    if(isset($subAction['errorNumber'])){
                                                    		$saveItem['error_number'] = (string)$subAction['errorNumber'];
                                                	    }
                                                	    if(isset($subAction['stringRepresentation'])){
                                                    		$saveItem['string_represent'] = (string)$subAction['stringRepresentation'];
                                                	    }
                                                	    if(isset($subAction['typeNumber'])){
                                                    		$saveItem['type_number'] = (int)$subAction['typeNumber'];
                                                	    }
                                                	    if(isset($subAction['isCorrect'])){
                                                    		$saveItem['is_correct'] = (int)(bool)$subAction['isCorrect'];
                                                	    }
                                                	    if(isset($subAction['etalon'])){
                                                    		$saveItem['etalon'] = $subAction['etalon'];
                                                	    }
                                                	    $subActionId = $this->actionByIdentifierExists($subAction['identifierString']);
                                                	    if($subActionId){
                                                    		$this->db->where('id',$subActionId)->update('account_actions',$saveItem);
                                                	    }else{
                                                    		$this->db->insert('account_actions',$saveItem);
                                                    		$subActionId = $this->db->insert_id();
                                                	    }
                                            		}
                                        	    }
						}
					    }
                                        }
                                    }

                                    if(isset($task['actions']) && is_array($task['actions'])){
                                        foreach($task['actions'] as $action){
//            				    echo "\t\t\titeration action...\n";
                                            $saveItem = array();
                                            if(!isset($action['identifierString']) || !$action['identifierString']){
                                                $this->Response->addError(1016);
                                                $this->Response->send();
                                            }
                                            $saveItem['task_id'] = $taskId;
                                            $saveItem['parent_id'] = 0;
                                            $saveItem['identifier_string'] = $action['identifierString'];
                                            if(isset($action['answer'])){
                                                $saveItem['answer'] = (string)$action['answer'];
                                            }
                                            if(isset($action['errorNumber'])){
                                                $saveItem['error_number'] = (string)$action['errorNumber'];
                                            }
                                            if(isset($action['stringRepresentation'])){
                                                $saveItem['string_represent'] = (string)$action['stringRepresentation'];
                                            }
                                            if(isset($action['typeNumber'])){
                                                $saveItem['type_number'] = (int)$action['typeNumber'];
                                            }
                                            if(isset($action['isCorrect'])){
                                                $saveItem['is_correct'] = (int)(bool)$action['isCorrect'];
                                            }
                                            if(isset($action['etalon'])){
                                                $saveItem['etalon'] = $action['etalon'];
                                            }
                                            $actionId = $this->actionByIdentifierExists($action['identifierString']);
                                    	    if($actionId){
                                                $this->db->where('id',$actionId)->update('account_actions',$saveItem);
//                                                echo "Made update...\n";
                                            }else{
                                                $this->db->insert('account_actions',$saveItem);
                                                $actionId = $this->db->insert_id();
//                                                echo "Made insert...\n";
                                            }
                                            

					    // delete all existing subactions (we get all the current list each time)
                                            $this->db->delete('account_actions', array('task_id' => $taskId, 'parent_id' => $actionId));
                                            if(isset($action['subActions']) && is_array($action['subActions'])){
                                                foreach($action['subActions'] as $subAction){
                                                    $saveItem = array();
                                                    if(!isset($subAction['identifierString']) || !$subAction['identifierString']){
                                                        $this->Response->addError(1017);
                                                        $this->Response->send();
                                                    }
                                                    $saveItem['task_id'] = $taskId;
                                                    $saveItem['parent_id'] = $actionId;
                                                    $saveItem['identifier_string'] = $subAction['identifierString'];
                                                    if(isset($subAction['answer'])){
                                                        $saveItem['answer'] = (string)$subAction['answer'];
                                                    }
                                                    if(isset($subAction['errorNumber'])){
                                                        $saveItem['error_number'] = (string)$subAction['errorNumber'];
                                                    }
                                                    if(isset($subAction['stringRepresentation'])){
                                                        $saveItem['string_represent'] = (string)$subAction['stringRepresentation'];
                                                    }
                                                    if(isset($subAction['typeNumber'])){
                                                        $saveItem['type_number'] = (int)$subAction['typeNumber'];
                                                    }
                                                    if(isset($subAction['isCorrect'])){
                                                        $saveItem['is_correct'] = (int)(bool)$subAction['isCorrect'];
                                                    }
                                                    if(isset($subAction['etalon'])){
                                                        $saveItem['etalon'] = $subAction['etalon'];
                                                    }
                                                    $subActionId = $this->actionByIdentifierExists($subAction['identifierString']);
                                                    if($subActionId){
                                                        $this->db->where('id',$subActionId)->update('account_actions',$saveItem);
//                                                    	echo "Making subaction update...\n";
                                                    }else{
                                                        $this->db->insert('account_actions',$saveItem);
                                                        $subActionId = $this->db->insert_id();
//                                                    	echo "Making subaction insert..." . $subActionId . "\n";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $this->Response->addError(1019);
                $this->Response->send();
            }
        }else{
             $this->Response->addError(1020);
             $this->Response->send();
        }
    }
    
    public function getLevelPathIdentifiers($childId){
        $levels = $this->db->from('account_level_path')->where('child_id',$childId)->get()->result_array();
        $result = array();
        foreach($levels as $item){
            $result[$item['identifier_number']] = $item['id'];
        }
        return $result;
    }
    
    public function getLevelsIdentifiers($levelPathId = 0){
        if($this->_levelsIdentifiers === null){
            $levels = $this->db->from('account_levels')->get()->result_array();
            $this->_levelsIdentifiers = array();
            foreach($levels as $item){
                $this->_levelsIdentifiers[$item['level_path_id']][$item['identifier_string']] = $item['id'];
            }
        }
        return (isset($this->_levelsIdentifiers[$levelPathId]) ? $this->_levelsIdentifiers[$levelPathId] : array());
    }
    
    public function getTasksIdentifiersByLevelId($levelId = 0){
        if($this->_tasksIdentifiers === null){
            $tasks = $this->db->from('account_tasks')->get()->result_array();
            $this->_tasksIdentifiers = array();
            foreach($tasks as $item){
                $this->_tasksIdentifiers[$item['level_id']][$item['identifier_string']] = $item['id'];
            }
        }
        return (isset($this->_tasksIdentifiers[$levelId]) ? $this->_tasksIdentifiers[$levelId] : array());
    }

    public function actionByIdentifierExists($action_identifier){
	$actiondId = 0;
	$action = $this->db->from('account_actions')->like('identifier_string', $action_identifier, 'none')->get()->result_array();
	if(count($action))
	    $actionId = $action[0]['id'];

	return $actionId;
    }

    public function taskErrorsByIdentifierExists($task_error_identifier){
	$taskErrorId = 0;
	$taskError = $this->db->from('account_task_errors')->where('identifier_number', $task_error_identifier)->get()->result_array();
	if(count($taskError))
	    $taskErrorId = $taskError[0]['id'];

	return $taskErrorId;
    }
    
    public function getChildLevels($childId){
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
            te.id AS te_id,
            te.task_id AS te_task_id,
            te.error_type AS te_error_type,
            te.identifier_number AS te_identifier_number,
            te.last_change_date AS te_last_change_date,

            ae.id AS ae_id,
            ae.task_error_id AS ae_task_error_id,
            ae.identifier_string AS ae_identifier_string,
            ae.answer AS ae_answer,
            ae.error_number AS ae_error_number,
            ae.string_represent AS ae_string_represent,
            ae.type_number AS ae_type_number,
            ae.is_correct AS ae_is_correct,
            ae.etalon AS ae_etalon,

            sae.id AS sae_id,
            sae.task_error_id AS sae_task_error_id,
            sae.identifier_string AS sae_identifier_string,
            sae.answer AS sae_answer,
            sae.error_number AS sae_error_number,
            sae.string_represent AS sae_string_represent,
            sae.type_number AS sae_type_number,
            sae.is_correct AS sae_is_correct,
            sae.etalon AS sae_etalon,

             a.id AS a_id,
            a.task_id AS a_task_id,
            a.identifier_string AS a_identifier_string,
            a.answer AS a_answer,
            a.error_number AS a_error_number,
            a.string_represent AS a_string_represent,
            a.type_number AS a_type_number,
            a.is_correct AS a_is_correct,
            a.etalon AS a_etalon,
            sa.id AS sa_id,
            sa.task_id AS sa_task_id,
            sa.identifier_string AS sa_identifier_string,
            sa.answer AS sa_answer,
            sa.error_number AS sa_error_number,
            sa.string_represent AS sa_string_represent,
            sa.type_number AS sa_type_number,
            sa.is_correct AS sa_is_correct,
            sa.etalon AS sa_etalon
            ')
            ->join('account_levels as l','lp.id = l.level_path_id','left')
            ->join('account_tasks as t','l.id = t.level_id','left')
            ->join('account_task_errors as te','t.id = te.task_id','left')
            ->join('account_actions as ae','te.id = ae.task_error_id AND ae.parent_id = 0','left')
            ->join('account_actions as sae','ae.id = sae.parent_id','left')
            ->join('account_actions as a','t.id = a.task_id AND a.parent_id = 0','left')
            ->join('account_actions as sa','a.id = sa.parent_id','left')
            ->where('lp.child_id',$childId)
            ->get()->result_array();
        $data = array();
        foreach($result as $item){
            $level_path_id = $item['lp_level_path_id'];
            $data[$level_path_id]['identifierNumber'] = (int)$item['lp_identifier_number'];
            $data[$level_path_id]['isAllLevelsSolved'] = (int)$item['lp_is_all_levels_solved'];
            $data[$level_path_id]['lastChangeDate'] = (float)$item['lp_last_change_date'];
            if($item['l_level_id']){
                $levelId = $item['l_level_id'];
                $data[$level_path_id]['levels'][$levelId]['identifierString'] = $item['l_identifier_string'];
                $data[$level_path_id]['levels'][$levelId]['countSolvedTasks'] = (int)$item['l_count_solved_tasks'];
                $data[$level_path_id]['levels'][$levelId]['countStartedTasks'] = (int)$item['l_count_started_tasks'];
                $data[$level_path_id]['levels'][$levelId]['currentScore'] = (float)$item['l_current_score'];
                $data[$level_path_id]['levels'][$levelId]['isAllTasksSolved'] = (int)(bool)$item['l_is_all_tasks_solved'];
                $data[$level_path_id]['levels'][$levelId]['isSelected'] = (int)(bool)$item['l_is_selected'];
                $data[$level_path_id]['levels'][$levelId]['lastChangeDate'] = (float)$item['l_last_change_date'];
                if($item['t_id']){
                    $taskId = $item['t_id'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['identifierString'] = $item['t_identifier_string'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['countSolvedActions'] = (int)$item['t_count_solved_actions'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['currentScore'] = (float)$item['t_current_score'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['lastChangeDate'] = (float)$item['t_last_change_date'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['secondsPerTask'] = (int)$item['t_seconds_per_task'];
                    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['statusNumber'] = (int)$item['t_status_number'];
                    if($item['te_id']){
                        $taskErrorId = $item['te_id'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['errorType'] = (int)$item['te_error_type'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['identifier'] = (int)$item['te_identifier_number'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['lastChangeDate'] = $item['te_last_change_date'];
                	if($item['ae_id']){
                    	    $actionId = $item['ae_id'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['identifierString'] = $item['ae_identifier_string'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['answer'] = $item['ae_answer'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['errorNumber'] = (int)$item['ae_error_number'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['stringRepresentation'] = $item['ae_string_represent'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['typeNumber'] = (int)$item['ae_type_number'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['isCorrect'] = (int)(bool)$item['ae_is_correct'];
                    	    $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['etalon'] = (int)$item['ae_etalon'];
                    	    if($item['sae_id']){
                        	$subActionId = $item['sae_id'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['identifierString'] = $item['sae_identifier_string'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['answer'] = $item['sae_answer'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['errorNumber'] = (int)$item['sae_error_number'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['stringRepresentation'] = $item['sae_string_represent'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['typeNumber'] = (int)$item['sae_type_number'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['isCorrect'] = (int)(bool)$item['sae_is_correct'];
                        	$data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['taskErrors'][$taskErrorId]['actions'][$actionId]['subActions'][$subActionId]['etalon'] = (int)$item['sae_etalon'];
                    	    }
                	}
		    }
                    if($item['a_id']){
                        $actionId = $item['a_id'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['identifierString'] = $item['a_identifier_string'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['answer'] = $item['a_answer'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['errorNumber'] = (int)$item['a_error_number'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['stringRepresentation'] = $item['a_string_represent'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['typeNumber'] = (int)$item['a_type_number'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['isCorrect'] = (int)(bool)$item['a_is_correct'];
                        $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['etalon'] = (int)$item['a_etalon'];
                        if($item['sa_id']){
                            $subActionId = $item['sa_id'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['identifierString'] = $item['sa_identifier_string'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['answer'] = $item['sa_answer'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['errorNumber'] = (int)$item['sa_error_number'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['stringRepresentation'] = $item['sa_string_represent'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['typeNumber'] = (int)$item['sa_type_number'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['isCorrect'] = (int)(bool)$item['sa_is_correct'];
                            $data[$level_path_id]['levels'][$levelId]['tasks'][$taskId]['actions'][$actionId]['subActions'][$subActionId]['etalon'] = (int)$item['sa_etalon'];
                        }
                    }
                }
            }
        }
        $data = $this->_recursiveSetIndexes($data);
        return $data;
    } 
    
    protected function _recursiveSetIndexes($data = array()){
        $result = array();
        foreach($data as $k => $v){
            if(is_array($v)){
                if(is_numeric($k)){
                    $result[] = $this->_recursiveSetIndexes($v);
                }else{
                    $result[$k] = $this->_recursiveSetIndexes($v);
                }   
            }else{
                $result[$k] = $v;
            }
        }
        return $result;
    }
    
    public function updateChildOlympLevels($childId,$data = ''){
        if($childId && $data){
            $data = json_decode($data,true);
            if($data && is_array($data)){
                $aLevels = $this->getOlympLevelsIdentifiers($childId);
                foreach($data as $level){
                    $saveItem = array();
                    if(!isset($level['identifierString']) || !$level['identifierString']){
                        $this->Response->addError(1014);
                        $this->Response->send();
                    }
                    $saveItem['child_id'] = $childId;
                    $saveItem['identifier_string'] = $level['identifierString'];
                    if(isset($level['lastChangeDate'])){
                        $saveItem['last_change_date'] = $level['lastChangeDate'];
                    }
                    if(isset($level['isAllTasksSolved'])){
                        $saveItem['is_all_tasks_solved'] = (int)(bool)$level['isAllTasksSolved'];
                    }
                    if(isset($aLevels[$level['identifierString']])){
                        $levelId = $aLevels[$level['identifierString']];
                        $this->db->where('id',$levelId)->update('account_olymp_levels',$saveItem);
                    }else{
                        $this->db->insert('account_olymp_levels',$saveItem);
                        $levelId = $this->db->insert_id();
                    }
                    if(isset($level['tasks']) && is_array($level['tasks'])){
                        $aTasks = $this->getOlympTasksIdentifiersByLevelId($levelId);
                        foreach($level['tasks'] as $task){
                            $saveItem = array();
                            if(!isset($task['identifierNumber']) || !$task['identifierNumber']){
                                $this->Response->addError(1015);
                                $this->Response->send();
                            }
                            $saveItem['level_id'] = $levelId;
                            $saveItem['identifier_number'] = $task['identifierNumber'];
                            if(isset($task['lastChangeDate'])){
                                $saveItem['last_change_date'] = $task['lastChangeDate'];
                            }
                            if(isset($task['currentScore'])){
                                $saveItem['current_score'] = (float)$task['currentScore'];
                            }
                            if(isset($task['statusNumber'])){
                                $saveItem['status_number'] = (int)$task['statusNumber'];
                            }
                            if(isset($task['tryCounter'])){
                                $saveItem['try_counter'] = (int)$task['tryCounter'];
                            }
                            if(isset($aTasks[$task['identifierNumber']])){
                                $taskId = $aTasks[$task['identifierNumber']];
                                $this->db->where('id',$taskId)->update('account_olymp_tasks',$saveItem);
                            }else{
                                $this->db->insert('account_olymp_tasks',$saveItem);
                                $taskId = $this->db->insert_id();
                            }
                            if(isset($task['actions']) && is_array($task['actions'])){
                                $aActions = $this->getOlympActionsIdentifiersByTaskId($taskId);
                                foreach($task['actions'] as $action){
                                    $saveItem = array();
                                    if(!isset($action['identifierNumber']) || !$action['identifierNumber']){
                                        $this->Response->addError(1016);
                                        $this->Response->send();
                                    }
                                    $saveItem['task_id'] = $taskId;
                                    $saveItem['identifier_number'] = $action['identifierNumber'];
                                    if(isset($action['isCorrect'])){
                                        $saveItem['is_correct'] = (int)(bool)$action['isCorrect'];
                                    }
                                    if(isset($aActions[$action['identifierNumber']])){
                                        $actionId = $aActions[$action['identifierNumber']];
                                        $this->db->where('id',$actionId)->update('account_olymp_actions',$saveItem);
                                    }else{
                                        $this->db->insert('account_olymp_actions',$saveItem);
                                        $actionId = $this->db->insert_id();
                                    }
                                    if(isset($action['hints']) && is_array($action['hints'])){
                                        $aHints = $this->getOlympHintsIdentifiersByActionId($actionId);
                                        foreach($action['hints'] as $hint){
                                            $saveItem = array();
                                            if(!isset($hint['identifierNumber']) || !$hint['identifierNumber']){
                                                $this->Response->addError(1018);
                                                $this->Response->send();
                                            }
                                            $saveItem['action_id'] = $actionId;
                                            $saveItem['identifier_number'] = $hint['identifierNumber'];
                                            if(isset($hint['userInput'])){
                                                $saveItem['user_input'] = (string)$hint['userInput'];
                                            }
                                            if(isset($aHints[$hint['identifierNumber']])){
                                                $hintId = $aHints[$hint['identifierNumber']];
                                                $this->db->where('id',$hintId)->update('account_olymp_hints',$saveItem);
                                            }else{
                                                $this->db->insert('account_olymp_hints',$saveItem);
                                                $hintId = $this->db->insert_id();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $this->Response->addError(1019);
                $this->Response->send();
            }
        }else{
             $this->Response->addError(1020);
             $this->Response->send();
        }
    }
    
    public function getOlympLevelsIdentifiers($childId){
        $levels = $this->db->from('account_olymp_levels')->where('child_id',$childId)->get()->result_array();
        $result = array();
        foreach($levels as $item){
            $result[$item['identifier_string']] = $item['id'];
        }
        return $result;
    }
    
    public function getOlympTasksIdentifiersByLevelId($levelId = 0){
        if($this->_olympTasksIdentifiers === null){
            $tasks = $this->db->from('account_olymp_tasks')->get()->result_array();
            $this->_olympTasksIdentifiers = array();
            foreach($tasks as $item){
                $this->_olympTasksIdentifiers[$item['level_id']][$item['identifier_number']] = $item['id'];
            }
        }
        return (isset($this->_olympTasksIdentifiers[$levelId]) ? $this->_olympTasksIdentifiers[$levelId] : array());
    }
    
    public function getOlympActionsIdentifiersByTaskId($taskId = 0){
        if($this->_olympActionsIdentifiers === null){
            $actions = $this->db->from('account_olymp_actions')->get()->result_array();
            $this->_olympActionsIdentifiers = array();
            foreach($actions as $item){
                $this->_olympActionsIdentifiers[$item['task_id']][$item['identifier_number']] = $item['id'];
            }
        }
        return (isset($this->_olympActionsIdentifiers[$taskId]) ? $this->_olympActionsIdentifiers[$taskId] : array());
    }
    
    public function getOlympHintsIdentifiersByActionId($actionId = 0){
        if($this->_olympHintsIdentifiers === null){
            $hints = $this->db->from('account_olymp_hints')->get()->result_array();
            $this->_olympHintsIdentifiers = array();
            foreach($hints as $item){
                $this->_olympHintsIdentifiers[$item['action_id']][$item['identifier_number']] = $item['id'];
            }
        }
        return (isset($this->_olympHintsIdentifiers[$actionId]) ? $this->_olympHintsIdentifiers[$actionId] : array());
    }
    
    public function getChildOlympLevels($childId){
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
            a.is_correct AS a_is_correct,
            h.id AS h_id,
            h.action_id AS h_action_id,
            h.identifier_number AS h_identifier_number,
            h.user_input AS h_user_input
            ')
            ->join('account_olymp_tasks as t','t.level_id = l.id','left')
            ->join('account_olymp_actions as a','a.task_id = t.id','left')
            ->join('account_olymp_hints as h','h.action_id = a.id','left')
            ->where('l.child_id',$childId)
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
        $data = $this->_recursiveSetIndexes($data);
        return $data;
    }
    
}   
