<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends API_Controller {

    public function indexAction()
    {
        $help = new Model_Help;
        $help->code();
        $help->request_URL($this->config->item('base_url').'request');
        $help->methods("<b>GET</b> and <b>POST</b>");
        $help->_code();
        
        $help->action('Delete Account By Email');
        $help->code();
            $help->options('ac','deleteaccount',true);
            $help->options('email','[EMAIL]',true);
        $help->_code();
        
        $help->action('Delete Account By Session');
        $help->code();
            $help->options('ac','deletesession',true);
        $help->_code();

        $help->action('Reset Password For Account');
        $help->code();
            $help->options('ac','resetpassword',true);
            $help->options('email','[EMAIL]',true);
        $help->_code();

        $help->action('Change Password For Account');
        $help->code();
            $help->options('ac','changepassword',true);
            $help->options('email','[EMAIL]',true);
            $help->options('oldpassword','[PASSWORD]',true);
            $help->options('newpassword','[PASSWORD]',true);
        $help->_code();

        $help->action('Record the time spent in application');
        $help->code();
            $help->options('ac','recordtimeinapp',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('user_id','[int]',true);
            $help->options('date','[yyyy-mm-dd]',true);
            $help->options('time','[seconds]',true);
        $help->_code();

        $help->section('Server JSON');
        
        $help->action('Retrieve JSON data from server');
        $help->code();
            $help->options('ac','retrievejson',true);
            $help->options('object','[level | task | scheme | help]',true);
            $help->options('level','[1 | 2 | 3 | 4]',true);
            $help->options('olympiad', '[0 | 1]', false);
            $help->options('locale','[en | ru | de | es]', false);
            $help->response('json', '[JSON_DATA]');
        $help->_code();

        $help->action('Update training scheme data file on server');
        $help->code();
            $help->options('ac','updatetrainingscheme',true);
            $help->options('level','[1 | 2 | 3 | 4]',true);
            $help->options('data', '[JSON_DATA]', true);
        $help->_code();
        
        $help->section('Auth');
        
        $help->action('Register via Server');
        $help->code();
            $help->options('ac','register',true);
            $help->options('email','[EMAIL]',true);
            $help->options('password','[PASSWORD]',true);
            $help->options('locale','[en | ru]');
        $help->_code();

        $help->action('Register via Social Network');
        $help->code();
            $help->options('ac','registerdevice',true);
            $help->options('social_id','[FB id]',true);
            $help->options('email','[EMAIL]');
            $help->options('locale','[en | ru]');
        $help->_code();
        
        $help->action('Login via Server');
        $help->code();
            $help->options('ac','login',true);
            $help->options('email','[EMAIL]',true);
            $help->options('password','[PASSWORD]',true);
            $help->response('authid','[AUTHID]');
        $help->_code();

        $help->action('Login via Social Network');
        $help->code();
            $help->options('ac','registerdevice',true);
            $help->options('social_id','[FB id]',true);
        $help->_code();

        $help->action('Update user location on the server');
        $help->code();
            $help->options('ac','updatelocation',true);
            $help->options('latitude','[LATITUDE]',true);
            $help->options('longitude','[LONGITUDE]',true);
        $help->_code();

        
        $help->section('Children');
        
        $help->action('Get childs');
        $help->code();
            $help->options('ac','getchilds',true);
            $help->options('authid','[AUTHID]',true);
        $help->_code();
        
        $help->action('Add child');
        $help->code();
            $help->options('ac','addchild',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('name','[NAME]',true);
            $help->options('gender','[1- men, by default || 2 - women]');
            $help->options('avatar','[BASE64]');
            $help->options('is_training_complete','[boolean]');
            $help->options('isSoundsOn','[boolean]');
            $help->options('isMusicOn','[boolean]');
            $help->options('PostStatisticsType','[int]');
            $help->options('SendStatisticsType','[int]');
        $help->_code();
        
        $help->action('Delete child');
        $help->code();
            $help->options('ac','deletechild',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('name','[NAME]',true);
        $help->_code();
        
        $help->action('Set active child');
        $help->code();
            $help->options('ac','setactivechild',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('name','[NAME]');
            $help->options('id','[int]');
        $help->_code();
        
        $help->action('Get active child');
        $help->code();
            $help->options('ac','getactivechild',true);
            $help->options('authid','[AUTHID]',true);
        $help->_code();
        
        $help->action('Set child details');
        $help->code();
            $help->options('ac','setchilddetails',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('name','[NAME]',true);
            $help->options('gender','[1- men, by default || 2 - women]');
            $help->options('avatar','[BASE64]');
            $help->options('isTrainingComplete','[BOOL]');
            $help->options('isSoundsOn','[BOOL]');
            $help->options('isMusicOn','[BOOL]');
            $help->options('PostStatisticsType','[INT]');
            $help->options('SendStatisticsType','[INT]');
            $help->options('postStatisticsAccounts','[JSON]');
            $help->options('sendStatisticsAccounts','[JSON]');
            $help->options('points','[FLOAT]');
        $help->_code();
        
        $help->section('Levels');
        
        $help->action('Get Levels');
        $help->code();
            $help->options('ac','getlevels',true);
            $help->options('authid','[AUTHID]',true);
        $help->_code();
        
        $help->action('Update Levels');
        $help->code();
            $help->options('ac','updatelevels',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('data','[JSON]',true);
            $help->requried_Fields('identifier*','[IDENTIFIER]',true);
            $arr = array(
            array(
                'identifierNumber' => 'netlevelpath1',
                'isAllLevelsSolved' => true,
                'lastChangeDate' => 'UNIXTIME',
                'levels' => array(
                    array(
                        'identifierString' => 'new_level1',
                        'countSolvedTasks' => 1,
                        'countStartedTasks' => 2,
                        'currentScore' => 4.6,
                        'isAllTasksSolved' => true,
                        'isSelected' => true,
                        'lastChangeDate' => 'UNIXTIME',
                        'tasks' => array(
                            array(
                                'countSolvedActions' => 2,
                                'currentScore' => 10.1,
                                'identifierString' => 'newtask1',
                                'secondsPerTask' => 10,
                                'statusNumber' => 1,
                                'lastChangeDate' => 'UNIXTIME',
                                'taskErrors' => array(
                            	    array(
                            		'identifierString' => 'identifier',
                            		'errorType' => 'Int',
                            		'lastChangeDate' => 'Last change date',
                            		'actions' => array(
                                    	    'answer' => 'Answer1',
                                    	    'errorNumber' => 2,
                                    	    'identifierString' => 'newAction1',
                                    	    'stringRepresentation' => 'represent',
                                    	    'typeNumber' => 5,
                                    	    'isCorrect' => true,
                                    	    'etalon' => '1111',
                                    	    'subActions' => array(
                                        	array(
                                            	    'answer' => 'Answer2',
                                            	    'errorNumber' => 2,
                                            	    'identifierString' => 'newAction3',
                                            	    'stringRepresentation' => 'represent',
                                            	    'typeNumber' => 5,
                                            	    'isCorrect' => true,
                                            	    'etalon' => '1111',
                                        	)
                            		    ),
                            		),
                            	    ),
                                ),
                                'actions' => array(
                                    array(
                                        'answer' => 'Answer1',
                                        'errorNumber' => 2,
                                        'identifierString' => 'newAction1',
                                        'stringRepresentation' => 'represent',
                                        'typeNumber' => 5,
                                        'isCorrect' => true,
                                        'etalon' => '1111',
                                        'subActions' => array(
                                            array(
                                                'answer' => 'Answer2',
                                                'errorNumber' => 2,
                                                'identifierString' => 'newAction3',
                                                'stringRepresentation' => 'represent',
                                                'typeNumber' => 5,
                                                'isCorrect' => true,
                                                'etalon' => '1111',
                                            )
                                        ),
                                    )
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $help->Data(print_r($arr,true));
        $help->JSON(json_encode($arr));
        $help->_code();
        
        $help->action('Get Olympiad Levels');
        $help->code();
            $help->options('ac','getolymplevels',true);
            $help->options('authid','[AUTHID]',true);
        $help->_code();
        
        $help->action('Update Olympiad Levels');
        $help->code();
            $help->options('ac','updateolymplevels',true);
            $help->options('authid','[AUTHID]',true);
            $help->options('data','[JSON]',true);
            $arr = array(
                array(
                    'identifierString' => 'newlevelpath1',
                    'isAllTasksSolved' => true,
                    'lastChangeDate' => 'UNIXTIME',
                    'tasks' => array(
                        array(
                            'identifierNumber' => 'new_task1',
                            'currentScore' => 1,
                            'statusNumber' => 1,
                            'tryCounter' => 1,
                            'lastChangeDate' => 'UNIXTIME',
                            'actions' => array(
                                array(
                                    'identifierNumber' => 'newaction1',
                                    'isCorrect' => true,
                                    'hints' => array(
                                        array(
                                            'identifierNumber' => 'hint1',
                                            'userInput' => 'asd',
                                        )
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
        $help->Data(print_r($arr,true));
        $help->JSON(json_encode($arr));
        $help->_code();

        $help->section('Rating');
        
        $help->action('Get Rate');
        $help->code();
            $help->options('ac','getrate',true);
            $help->options('authid','[AUTHID]',true);
        $help->_code();
        
        echo $help;
    }
}
