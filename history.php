<?php
function display_data($data) {
    $output = "<table>";
    foreach($data as $key => $var) {
        //$output .= '<tr>';
        if($key===0) {
            $output .= '<tr>';
            foreach($var as $col => $val) {
                $output .= "<td>" . $col . '</td>';
            }
            $output .= '</tr>';
            foreach($var as $col => $val) {
                $output .= '<td>' . $val . '</td>';
            }
            $output .= '</tr>';
        }
        else {
            $output .= '<tr>';
            foreach($var as $col => $val) {
                $output .= '<td>' . $val . '</td>';
            }
            $output .= '</tr>';
        }
    }
    $output .= '</table>';
    echo $output;
}
?>

<html>
<head>
<meta charset="utf-8">
</head>
<body>

<?php if($results) {display_data($results);}?>

</html>
