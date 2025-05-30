# cookies

[Google's reference article](https://support.google.com/analytics/answer/11397207?hl=en)

## user cookie

Used to store user information

### name

    _ga

### value

GA1.1.<id>.<timestamp>
`GA1.1.1827526090.1689745728`

- `id` is `\d{6,10}`
- `timestamp` is `\d{10}`

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

_ga_<measurement-id>
`_ga_8XQMZ2E6TH`

where `measurement-id` is found inside your google analytics settings and consists of `[A-Z0-9]{10}`


### value

GS<version_number>.<session_id>.<session_number>.<session_engaged>.<last_activity>.<?>.<?>.<?>
`GS1.1.1689765380.3.1.1689766550.0.0.0`

- `version_number` is `1.1`
- `session_id`  is the `timestamp` when the session started
- `session_number`
- `session_engaged` is mostly `1` but can also be `0`, no other values found
- `last_activity` - timestamp
- the last 3 values are always zero

_NOTE_: may not always be GA1.1 https://stackoverflow.com/a/16107194/10126479

#### cookie on first day

    GS1.1.1689765380.3.1.1689766550.0.0.0

#### cookie on next day on first attempt

    GS1.1.1689828668.4.0.1689828668.0.0.0

#### cookie on next day on second attempt

    GS1.1.1689828668.4.1.1689829000.0.0.0

#### cookie later on

    GS1.1.1689832664.5.1.1689832969.0.0.0

### new version

As of May 2025, there is a new cookie value that looks like this:

`GS2.1.s1747027167$o11$g1$t1747027167$j0$l0$h0`

So the cookie name is unchanged, however the value is significantly different with the `$` as new separator as well as a new `2.1` version number.

#### value format

GS<version_number>.s<session_id>$o<session_number>$g<session_engaged>$t<last_activity>$j<j>$l<l>$h<h>
`GS2.1.s1747027167$o11$g1$t1747027167$j0$l0$h0`

- `version_number` is `2.1`
- `session_id`  is the `timestamp` when the session started
- `session_number` ? - most sample values had value 1, then 2, but also found one with 41
- `session_engaged` is mostly one but can also be zero, no other values found
- `last_activity` ? - it's a timestamp
- `j` ? - was zero until the last few days, now it's two digits
- `l` and `h` are always zero, like in the previous format

