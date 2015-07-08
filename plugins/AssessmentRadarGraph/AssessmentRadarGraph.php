<?php
class AssessmentRadarGraph extends PluginBase {

    protected $storage = 'DbStorage';    
    static protected $name = 'Assessment Graph';
    static protected $description = 'Add Radargraph for your assessment question groups';
    
    public function __construct(PluginManager $manager, $id) {
        parent::__construct($manager, $id);
        
        
        /**
         * Here you should handle subscribing to the events your plugin will handle
         */
        $this->subscribe('afterPluginLoad', 'helloWorld');
        $this->subscribe('afterSurveyComplete', 'afterSurveyComplete');
	$this->subscribe('beforeSurveySettings');
	$this->subscribe('newSurveySettings');
    }
    
    
    /*
     * Below are the actual methods that handle events
     */
    
    public function afterAdminMenuLoaded()
    {
        $event = $this->event;
        $menu = $event->get('menu', array());
        $menu['left'][]=array(
                'href' => "http://docs.limesurvey.org",
                'alt' => gT('LimeSurvey online manual'),
                'image' => 'showhelp.png'
            );
        
        $event->set('menu', $menu);
    }

    public function helloWorld() 
    {
	//if($this->isEnabled() == true) {
	// TODO how to retrieve survuy specific plugin settings?
        $event = $this->event;
        $count = (int) $this->get('count');
        if ($count === false) $count = 0;
        $count++;
        $this->pluginManager->getAPI()->setFlash("A graph will be generated for each response message : ". $this->get('message', 'Survey', $event->get('surveyId')) . $count);
        $this->set('count', $count);
	//}
    }
    
    
    /**
     * This event is fired by the administration panel to gather extra settings
     * available for a survey.
     * The plugin should return setting meta data.
     * @param PluginEvent $event
     */
    public function beforeSurveySettings()
    {
	// TODO Activation of graph for this enquete.
        $event = $this->event;
        $event->set("surveysettings.{$this->id}", array(
            'name' => get_class($this),
            'settings' => array(
		// Enable the graph for this enquete?
                'message' => array(
                    'type' => 'string',
                    'label' => 'My Radarplot message to show to users:',
                    'current' => $this->get('message', 'Survey', $event->get('survey'))),
		'activatedForThisSurvey' => array(
                    'type' => 'boolean',
                    'label' => 'Should the assessment result page show the group totals in a graph? :',
                    'current' => $this->get('activatedForThisSurvey', 'Survey', $event->get('survey'))
                )
            )
         ));
    }
    // Saves all plugin enquete specific settings to the database 
    public function newSurveySettings()
    {
        $event = $this->event;
        foreach ($event->get('settings') as $name => $value)
        {
            
            $this->set($name, $value, 'Survey', $event->get('survey'));
        }
    }

    public function afterSurveyComplete()
    {
	error_reporting(E_ALL);

	if($this->isEnabled() == true) {
	$surveyid = $this->event->get('surveyId');
	$assessments = doAssessment($surveyid, true);

	// EXAMPLE OUTPUT RETURNED
	// Array ( [total] => 0 [assessmentgroup] => Array ( [0] => 
	// Array ( [groupname] => Example 1 assessment [perc] => 0 [total] => 0 [max] => 10 [min] => 0 [message] => {PERC} )

	// FORMAT NEEDED BY OUR radarGraph.php
	// { "surveyid": "1258", "assessmentgroup": [ { "groupname": "Example 1", "title": "Example 1 assessment", "min": 0, "max": 10, "score": 5, "ref": 4 }, { "groupname": "Example 2", "title": "Example 2 assessment", "min": 0, "max": 10, "score": 3, "ref": 8 }, { "groupname": "Example 3", "title": "Example 3 assessment", "min": 0, "max": 10, "score": 7, "ref": 10 } ] }
	
	$renderGraphAssessmentData = (object) array("surveyid" => $surveyid, "assessmentgroup" => $assessments["assessmentgroup"]);
	$renderGraphAssessmentData = $this->checkAndCorrectGraphAssessmentData($renderGraphAssessmentData);

	$generated_graph_filename = generateRadarGraph($renderGraphAssessmentData);
	
	echo ("<div class='assessmentgraph'><img src='http://alexander.khleuven.be/pwo-vadvies/limesurvey-devop/LimeSurvey/tmp/upload/$generated_graph_filename' /></div>");
	}
    }

    private function checkAndCorrectGraphAssessmentData($data) 
    {
	$i = 0;
	for($i=0;$i<count($data->assessmentgroup);$i++) {
	    	$data->assessmentgroup[$i]->score = $data->assessmentgroup[$i]->perc;
		// TODO how to make sure that this reference data can be introduced by the research team?
		$data->assessmentgroup[$i]->ref = 4;
		$data->assessmentgroup[$i]->title = $data->assessmentgroup[$i]->groupname;
	}

	return $data;
    }
    public function isEnabled() {
	$setting = $this->get('activatedForThisSurvey', 'Survey', $this->event->get('surveyId'));
	return  $setting == 1 ;
    }
}

require_once('radarGraph.php');
