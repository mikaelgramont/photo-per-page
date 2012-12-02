This tiny plugin lets you associate a photo with a given page.
Example of use: displaying a custom banner in the header for each page.

<?php
if (function_exists('photo_per_page')) {
  echo photo_per_page($post);
}
?>