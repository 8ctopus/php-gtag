# always present
v: version number - universal analytics is 1 - google analytics 4 is 2 - (int) - 2
gtm: does not change even on a different site - could be script version or something with google tag manager - (string) - 45je37c0
tid: tracking id (string) - G-8XQMZ2E6TH
cid: client id - content of _ga cookie without GA1.1. (string) - 948747482.1689681163 - random(10).timestamp
sid: session id - timestamp - argument 3 of _ga_8XQMZ2E6TH cookie (string) - 1689681163
_p: shared among events sent at the same time (int) - Math.floor(Math.random() * (2147483647 - 0 + 1) + 0) - 2147483647 = 0x7FFFFFFF
_s: event number within _p collection (int) - starts at 1 [1-3]
sct: session number (int) - 1
seg: session engaged (bool) - 0/1

# debug - not always present
ep.debug_mode: debug mode (bool)

# event - always present
en: event (string) - page_view

# page - always present
dl: page location (string) - https://test.com/gtag-index.php
dt: page title (string) - My First Web Page
dr: page referrer (string) - https://test.com/

# user - always present
ul: user language (string) - en-us
sr: screen resolution (string) - 1920x1080
uafvl: user agent full version list (string) - Not.A%2FBrand;8.0.0.0|Chromium;114.0.5735.201|Microsoft%20Edge;114.0.1823.82
uaa: user agent architecture (string) - x86
uab: user agent bitness (string) - 64
uap: user agent platform (string) - Windows
uapv: user agent version (string) - 10.0.0
uaw: user agent wow64 (bool) - 0/1
uam: user agent model (string) - empty string
uamb: user agent mobile (bool) - 0/1

# always present
ngs: does not change (bool/int) - 1

# not always present:
_et: engagement time (int) - 7
_ss: session start (bool) - 1
_fv: first visit (bool) - 1
_ee: external event (bool) - 1
_nsi: is new to site (bool) - 1
