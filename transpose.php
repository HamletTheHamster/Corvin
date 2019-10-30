<?php

function transpose($columnArray) {

  while ($row = $columnArray) {
    $rowArray[] = $row[0];
  }

  return($rowArray);
}
?>
