# cookies

[Google's reference article](https://support.google.com/analytics/answer/11397207?hl=en)

## user cookie

Used to store user information

### name

    _ga

### value

GA1.1.<id>.<timestamp>
GA1.1.\d{6,10}.\d{10}

`GA1.1.1827526090.1689745728`

### new version

As of May 2025, there's a new version of the user cookie which looks like this:

`GA1.2.1987826055.1739862817`

So it looks unchanged, except for a new version number.

## session cookie

Used to store session state

> In Analytics, a session initiates when a user either opens your app in the foreground or views a page or screen and no session is currently active (e.g. their previous session has timed out). By default, a session ends (times out) after 30 minutes of user inactivity. There is no limit to how long a session can last.

> When a session starts, Google automatically collects a session_start event and generates a session ID (`session_id`) and session number (`session_number`) via the session_start event. Session ID is a timestamp of when a session began. Session number identifies the number of sessions that a user has started up to the current session (e.g., a user's third or fifth session on your site).

[reference](https://support.google.com/analytics/answer/9191807)

### name

_ga_<container-id>

where `container-id` is `[A-Z0-9]{10}`

`_ga_8XQMZ2E6TH`

### value

_NOTE_: may not always be GA1.1 https://stackoverflow.com/a/16107194/10126479

GS1.1.<session_id(timestamp)>.<session_number>.<session_engaged>.<last_activity>.<?>.<?>.<?>

#### cookie on first day

    GS1.1.1689765380.3.1.1689766550.0.0.0

#### cookie on next day on first attempt

    GS1.1.1689828668.4.0.1689828668.0.0.0

#### cookie on next day on second attempt

    GS1.1.1689828668.4.1.1689829000.0.0.0

#### cookie later on

    GS1.1.1689832664.5.1.1689832969.0.0.0

### new version

As of May 2025, there is a new cookie that looks like this

`_ga_8P9R6GWMP3` => `GS2.1.s1747027167$o11$g1$t1747027167$j0$l0$h0`

So the name appears unchanged, however the cookie is significantly different with the `$` as new separator as well as a new version number

#### value format

GS2.1.s<session_id,timestamp>$o<?>$g<?>$t<?,timestamp>$j<?>$l<?>$h<?>
