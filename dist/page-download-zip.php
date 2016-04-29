<?php
/*
Template Name: Download ZIP
*/

function slugify($text)
{ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text))
  {
    return 'n-a';
  }

  return $text;
}




$id = $_POST['id'];
if(!$id || $id=='') 
	exit;


$documents_id = json_decode(get_post_meta($id, 'oz_documents',true));
is_array($documents_id) ? natsort($documents_id) : '' ;

$file = tempnam(ABSPATH."tmp", "zip");
$zip = new ZipArchive();
$zip->open($file, ZipArchive::OVERWRITE);


foreach($documents_id as $doc_id) {
	$djvu_file = get_attached_file(get_post_meta($doc_id, 'oz_djvu', true));
	$djvu_name = explode(DIRECTORY_SEPARATOR, $djvu_file);
	$djvu_name = 'djvu'.DIRECTORY_SEPARATOR.$doc_id.'_'.$djvu_name[sizeof($djvu_name)-1];
	//echo $djvu_file.'    '.$djvu_name;

	$pdf_file = get_attached_file(get_post_meta($doc_id, 'oz_pdf', true));
	$pdf_name = explode(DIRECTORY_SEPARATOR, $pdf_file);
	$pdf_name = 'pdf'.DIRECTORY_SEPARATOR.$doc_id.'_'.$pdf_name[sizeof($pdf_name)-1];

	$zip->addFile($djvu_file, $djvu_name);
	$zip->addFile($pdf_file, $pdf_name);

}
$zip->close();
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="'.slugify(get_the_title($id)).'"');
ob_clean();
flush();

readfile($file);
//unlink($file); 
?>