<?php
 // EXAMPLE TAKEN FROM http://pchart.sourceforge.net/documentation.php?topic=exemple8
 // Standard inclusions     


/**
*  Generates a graph in tmp folder based on assessment data and returns unique filename.
*
*  Example input: browse to plugins/RadarGraph/radarGraph.php?testimage=j
**/
function generateRadarGraph($assessments) {


	// TODO use YUI framework to load resources correctly 
        require_once('/var/www/pwo-vadvies/limesurvey/application/third_party/pchart/pchart/pChart.class');
	require_once('/var/www/pwo-vadvies/limesurvey/application/third_party/pchart/pchart/pData.class');
	require_once('/var/www/pwo-vadvies/limesurvey/application/third_party/pchart/pchart/pCache.class');

// Dataset definition   
 $DataSet = new pData;  

 $aAxisses = array();
 $aSerie1 = array();
 $aSerie2 = array();
 foreach ($assessments->assessmentgroup as $category) {
	$aAxisses[] = $category->title;
	$aSerie1[] = $category->score;
	$aSerie2[] = $category->ref;
 }

// $DataSet->AddPoint(array("Kennisverwerven","Gezondheidsbevordering","Permanente Evaluatie","Consultatie","Promotie"),"Label");
// $DataSet->AddPoint(array(rand(1,10),rand(1,10),rand(1,10),rand(1,10),rand(1,10)),"Serie1");
// $DataSet->AddPoint(array(rand(1,10),rand(1,10),rand(1,10),rand(1,10),rand(1,10)),"Serie2");

 $DataSet->AddPoint($aAxisses,"Label");
$DataSet->AddPoint($aSerie1,"Serie1");
$DataSet->AddPoint($aSerie2,"Serie2");

 $DataSet->AddSerie("Serie1");
 $DataSet->AddSerie("Serie2");
 $DataSet->SetAbsciseLabelSerie("Label");


 $DataSet->SetSerieName("Referentie","Serie1");
 $DataSet->SetSerieName("Uw competenties","Serie2");


// Initialise the graph
 $Test = new pChart(600,500);
 // TODO localization for fonts needed
 $Test->setFontProperties("/var/www/pwo-vadvies/limesurvey-devop/LimeSurvey/fonts/DejaVuSans.ttf",8);  
 $Test->setGraphArea(80,30,450,450);  
  
 // Draw the radar graph  
 $Test->drawRadarAxis($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,20,120,120,120,230,230,230);  
 $Test->drawFilledRadar($DataSet->GetData(),$DataSet->GetDataDescription(),50,20);  
  
 // Finish the graph  
 $Test->drawLegend(15,15,$DataSet->GetDataDescription(),255,255,255);  
 $Test->setFontProperties("Fonts/tahoma.ttf",10);  
 $Test->drawTitle(0,22,"Example 8",50,50,50,400);  
 
  // Save to tmp folder to embed in html and pdf report
  $sRenderedFilename = $assessments->surveyid . "-" . uniqid() . ".png";

  // TODO localization of tmp folder
  $Test->Render("/var/www/pwo-vadvies/limesurvey-devop/LimeSurvey/tmp/upload/".$sRenderedFilename);

  return $sRenderedFilename;
}

    if(isset($_GET["testimage"])) {
	// Sample survey with 3 assessment groups
    	$t_json = '{ "surveyid": "1258", "assessmentgroup": [ { "groupname": "Example 1", "title": "Example 1 assessment", "min": 0, "max": 10, "score": 5, "ref": '.rand(1,10).' }, { "groupname": "Example 2", "title": "Example 2 assessment", "min": 0, "max": 10, "score": 3, "ref": '.rand(1,10).' }, { "groupname": "Example 3", "title": "Example 3 assessment", "min": 0, "max": 10, "score": 7, "ref": '.rand(1,10).' } ] }';
	
	echo "Example input: ".$t_json . "<br />";
	
    	$demo_assessment = json_decode($t_json);

    	echo "Filename: <a href='http://alexander.khleuven.be/pwo-vadvies/limesurvey-devop/LimeSurvey/tmp/upload/".generateRadarGraph($demo_assessment)."'>Link to file</a>";
    } else { /*echo ""; // TESTING PURPOSES */ } 
?>
