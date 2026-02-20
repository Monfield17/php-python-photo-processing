<?php 

require_once __DIR__ . '/src/Ai/PhotoAiRunner.php'; 
use App\Ai\PhotoAiRunner; 
$result = PhotoAiRunner::analyze('/absolutni/cesta/k/fotce.jpg'); 

print_r($result);