<?php

$in = fopen("expressions.txt","r") or die("Unable to open file.");

echo <<< EOT
<html>
  <head>
    <title>Expression Evaluator</title>
  </head>
  <body>
EOT;

echo "\r\n";

$inputLine = fgets($in);
$inputLine = trim($inputLine);
$indent = "    ";

while(!feof($in)){

  $inputLine = str_replace(" ","",$inputLine);
  echo $indent.$inputLine." <br/>"."\r\n";
  $e = new ExpressionString($inputLine);
  try{
    $val = evalExpr($e,$indent);
    if($e->getIndex()!=strlen($inputLine))
      throw new Exception();
    echo "<br/>Result: ".$val;
  }
  catch(Exception $ex) {
    if($e->getIndex() >= strlen($inputLine))
      echo "<br/>Invalid Expression, unexpected end of line";
    else
      echo "<br/>Invalid Expression, unexpected character at index ".$e->getIndex();
  }
  echo " <br/><br/>"."\r\n";
  $inputLine = fgets($in);
  $inputLine = trim($inputLine);
}
  fclose('expression.txt');
echo <<< EOT
  </body>
</html>
EOT;


function evalDigit($e, $indent){
  if($e->getCurrentChar()>='0' && $e->getCurrentChar()<='9'){
    echo $indent . $e->getCurrentChar()."\r\n";
    $result = $e->getCurrentChar();
    $e->advance();
    return $result;
  }else{
    throw new Exception("Exception Thrown");
  }
}

function evalTerm($e, $indent){

  $result;

  if($e->getCurrentChar()=='('){
    echo $indent.'('."\r\n";
    $e->advance();
    $result = evalExpr($e,$indent."  ");
    if($e->getCurrentChar()!=')'){
      throw new Exception();
    }
    echo $indent.')'."\r\n";
    $e->advance();
  }else{
    $result = evalDigit($e,$indent);
  }
  return $result;
}

function evalExpr($e,$indent){

  $result = evalTerm($e,$indent);

  while($e->getCurrentChar()=='+' || $e->getCurrentChar()=='-'){
    echo $indent.$e->getCurrentChar()."\r\n";
    switch($e->getCurrentChar()){
      case '+':
        $e->advance();
        $result += evalTerm($e,$indent);
        break;
      case '-':
        $e->advance();
        $result -= evalTerm($e,$indent);
        break;
    }
  }

  return $result;
}

class ExpressionString{

  private $e;
  private $i;
  public $currentChar;

  public function __construct($s){
    $this->e = str_split($s);
    $this->reset();
  }

  public function reset(){
    $this->i=-1;
    $this->advance();
  }

  public function advance(){
    if($this->i<count($this->e)){
      $this->i++;
    }
    if($this->i<count($this->e)){
      $this->currentChar=$this->e[$this->i];
    }else{
      $this->currentChar='.';
    }
  }

  public function getCurrentChar(){
    return $this->currentChar;
  }

  public function getIndex(){
    return $this->i;
  }
}

?>
