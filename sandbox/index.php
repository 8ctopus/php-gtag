<?php

declare(strict_types=1);

$measurementId = 'G-8XQMZ2E6TH';

?><!DOCTYPE html>
<html>
<head>
<title>index page</title>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $measurementId ?>"></script>
<script>

window.dataLayer = window.dataLayer || [];

function gtag() {
  dataLayer.push(arguments);
}

gtag('js', new Date());
gtag('config', '<?= $measurementId ?>', {
  // GA automatically tracks page views by default
  send_page_view: false,
  // debug view instructions
  // https://support.google.com/analytics/answer/7201382?hl=en&utm_id=ad#zippy=%2Cgoogle-tag-gtagjs
  // link to debug view
  // https://analytics.google.com/analytics/web/#/a62992619p355170503/admin/debugview/overview
  // setting the parameter to false doesn't disable debug mode
  //debug_mode: true,
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('button#page-view').addEventListener('click', event => {
    gtag('event', 'page_view', {
      page_location: window.location.href,
      page_path: window.location.pathname,
      page_title: 'jjajaja',
      //debug_mode: true,
    });

    console.log('pageview');
  })

  document.querySelector('button#purchase').addEventListener('click', event => {
    gtag('event', 'purchase', {
      transaction_id: 'T111',
      value: 88.99,
      currency: 'USD',
      /*
      tax: 5.00,
      shipping: 7.00,
      items: [
        {
          item_id: 'SKU_123',
          item_name: 'Product Name',
          quantity: 1,
          price: 99.99
        }
      ]
      */
      //debug_mode: true,
    });

    console.log('purchase');
  })
});

/*
*/

</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
<div class="container">
  <h1> hello world </h1>
  <button id="page-view">page view</button>
  <button id="purchase">purchase</button>
</div>
</body>
</html>
