<?php

$target_dir = "../documents/signed/";
$id = $_GET['id'];

$allowed = array("pdf" => "application/octet-stream");
$filename = $_FILES["file"]["name"];
$filetype = $_FILES["file"]["type"];
$filesize = $_FILES["file"]["size"];
$ext = pathinfo($filename, PATHINFO_EXTENSION);
if (!array_key_exists($ext, $allowed)){
    $json = [
        'Status' => 'SUCCESS',
        'Message' => 'Documento subido con éxito',
    ];
}
else{
    if (in_array($filetype, $allowed)) {
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $id . ".pdf")){
            $json = [
                'Status' => 'SUCCESS',
                'Message' => 'Documento subido con éxito',
            ];
        }   
        else{
            $json = [
                'Status' => 'ERROR',
                'Message' => 'No se pudo subir el documento',
            ];
        }
    } else {
        $json = [
            'Status' => 'ERROR',
            'Message' => 'Solo se admiten documentos PDF',
        ];
    }
}

echo json_encode($json);
?>
