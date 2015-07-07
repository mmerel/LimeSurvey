<?php
 // EXAMPLE TAKEN FROM http://pchart.sourceforge.net/documentation.php?topic=exemple8
 // Standard inclusions     


function generateRadarGraph($assessments) {
 
        require_once('/var/www/pwo-vadvies/limesurvey/application/third_party/pchart/pchart/pChart.class');
	require_once('/var/www/pwo-vadvies/limesurvey/application/third_party/pchart/pchart/pData.class');
	require_once('/var/www/pwo-vadvies/limesurvey/application/third_party/pchart/pchart/pCache.class');

// TODO somewhere else!
//$content = "<h2>Summary Radar graph</h2>";

// Dataset definition   
 $DataSet = new pData;  
 $DataSet->AddPoint(array("Kennisverwerven","Gezondheidsbevordering","Permanente Evaluatie","Consultatie","Promotie", "Praktijkchecklist"),"Label");  
 $DataSet->AddPoint(array(rand(1,10),rand(1,10),rand(1,10),rand(1,10),rand(1,10),rand(1,10)),"Serie1");  
 $DataSet->AddPoint(array(rand(1,10),rand(1,10),rand(1,10),rand(1,10),rand(1,10),rand(1,10)),"Serie2");  
 $DataSet->AddSerie("Serie1");  
 $DataSet->AddSerie("Serie2");  
 $DataSet->SetAbsciseLabelSerie("Label");  
  
  
 $DataSet->SetSerieName("Referentie","Serie1");  
 $DataSet->SetSerieName("Uw competenties","Serie2");  
  
 // Initialise the graph  
 $Test = new pChart(600,500);  
 $Test->setFontProperties("/var/www/pwo-vadvies/limesurvey/fonts/DejaVuSans.ttf",8);  
 //$Test->drawFilledRoundedRectangle(7,7,393,393,5,240,240,240); 
 //$Test->drawRoundedRectangle(5,5,395,395,5,230,230,230);  
 $Test->setGraphArea(80,30,450,450);  
 //$Test->drawFilledRoundedRectangle(30,30,370,370,5,255,255,255);  
 //$Test->drawRoundedRectangle(30,30,370,370,5,220,220,220);  
  
 // Draw the radar graph  
 $Test->drawRadarAxis($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,20,120,120,120,230,230,230);  
 $Test->drawFilledRadar($DataSet->GetData(),$DataSet->GetDataDescription(),50,20);  
  
 // Finish the graph  
 $Test->drawLegend(15,15,$DataSet->GetDataDescription(),255,255,255);  
 $Test->setFontProperties("Fonts/tahoma.ttf",10);  
 $Test->drawTitle(0,22,"Example 8",50,50,50,400);  
// echo "ok";
// FAILS $Test->Render("example8.png");
// echo "<img src='example8.png' />";
 $Test->Stroke();
}


    generateRadarGraph(null);
?>
