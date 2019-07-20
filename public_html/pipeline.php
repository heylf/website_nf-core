<?php

$title = '<a href="/'.$pipeline->name.'">nf-core/<br class="d-sm-none">'.$pipeline->name.'</a>';
$subtitle = $pipeline->description;

# Details for parsing markdown readme, fetched from Github
# Build the remote file path
if(count($path_parts) > 1){
    if(substr($_SERVER['REQUEST_URI'], -3) == '.md'){
        # Clean up URL by removing .md
        header('Location: '.substr($_SERVER['REQUEST_URI'], 0, -3));
        exit;
    }
    $markdown_fn = 'https://raw.githubusercontent.com/'.$pipeline->full_name.'/master/'.implode('/', array_slice($path_parts, 1)).'.md';
} else {
    $markdown_fn = 'https://raw.githubusercontent.com/'.$pipeline->full_name.'/master/README.md';
    $md_trim_before = '# Introduction';
}
$src_url_prepend = 'https://raw.githubusercontent.com/'.$pipeline->full_name.'/master/'.implode('/', array_slice($path_parts, 1, -1)).'/';
$href_url_prepend = $pipeline->name.'/'.implode('/', array_slice($path_parts, 1, -1)).'/';
$md_content_replace = array(
    array('# nf-core/'.$pipeline->name.': '),
    array('# ')
);
$html_content_replace = array(
    array('<table>'),
    array('<table class="table">')
);

# Header - keywords
$header_html = '<p>';
foreach($pipeline->topics as $keyword){
  $header_html .= '<a href="/pipelines?q='.$keyword.'" class="badge font-weight-light btn btn-sm btn-outline-light">'.$keyword.'</a> ';
}
$header_html .= '</p>';

# Header - GitHub link
$header_html .= '<p class="mb-0"><a href="'.$pipeline->html_url.'" class="text-light"><i class="fab fa-github"></i> '.$pipeline->html_url.'</a></p>';

# Main page nav and header
$no_print_content = true;
$mainpage_container = false;
include('../includes/header.php');

# Pipeline subheader

# Get details for the button to the latest release
function rsort_releases($a, $b){
    $t1 = strtotime($a->published_at);
    $t2 = strtotime($b->published_at);
    return $t2 - $t1;
}
if(count($pipeline->releases) > 0){
    usort($pipeline->releases, 'rsort_releases');
    $download_bn = '<a href="'.$pipeline->releases[0]->html_url.'" class="btn btn-success btn-lg">Get version '.$pipeline->releases[0]->tag_name.'</a>';
} else {
    $download_bn = 'fubar';
}

# Extra HTML for the header - tags and GitHub URL
?>

<div class="mainpage-subheader-heading">
  <div class="container text-center">
    <p><?php echo $download_bn; ?></p>
    <div class="btn-group">
      <?php
      $pipeline_docs_links = array(
        'Readme' => '',
        'Usage' => '/docs/usage',
        'Output' => '/docs/output'
      );
      foreach($pipeline_docs_links as $txt => $url){
        if($_SERVER['REQUEST_URI'] == '/'.$pipeline->name.$url){
          echo '<a href="/'.$pipeline->name.$url.'" class="btn btn-secondary">'.$txt.'</a>';
        } else {
          echo '<a href="/'.$pipeline->name.$url.'" class="btn btn-outline-secondary">'.$txt.'</a>';
        }
      }
      ?>
    </div>
  </div>
</div>
<div class="triangle subheader-triangle-down"></div>

<div class="container main-content">

<?php

# Print rendered markdown made in header.php
echo '<div class="rendered-markdown">';
echo $content;
echo '</div>';

# echo '<pre>'.print_r($pipeline, true).'</pre>';

include('../includes/footer.php');
